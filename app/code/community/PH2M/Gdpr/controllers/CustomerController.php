<?php
/**
 * PH2M_Gdpr
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gdpr
 * @copyright  Copyright (c) 2018 PH2M SARL
 * @author     PH2M SARL <contact@ph2m.com> : http://www.ph2m.com/
 *
 */


class PH2M_Gdpr_CustomerController extends Mage_Core_Controller_Front_Action
{
    public function deleteConfirmationAction()
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_remove/enable')) {
            return false;
        }

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return false;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function downloadViewAction()
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_download/enable')) {
            return false;
        }

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return false;
        }

        $this->loadLayout();
        $this->renderLayout();
    }


    /**
     * Download customer file data
     *
     * @return mixed
     */
    public function downloadFileAction()
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_download/enable')) {
            return false;
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer) {
            return $this->_redirect('customer/account/login');
        }

        $file = Mage::helper('phgdpr')->getCustomerDataFile($customer->getId());

        if (file_exists($file)) {
            $data = file_get_contents($file);
            return $this->_prepareDownloadResponse('customer-information.txt', $data);
        }
        Mage::getSingleton('core/session')->addError(Mage::helper('phgdpr')->__('Sorry, but this file does not exist'));
        return $this->_redirectReferer();
    }

    /**
     * Send request for delete account
     *
     * @return mixed
     */
    public function deleteAccountAction()
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_remove/enable')) {
            return false;
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer) {
            return $this->_redirect('customer/account/login');
        }
        if (Mage::getStoreConfig('phgdpr/customer_data_remove/enable_password_confirmation_for_delete')) {
            $passwordConfirmation   = $this->getRequest()->getParam('password');

            if (!$customer->validatePassword($passwordConfirmation)) {
                Mage::getSingleton('core/session')->addError(Mage::helper('phgdpr')->__('Invalid password'));
                return $this->_redirectReferer();
            }
        }

        Mage::getModel('phgdpr/customer_data_remove')->requestDeleteCustomerData($customer);

        $session = Mage::getSingleton('customer/session');
        $session->logout();
        $session->renewSession();
        if (!Mage::getStoreConfig('phgdpr/customer_data_remove/remove_action_in_queue')) {
            $session->addSuccess(Mage::getStoreConfig(('phgdpr/customer_data_remove/account_delete_success_message')));
        } else {
            $session->addSuccess(Mage::getStoreConfig(('phgdpr/customer_data_remove/account_delete_queue_pending_message')));
        }
        return $this->_redirectReferer();
    }


    /**
     * Send request for download customer data
     *
     * @return Mage_Adminhtml_Controller_Action|Mage_Core_Controller_Varien_Action|void
     */
    public function downloadCustomerDataAction()
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_download/enable')) {
            return false;
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer) {
            $this->_redirect('customer/account/login');
            return;
        }

        $data = Mage::getModel('phgdpr/customer_data_download')->requestRetrieveCustomerData($customer);

        if (!Mage::getStoreConfig('phgdpr/customer_data_download/download_action_in_queue')) {
            $this->_prepareDownloadResponse('customer-information.txt', $data);
        } else {
            Mage::getSingleton('core/session')->addSuccess(Mage::getStoreConfig('phgdpr/customer_data_download/queue_processing_message'));
            $this->_redirectReferer();
        }
    }
}
