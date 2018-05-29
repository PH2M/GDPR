<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 *
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
class PH2M_Gdpr_Model_System_Config_Source_Rulesvalidity
{

    const NO_VALID = 0;
    const VALID = 1;
    const WAIT_MANUAL_VALIDATION = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::NO_VALID, 'label'=>Mage::helper('adminhtml')->__('No valid')),
            array('value' => self::VALID, 'label'=>Mage::helper('adminhtml')->__('Valid')),
            array('value' => self::WAIT_MANUAL_VALIDATION, 'label'=>Mage::helper('adminhtml')->__('Wait manual validation')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            self::NO_VALID => Mage::helper('adminhtml')->__('No valid'),
            self::VALID => Mage::helper('adminhtml')->__('Valid'),
            self::WAIT_MANUAL_VALIDATION => Mage::helper('adminhtml')->__('Wait manual validation'),
        );
    }
}
