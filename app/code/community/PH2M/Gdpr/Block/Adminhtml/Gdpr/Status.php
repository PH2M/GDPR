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

class PH2M_Gdpr_Block_Adminhtml_Gdpr_Status extends Mage_Core_Block_Template
{
    /**
     * Constructor. Set template.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ph/gdpr/system/config/status.phtml');
    }


    /**
     * Return status css class
     *
     * @param $config
     * @return string
     */
    public function getCssClass($config)
    {
        $config = Mage::getStoreConfig($config);
        if ($config == PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::NO_VALID) {
            return 'bad';
        } elseif ($config == PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity::WAIT_MANUAL_VALIDATION) {
            return 'wait';
        } else {
            return 'good';
        }
    }
}
