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
        if (!Zend_Validate::is(trim($this->getFirstname()), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The first name cannot be empty.');
        }

        if (!Zend_Validate::is(trim($this->getLastname()), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The last name cannot be empty.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();

        // START REWRITE
        // IF password format for gdpr is disable, use magento basic check
        if (!Mage::helper('phgdpr/password')->isPasswordGdprValidationEnabled()) {
			if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
				$errors[] = Mage::helper('customer')->__('The password cannot be empty.');
			}
			if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(self::MINIMUM_PASSWORD_LENGTH))) {
				$errors[] = Mage::helper('customer')
					->__('The minimum password length is %s', self::MINIMUM_PASSWORD_LENGTH);
			}
			if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array('max' => self::MAXIMUM_PASSWORD_LENGTH))) {
				$errors[] = Mage::helper('customer')
					->__('Please enter a password with at most %s characters.', self::MAXIMUM_PASSWORD_LENGTH);
			}
        } else {
            if (!$this->getId() && strlen($password) && $errorPassword = Mage::helper('phgdpr/password')->invalidPasswordFormat($password)) {
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
}
