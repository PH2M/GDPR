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

class PH2M_Gdpr_Model_Resource_Queue extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('phgdpr/queue', 'queue_id');
    }


    /**
     * @param Mage_Core_Model_Abstract $queue
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $queue)
    {
        // modify create / update dates
        if ($queue->isObjectNew() && !$queue->hasCreationTime()) {
            $queue->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $queue->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($queue);
    }
}
