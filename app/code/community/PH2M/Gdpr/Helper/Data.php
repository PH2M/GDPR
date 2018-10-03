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

class PH2M_Gdpr_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param string $type
     * @return null|string
     */
    public function getRandom($type = 'str')
    {
        if ($type == 'str') {
            return 'anonymous';
        } elseif ($type == 'email') {
            $randString = str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(1, 10));
            $rand       = substr(str_shuffle($randString), 1, 10);
            return $rand . '@example.com';
        } elseif ($type == 'ip') {
            return '0.0.0.0';
        }
        return null;
    }

    /**
     * @return string
     */
    public function getRunDateCustomerDeleteData()
    {
        if ($delay = Mage::getStoreConfig('phgdpr/customer_data_remove/queue_delay')) {
            $timestamp = Mage::getSingleton('core/date')->gmtTimestamp() + $delay;
            return date("Y-m-d H:i:s", $timestamp);
        }
        return Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
    }


    /**
     * @return string
     */
    public function getRunDateRemoveCustomerDownloadFile()
    {
        if ($delay = Mage::getStoreConfig('phgdpr/customer_data_download/queue_delay_remove_file')) {
            $timestamp = Mage::getSingleton('core/date')->gmtTimestamp() + $delay;
            return date("Y-m-d H:i:s", $timestamp);
        }
        return Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function getDownloadContentFileUrl()
    {
        return Mage::getUrl('phgdpr/customer/downloadFile');
    }
    /**
     * @return string
     */
    public function getRemoveCustomerAccountUrl()
    {
        return Mage::getUrl('phgdpr/customer/deleteAccount');
    }

    /**
     * @return string
     */
    public function getDownloadAccountDataUrl()
    {
        return Mage::getUrl('phgdpr/customer/downloadCustomerData');
    }

    /**
     * @param $message
     * @param int $level
     */
    public function log($message, $level = Zend_Log::NOTICE)
    {
        Mage::log($message, $level, 'phgdpr.log');
    }


    /**
     * @param $customerId
     * @return string
     */
    public function getCustomerDataFile($customerId)
    {
        $directory  =  Mage::getBaseDir('var') . DS . 'phgdpr';
        $file       = $directory . DS . 'customer-data-file-' . $customerId . '.json';

        return $file;
    }
}
