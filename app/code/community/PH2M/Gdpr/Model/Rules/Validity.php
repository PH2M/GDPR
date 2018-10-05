<?php
/**
 * PH2M_GDPR
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   GDPR
 * @copyright  Copyright (c) 2018 PH2M SARL
 * @author     PH2M SARL <contact@ph2m.com> : http://www.ph2m.com/
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class PH2M_Gdpr_Model_Rules_Validity extends Varien_Object
{
    protected $configModel;

    /**
     * Check if all config for respect GDPR is enabled
     * @event admin_system_config_changed_section_newsletter
     * @event
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
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_data_remove', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::VALID, 'default', 0);
        } else {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_data_remove', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID, 'default', 0);
        }
    }

    protected function checkCustomerCanDownloadData()
    {
        if (Mage::getStoreConfig('phgdpr/customer_data_download/enable')) {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_download_own_information', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::VALID, 'default', 0);
        } else {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_download_own_information', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID, 'default', 0);
        }
    }

    protected function checkNewsletterDoubleOptIn()
    {
        if (Mage::getStoreConfig('newsletter/subscription/confirm')) {
            $this->configModel->saveConfig('phgdpr/valid_rules/newsletter_double_optin', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::VALID, 'default', 0);
        } else {
            $this->configModel->saveConfig('phgdpr/valid_rules/newsletter_double_optin', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID, 'default', 0);
        }
    }

    protected function checkPasswordFormatValidation()
    {
        if (Mage::getStoreConfig('phgdpr/fonctionality/password_format_validation')) {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_complex_password', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::VALID, 'default', 0);
        } else {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_complex_password', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID, 'default', 0);
        }
    }

    protected function checkLoginLimitAttempts()
    {
        if (Mage::getStoreConfig('phgdpr/fonctionality/login_limit_attempts')) {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_login_limit_attempts', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::VALID, 'default', 0);
        } else {
            $this->configModel->saveConfig('phgdpr/valid_rules/customer_login_limit_attempts', PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID, 'default', 0);
        }
    }
}
