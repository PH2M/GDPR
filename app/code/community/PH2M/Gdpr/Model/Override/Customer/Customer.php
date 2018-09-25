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

class PH2M_Gdpr_Model_Override_Customer_Customer extends Mage_Customer_Model_Customer
{
    /**
     * Confirmation requirement flag
     *
     * @var boolean
     */
    private static $_isConfirmationRequired;

    const DEFAULT_ATTEMPS_NUMBER = 5;
    const DEFAULT_TIME_BLOCKED = 27000;


    /**
     * REWRITE : Add gdpr password verification
     *
     * Validate customer attribute values.
     * For existing customer password + confirmation will be validated only when password is set (i.e. its change is requested)
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();
        if (!Zend_Validate::is( trim($this->getFirstname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The first name cannot be empty.');
        }

        if (!Zend_Validate::is( trim($this->getLastname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The last name cannot be empty.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();

        // START REWRITE
        // IF password format for gdpr is disable, use magento basic check
        if(!Mage::helper('phgdpr/password')->isPasswordGdprValidationEnabled()) {
            if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
                $errors[] = Mage::helper('customer')->__('The password cannot be empty.');
            }
            if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
                $errors[] = Mage::helper('customer')->__('The minimum password length is %s', 6);
            }
        } else {
            if(!$this->getId() && strlen($password) && $errorPassword = Mage::helper('phgdpr/password')->invalidPasswordFormat($password)) {
                $errors[] = $errorPassword;
            }
        }
        // END REWRITE

        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }

        $entityType = Mage::getSingleton('eav/config')->getEntityType('customer');
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'dob');
        if ($attribute->getIsRequired() && '' == trim($this->getDob())) {
            $errors[] = Mage::helper('customer')->__('The Date of Birth is required.');
        }
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'taxvat');
        if ($attribute->getIsRequired() && '' == trim($this->getTaxvat())) {
            $errors[] = Mage::helper('customer')->__('The TAX/VAT number is required.');
        }
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'gender');
        if ($attribute->getIsRequired() && '' == trim($this->getGender())) {
            $errors[] = Mage::helper('customer')->__('Gender is required.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }


    /**
     * REWRITE : Add limit login attempt
     *
     * @param  string $login
     * @param  string $password
     * @throws Mage_Core_Exception
     * @return true
     *
     */
    public function authenticate($login, $password)
    {

        $this->loadByEmail($login);
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core',
                Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            $this->verifyAttempts();
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }

        if ($this->verifyAttempts()) {
            Mage::dispatchEvent('customer_customer_authenticated', array(
                'model'    => $this,
                'password' => $password,
            ));
            return true;
        }
    }

    /**
     * Verify user connection attempts to limit request
     *
     * @return bool
     */
    protected function verifyAttempts()
    {

        if (!Mage::getStoreConfig('phgdpr/fonctionality/login_limit_attempts')) {
            return false;
        }

        if (!$numberAttemps = Mage::getStoreConfig('phgdpr/valid_rules/customer_login_attemps_number')) {
            $numberAttemps = self::DEFAULT_ATTEMPS_NUMBER;
        }

        if (!$timeBlocked = intval(Mage::getStoreConfig('phgdpr/valid_rules/customer_login_time_blocked'))*60) {
            $timeBlocked = self::DEFAULT_TIME_BLOCKED;
        }

        $cookie = Mage::getSingleton('core/cookie');

        $cookieLoginAttemps = $cookie->get('loginattemps');
        if ($cookieLoginAttemps) {
            if ($cookieLoginAttemps >= $numberAttemps) {
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('core')->__('You tried to log in too many times, try later')
                );
            }
            $cookieLoginAttemps++;
            $cookie->set('loginattemps', $cookieLoginAttemps, $timeBlocked, '/');
            return false;
        }
        else {
            $cookie->set('loginattemps', 1, $timeBlocked, '/');
        }

        return true;
    }
}
