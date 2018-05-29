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


class PH2M_Gdpr_Block_Account_Download extends Mage_Core_Block_Template
{

    /**
     * Return true, if file data for current customer is find
     *
     * @return bool
     */
    public function getCustomerDataFile()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer) {
            return $this->_redirect('customer/account/login');
        }

        $file = Mage::helper('phgdpr')->getCustomerDataFile($customer->getId());
        if (file_exists($file)) {
            return true;
        }
        return false;
    }
}
