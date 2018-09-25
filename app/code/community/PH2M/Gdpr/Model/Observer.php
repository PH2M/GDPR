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
class PH2M_Gdpr_Model_Observer
{
    protected $configModel;
    const EXCEPTION_ACCOUNT_GDPR_LOCK = 20;
    const DEFAULT_ATTEMPS_NUMBER = 5;
    const DEFAULT_TIME_BLOCKED = 5;


    /**
     * Check if all config for respect GDPR is enabled
     */
    public function checkRulesValidity()
    {
        $this->configModel = Mage::getConfig();
        $this->checkNewsletterDoubleOptIn();
        $this->checkPasswordFormatValidation();
        $this->checkLoginLimitAttempts();
        $this->checkCustomerCanRemoveData();
        $this->checkCustomerCanDownloadData();
    }

    protected function checkCustomerCanRemoveData()
    {
        if (Mage::getStoreConfig('phgdpr/customer_data_remove/enable')) {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_data_remove', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::WAIT_MANUAL_VALIDATION, 'default', 0);
        } else {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_data_remove', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID, 'default', 0);
        }
    }

    protected function checkCustomerCanDownloadData()
    {
        if (Mage::getStoreConfig('phgdpr/customer_data_download/enable')) {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_download_own_information', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::WAIT_MANUAL_VALIDATION, 'default', 0);
        } else {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_download_own_information', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID, 'default', 0);
        }
    }

    protected function checkNewsletterDoubleOptIn()
    {
        if (Mage::getStoreConfig('newsletter/subscription/confirm')) {
            $this->configModel->saveConfig('phgdpr/valid_rules/newsletter_double_optin', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::WAIT_MANUAL_VALIDATION, 'default', 0);
        } else {
            $this->configModel->saveConfig('phgdpr/valid_rules/newsletter_double_optin', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID, 'default', 0);
        }
    }

    protected function checkPasswordFormatValidation()
    {
        if (Mage::getStoreConfig('phgdpr/fonctionality/password_format_validation')) {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_complex_password', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::WAIT_MANUAL_VALIDATION, 'default', 0);
        } else {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_complex_password', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID, 'default', 0);
        }
    }

    protected function checkLoginLimitAttempts()
    {
        if (Mage::getStoreConfig('phgdpr/fonctionality/login_limit_attempts')) {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_login_limit_attempts', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::WAIT_MANUAL_VALIDATION, 'default', 0);
        } else {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_login_limit_attempts', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID, 'default', 0);
        }
    }


    /**
     * If customer try to login too many time during 30 seconds,
     * lock the login system during 30 seconds
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    public function limitLoginAttempts(Varien_Event_Observer $observer)
    {

        if (Mage::getStoreConfig('phgdpr/fonctionality/login_limit_attempts') && !$this->verifyAttempts()) {
            exit($observer->getEvent()->getControllerAction()->getResponse()->setRedirect(Mage::helper('core/http')->getHttpReferer()));
        }

        return true;
    }

    /**
     * Verify user connection attempts to limit request
     *
     * @return bool
     */
    protected function verifyAttempts()
    {

        if (!$numberAttempts = Mage::getStoreConfig('phgdpr/fonctionality/customer_login_attempts_number')) {
            $numberAttempts = self::DEFAULT_ATTEMPS_NUMBER;
        }

        if (!$timeBlocked = intval(Mage::getStoreConfig('phgdpr/fonctionality/customer_login_time_blocked'))*60) {
            $timeBlocked = self::DEFAULT_TIME_BLOCKED*60;
        }

        $cookie = Mage::getSingleton('core/cookie');

        $cookieLoginAttempts = $cookie->get('login_attempts');
        if ($cookieLoginAttempts) {
            if ($cookieLoginAttempts >= $numberAttempts) {
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('core')->__('You tried to log in too many times, you can try again after %s minutes', ($timeBlocked/60))
                );
            }
            $cookieLoginAttempts++;
            $cookie->set('login_attempts', $cookieLoginAttempts, $timeBlocked, '/');
            return false;
        }
        else {
            $cookie->set('login_attempts', 1, $timeBlocked, '/');
        }

        return true;
    }


    /**
     * Queue runner, execute run function for entity type
     *
     * @return bool
     */
    public function runQueueRunner()
    {
        /** @var PH2M_Gdpr_Model_Queue $entities */
        $entities = Mage::getModel('phgdpr/queue')->getEntitiesToRun();
        if (!$entities) {
            return false;
        }
        foreach ($entities as $entity) {
            $action = Mage::getModel($entity->getEntityType());
            if ($action) {
                try {
                    $action->run($entity->getParams());
                    $entity->delete();
                } catch (Exception $e) {
                    Mage::logException($e);
                    return false;
                }
            }
        }

        return true;
    }


    public function checkIfAccountIsLock(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getModel();
        if ($customer && $customer->getIsGdprLock()) {
            throw Mage::exception('Mage_Core', Mage::getStoreConfig('phgdpr/customer_data_remove/lock_account_message'), self::EXCEPTION_ACCOUNT_GDPR_LOCK);
        }
    }
}
