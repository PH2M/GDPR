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

class PH2M_Gdpr_Model_Clean_Removefile extends Mage_Core_Model_Abstract implements PH2M_Gdpr_Model_Runinterface
{


    /**
     * Method call by queue runner
     *
     * @param $file
     * @return bool
     */
    public function run($file)
    {
        try {
            unlink($file);
            return true;
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('phgdpr')->log('Unable to remove file ' . $file, Zend_Log::ERR);
            return false;
        }
    }
}
