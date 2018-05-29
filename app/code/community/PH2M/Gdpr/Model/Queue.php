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

class PH2M_Gdpr_Model_Queue extends Mage_Core_Model_Abstract
{

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('phgdpr/queue');
    }


    /**
     * Add unique customer id in queue
     *
     * @param string $entityType
     * @param string $params
     * @param string | null $runDate
     * @return bool
     */
    public function addEntity($entityType, $params, $runDate = null)
    {
        if ($this->isDuplicateEntry($entityType, $params)) {
            return false;
        }

        if (!$runDate) {
            $runDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
        }

        try {
            $this->setEntityType($entityType)
                ->setParams($params)
                ->setRunDate($runDate)
                ->save();
            return true;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

    /**
     * Detect if customer already exist in queue
     *
     * @param $entityType
     * @param $params
     * @return bool
     */
    public function isDuplicateEntry($entityType, $params)
    {
        if (!$collection = $this->getQueuesForEntityType($entityType)) {
            return false;
        }

        $collection->addFieldToFilter('params', $params);

        if ($collection->getSize() > 0) {
            return true;
        }
        return false;
    }


    /**
     * @param $entityType
     * @return bool|Mage_Eav_Model_Entity_Collection_Abstract|PH2M_Gdpr_Model_Resource_Queue_Collection
     */
    public function getQueuesForEntityType($entityType)
    {
        $collection = Mage::getResourceModel('phgdpr/queue_collection')
            ->addFieldToFilter('entity_type', $entityType)
        ;

        if ($collection->getSize() < 1) {
            return false;
        }

        return $collection;
    }

    /**
     * @return bool|PH2M_Gdpr_Model_Resource_Queue_Collection
     */
    public function getEntitiesToRun()
    {
        $collection = Mage::getResourceModel('phgdpr/queue_collection')
            ->addFieldToFilter('run_date', ['lteq' => Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s')]);

        if ($collection->getSize() < 1) {
            return false;
        }

        return $collection;
    }
}
