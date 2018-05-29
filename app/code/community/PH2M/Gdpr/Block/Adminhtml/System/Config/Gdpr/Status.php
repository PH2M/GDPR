<?php
/**
 * PH2_Gdpr
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

class PH2M_Gdpr_Block_Adminhtml_System_Config_Gdpr_Status extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = Mage::getBlockSingleton('phgdpr/adminhtml_gdpr_status')->toHtml();

        return $html;
    }
}
