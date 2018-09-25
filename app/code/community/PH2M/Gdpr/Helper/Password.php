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

class PH2M_Gdpr_Helper_Password
{

    /**
     * Check if the password is valid for gdpr
     *
     * @param $password
     * @return array | bool
     */
    public function invalidPasswordFormat($password)
    {
        $errorCount = 1;
        if (strlen($password) < '8') {
            return Mage::helper('phgdpr')->__('Your password must contain at least 8 characters.');
        }
        if (!preg_match("#[0-9]+#", $password)) {
            $errorCount++;
        }
        if (!preg_match("#[A-Z]+#", $password)) {
            $errorCount++;
        }
        if (!preg_match("#[a-z]+#", $password)) {
            $errorCount++;
        }
        if (!preg_match("#[^a-zA-Z0-9$]#", $password)) {
            $errorCount++;
        }

        if ($errorCount >= 3) {
            return Mage::helper('phgdpr')->__('Please enter valid password, with 3 or more than options, numeric, lowercase, uppercase, special character');
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function isPasswordGdprValidationEnabled()
    {
        return Mage::getStoreConfig('phgdpr/fonctionality/password_format_validation');
    }
}
