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
class PH2M_Gdpr_Model_System_Config_Source_Orderattributes
{

    protected $_options;

    /**
     * @return mixed
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            /**
             * Get the table name
             */
            $tableName = $resource->getTableName('sales/order');

            $fields = array_keys($readConnection->describeTable($tableName));

            sort($fields);
            foreach ($fields as $field) {
                if ($field) {
                    $this->_options[] = [
                        'label' => $field,
                        'value' => $field
                    ];
                }
            }
        }
        return $this->_options;
    }
}
