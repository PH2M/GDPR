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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class PH2M_Gdpr_Model_Resource_Setup_Customer extends Mage_Customer_Model_Resource_Setup
{


    /**
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = [
            'customer' => [
                'entity_model' => 'customer/customer',
                'attribute_model' => 'customer/attribute',
                'table' => 'customer/entity',
                'increment_model' => 'eav/entity_increment_numeric',
                'additional_attribute_table' => 'customer/eav_attribute',
                'entity_attribute_collection' => 'customer/attribute_collection',
                'attributes' => [
                    'is_gdpr_lock' => [
                        'type' => 'int',
                        'label' => 'Is GDPR lock',
                        'input' => 'select',
                        'source' => 'eav/entity_attribute_source_boolean',
                        'required' => false,
                        'sort_order' => 120,
                        'is_visible' => 0,
                        'position' => 120
                    ]
                ]
            ]
        ];
        return $entities;
    }
}
