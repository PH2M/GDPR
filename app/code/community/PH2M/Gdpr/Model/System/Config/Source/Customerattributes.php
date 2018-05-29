<?php
/**
 * PH2M_PhGdrp
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   PhGdrp
 * @copyright  Copyright (c) 2018 PH2M SARL
 * @author     PH2M SARL <contact@ph2m.com> : http://www.ph2m.com/
 *
 */
class PH2M_Gdpr_Model_System_Config_Source_Customerattributes
{

    protected $_options;

    /**
     * @return mixed
     */
    public function toOptionArray()
    {

        if (!$this->_options) {
            $attributes = Mage::getModel('customer/customer')->getAttributes();

            foreach ($attributes as $attribute) {
                if ($attribute->getAttributeCode() == 'default_shipping' || $attribute->getAttributeCode() == 'default_billing') {
                    continue;
                }
                if ($label = $attribute->getFrontendLabel()) {
                    $this->_options[] = [
                        'label' => $label,
                        'value' => $attribute->getAttributeCode()
                    ];
                }
            }
        }
        return $this->_options;
    }
}
