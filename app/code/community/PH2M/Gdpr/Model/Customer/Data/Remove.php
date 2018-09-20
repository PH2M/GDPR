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

class PH2M_Gdpr_Model_Customer_Data_Remove extends Mage_Core_Model_Abstract implements PH2M_Gdpr_Model_Runinterface
{

    const XML_PATH_EMAIL_TEMPLATE           = 'phgdpr/customer_data_remove/confirm_email_template';
    const XML_PATH_EMAIL_SENDER             = 'phgdpr/customer_data_remove/email_sender_identity';


    /**
     * Method call by queue runner
     * Or delete customer request
     *
     * @param $params
     * @return bool
     */
    public function run($params)
    {
        return $this->deleteCustomerData($params);
    }


    /**
     * @param Mage_Customer_Model_Customer $customer $customer
     * @return bool
     */
    public function requestDeleteCustomerData($customer)
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_remove/enable')) {
            return false;
        }
        $customerEmail = $customer->getEmail();

        Mage::helper('phgdpr')->log('New delete customer data request, customer id [' . $customer->getId() . ']');

        Mage::dispatchEvent('request_customer_data_remove_before', ['customer' => $customer, 'customer_email' => $customerEmail]);

        if (Mage::getStoreConfig('phgdpr/customer_data_remove/remove_action_in_queue')) {
            if (Mage::getStoreConfig('phgdpr/customer_data_remove/enable_remove_from_newsletter')) {
                $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customerEmail);
                if ($subscriber->getId()) {
                    $subscriber->unsubscribe();
                }
            }
            $this->lockCustomerAccount($customer);
            Mage::getModel('phgdpr/queue')->addEntity(PH2M_Gdpr_Model_Queue_Entitytype::CUSTOMER_DELETE_DATA, $customer->getId(), Mage::helper('phgdpr')->getRunDateCustomerDeleteData());
        } else {
            $this->run($customer);
        }

        Mage::dispatchEvent('request_customer_data_remove_after', ['customer' => $customer, 'customer_email' => $customerEmail]);
        return true;
    }


    /**
     * @param  $customer
     * @return bool
     */
    public function deleteCustomerData($customer)
    {
        if (!$customer) {
            return false;
        }
        if (is_numeric($customer)) {
            $customer = Mage::getModel('customer/customer')->load($customer);
        }
        if (!$customer) {
            return false;
        }

        $customerEmail = $customer->getEmail();

        Mage::dispatchEvent('customer_data_remove_action_before', ['customer' => $customer, 'customer_email' => $customerEmail]);

        $this->deleteCustomerNewsletterSubscription($customerEmail);
        $this->deleteCustomerQuotes($customer);
        $this->anonymiseCustomerOrder($customer);
        $this->anonymiseCustomerProductReviews($customer);
        $this->deleteCustomerAccount($customer);


        /** @var Mage_Core_Model_Email_Template $email */
        $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $customer->getStore());
        if ($template) {
            $mailTemplate = Mage::getModel('core/email_template');
            /* @var $mailTemplate Mage_Core_Model_Email_Template */
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                    $customerEmail,
                    null,
                    []
                );
        }

        Mage::dispatchEvent('customer_data_remove_action_after', ['customer' => $customer, 'customer_email' => $customerEmail]);
        return true;
    }

    /**
     * Delete customer account
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return bool
     */
    protected function deleteCustomerAccount($customer)
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_remove/enable_remove_customer_account')) {
            return $this->lockCustomerAccount($customer);
        }
        try {
            // Disable Secure area for force customer delete from front area
            Mage::register('isSecureArea', true);
            $customer->delete();
            Mage::register('isSecureArea', false);
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('phgdpr')->log($e->getMessage(), Zend_Log::ERR);
            return false;
        }
        return true;
    }

    /**
     * @param $customer
     * @return bool
     */
    protected function lockCustomerAccount($customer)
    {
        $customer->setIsGdprLock(true);
        try {
            $customer->save();
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('phgdpr')->log($e->getMessage(), Zend_Log::ERR);
            return false;
        }
        return true;
    }


    /**
     * Remove newsletter subscription
     *
     * @param $customerEmail
     * @return bool
     */
    protected function deleteCustomerNewsletterSubscription($customerEmail)
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_remove/enable_remove_from_newsletter')) {
            return false;
        }

        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customerEmail);
        if ($subscriber->getId()) {
            try {
                $subscriber->unsubscribe();
                $subscriber->delete();
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::helper('phgdpr')->log($e->getMessage(), Zend_Log::ERR);
                return false;
            }
        }
        return true;
    }

    /**
     * @param $customer
     * @return bool
     */
    protected function deleteCustomerQuotes($customer)
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_remove/enable_remove_quotes')) {
            return false;
        }
        $quotes = Mage::getResourceModel('sales/quote_collection')
            ->addFieldToFilter('customer_id', $customer->getId());
        foreach ($quotes as $quote) {
            try {
                $quote->delete();
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::helper('phgdpr')->log($e->getMessage(), Zend_Log::ERR);
                return false;
            }
        }
        return true;
    }


    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return bool
     */
    protected function anonymiseCustomerOrder($customer)
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_remove/enable_anonimyse_orders')) {
            return false;
        }
        $orders = $this->getCustomerOrders($customer);
        foreach ($orders as $order) {
            $this->anonymiseSaleData($order);
        }
        return true;
    }


    protected function anonymiseCustomerProductReviews($customer)
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_remove/enable_anonimyse_customer_product_reviews')) {
            return false;
        }
        $reviews = $this->getCustomerProductReviews($customer);
        foreach ($reviews as $review) {
            $this->anonymiseReview($review);
        }
        return true;
    }

    /**
     * @param $customer
     * @return Mage_Eav_Model_Entity_Collection_Abstract|Mage_Sales_Model_Resource_Order_Collection
     */
    protected function getCustomerOrders($customer)
    {
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('customer_id', $customer->getId());
        return $orders;
    }


    /**
     * @param $customer
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    protected function getCustomerProductReviews($customer)
    {
        $reviews = Mage::getResourceModel('review/review_collection')
            ->addCustomerFilter($customer->getId())
        ;
        return $reviews;
    }

    /**
     * anonymise customer details from the address
     *
     * @param Mage_Sales_Model_Order_Address|Mage_Sales_Model_Quote_Address $address
     */
    protected function anonymiseSaleAddress($address)
    {
        $helper = Mage::helper('phgdpr');
        $address->setFirstname($helper->getRandom());
        $address->setMiddlename($helper->getRandom());
        $address->setLastname($helper->getRandom());
        $address->setCompany($helper->getRandom());
        $address->setEmail($helper->getRandom('email'));
        $address->setRegion($helper->getRandom());
        $address->setStreet($helper->getRandom());
        $address->setCity($helper->getRandom());
        $address->setPostcode($helper->getRandom());
        $address->setTelephone($helper->getRandom());
        $address->setFax($helper->getRandom());
    }

    /**
     * Anonymise customer details from a quote or order
     *
     * @param Mage_Sales_Model_Order $order
     */
    protected function anonymiseSaleData($order)
    {
        $helper = Mage::helper('phgdpr');
        $order->setCustomerFirstname($helper->getRandom());
        $order->setCustomerMiddlename($helper->getRandom());
        $order->setCustomerLastname($helper->getRandom());
        $order->setCustomerEmail($helper->getRandom('email'));
        $order->setRemoteIp($helper->getRandom('ip'));
        $this->anonymiseSaleAddress($order->getBillingAddress());
        $this->anonymiseSaleAddress($order->getShippingAddress());

        $paymentInformation = $order->getPayment()->getAdditionalInformation();
        if (isset($paymentInformation['paypal_payer_email'])) {
            $paymentInformation['paypal_payer_email'] = $helper->getRandom('email');
            $order->getPayment()->setAdditionalInformation($paymentInformation);
        }

        try {
            $order->save();
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('phgdpr')->log($e->getMessage(), Zend_Log::ERR);
        }
    }

    /**
     * Anonymise customer nickname for her review
     *
     * @param Mage_Review_Model_Review $review
     */
    protected function anonymiseReview($review)
    {
        $review->setNickname(Mage::helper('phgdpr')->__('Anonymous'));

        try {
            $review->save();
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('phgdpr')->log($e->getMessage(), Zend_Log::ERR);
        }
    }
}
