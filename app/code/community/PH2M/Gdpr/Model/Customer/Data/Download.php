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

class PH2M_Gdpr_Model_Customer_Data_Download extends Mage_Core_Model_Abstract implements PH2M_Gdpr_Model_Runinterface
{
    const XML_PATH_EMAIL_TEMPLATE           = 'phgdpr/customer_data_download/email_template';
    const XML_PATH_EMAIL_SENDER             = 'phgdpr/customer_data_download/email_sender_identity';


    /**
     * Method call by queue runner
     * Or delete customer request
     *
     * @param $params
     * @return mixed
     */
    public function run($params)
    {
        return $this->retrieveCustomerData($params);
    }


    /**
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return mixed
     */
    public function retrieveCustomerData($customer)
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
        if (!Mage::getStoreConfig('phgdpr/customer_data_download/enable')) {
            return false;
        }

        $fileData = new Varien_Object();
        $jsonData = '';

        Mage::helper('phgdpr')->log('New download customer data request, customer id [' . $customer->getId() . ']');

        Mage::dispatchEvent('customer_data_download_construct_data_before', ['customer' => $customer, 'fileData' => $fileData]);

        $this->addCustomerAccountData($fileData, $customer);
        $this->addCustomerAddressData($fileData, $customer);
        $this->addCustomerOrderData($fileData, $customer);
        $this->addCustomerProductReviewData($fileData, $customer);
        Mage::dispatchEvent('customer_data_download_construct_data_after', ['customer' => $customer, 'fileData' => $fileData]);
        try {
            $jsonData = $fileData->toJson();
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('phgdpr')->log($e->getMessage(), Zend_Log::ERR);
        }
        if (!$jsonData) {
            return false;
        }
        if (Mage::getStoreConfig('phgdpr/customer_data_download/download_action_in_queue')) {
            $this->sendCanDownloadFileMail($jsonData, $customer);
            return true;
        }
        return $jsonData;
    }

    /**
     * @param $jsonData
     * @param Mage_Customer_Model_Customer $customer $customer
     */
    protected function sendCanDownloadFileMail($jsonData, $customer)
    {
        $file = $this->saveDataInFile($jsonData, $customer);
        Mage::getModel('phgdpr/queue')->addEntity(PH2M_Gdpr_Model_Queue_Entitytype::CUSTOMER_REMOVE_DOWNLOAD_FILE, $file, Mage::helper('phgdpr')->getRunDateRemoveCustomerDownloadFile());
        /** @var Mage_Core_Model_Email_Template $email */
        $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $customer->getStore());
        if ($template) {
            $mailTemplate = Mage::getModel('core/email_template');
            /* @var $mailTemplate Mage_Core_Model_Email_Template */
            $mailTemplate->setDesignConfig(['area' => 'frontend'])
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                    $customer->getEmail(),
                    null,
                    []
                );
        }
    }

    /**
     * @param $jsonData
     * @param Mage_Customer_Model_Customer $customer $customer
     * @return string
     */
    public function saveDataInFile($jsonData, $customer)
    {
        $directory  =  Mage::getBaseDir('var') . DS . 'phgdpr';
        $file       = $directory . DS . 'customer-data-file-' . $customer->getId() . '.json';
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        file_put_contents($file, $jsonData);

        return $file;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer $customer
     * @return bool|mixed
     */
    public function requestRetrieveCustomerData($customer)
    {
        if (!Mage::getStoreConfig('phgdpr/customer_data_download/enable')) {
            return false;
        }
        $customerEmail = $customer->getEmail();

        Mage::helper('phgdpr')->log('New retrieve customer data request, customer id [' . $customer->getId() . ']');

        Mage::dispatchEvent('request_customer_data_download_before', ['customer' => $customer, 'customer_email' => $customerEmail]);

        if (Mage::getStoreConfig('phgdpr/customer_data_download/download_action_in_queue')) {
            Mage::getModel('phgdpr/queue')->addEntity(PH2M_Gdpr_Model_Queue_Entitytype::CUSTOMER_DOWNLOAD_DATA, $customer->getId());
            $data = false;
        } else {
            $data = $this->run($customer);
        }

        Mage::dispatchEvent('request_customer_data_download_after', ['customer' => $customer, 'customer_email' => $customerEmail]);
        return $data;
    }



    /**
     * @param $fileData
     * @param Mage_Customer_Model_Customer $customer $customer
     * @return bool
     */
    protected function addCustomerAccountData($fileData, $customer)
    {
        if (!$customerAttributes = Mage::getStoreConfig('phgdpr/customer_data_download/customer_attribute_to_export')) {
            return false;
        }
        $customerAttributes = explode(',', $customerAttributes);
        foreach ($customerAttributes as $attribute) {
            $attributeName = $customer->getResource()
                ->getAttribute($attribute)
                ->getFrontend()
                ->getAttribute()
                ->getFrontendLabel();
            $fileData->setData($attributeName, $customer->getData($attribute));
        }

        return true;
    }


    /**
     * @param $fileData
     * @param Mage_Customer_Model_Customer $customer $customer
     * @return bool
     */
    protected function addCustomerAddressData($fileData, $customer)
    {
        /** @var $address Mage_Customer_Model_Address */
        if (!Mage::getStoreConfig('phgdpr/customer_data_download/enable_customer_download_addresses')) {
            return false;
        }
        $addresses = $customer->getAddresses();
        if (!$addresses) {
            return false;
        }
        if (!$addressAttributes  = Mage::getStoreConfig('phgdpr/customer_data_download/address_attribute_to_export')) {
            return false;
        }
        $addressAttributes      = explode(',', $addressAttributes);
        $addressData            = [];
        foreach ($addresses as $address) {
            foreach ($addressAttributes as $attribute) {
                $attributeName = $address->getResource()
                    ->getAttribute($attribute)
                    ->getFrontendLabel();
                $addressData[$address->getId()][$attributeName] = $address->getData($attribute);
            }
        }

        $fileData->setAddresses($addressData);
        return true;
    }


    /**
     * @param $fileData
     * @param Mage_Customer_Model_Customer $customer $customer
     * @return bool
     */
    protected function addCustomerOrderData($fileData, $customer)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        if (!Mage::getStoreConfig('phgdpr/customer_data_download/enable_customer_download_orders')) {
            return false;
        }
        $orders = $this->getCustomerOrders($customer);
        $orders->addAttributeToSelect('*');
        if (!$orders->getSize()) {
            return false;
        }
        if (!$orderAttributes  = Mage::getStoreConfig('phgdpr/customer_data_download/order_attribute_to_export')) {
            return false;
        }
        $orderAttributes      = explode(',', $orderAttributes);
        $orderData            = [];
        foreach ($orders as $order) {
            foreach ($orderAttributes as $attribute) {
                $orderData[$order->getIncrementId()][$attribute] = $order->getData($attribute);
            }
        }

        $fileData->setOrders($orderData);
        return true;
    }

    /**
     * @param $fileData
     * @param Mage_Customer_Model_Customer $customer $customer
     * @return bool
     */
    protected function addCustomerProductReviewData($fileData, $customer)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        if (!Mage::getStoreConfig('phgdpr/customer_data_download/enable_customer_product_reviews')) {
            return false;
        }
        $reviews = $this->getCustomerProductReviews($customer);
        if (!$reviews->getSize()) {
            return false;
        }
        $reviewData            = [];
        foreach ($reviews as $review) {
            $reviewData[$review->getId()] = $review->getData();
        }

        $fileData->setProductReviews($reviewData);
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
            ->addCustomerFilter($customer->getId());
        return $reviews;
    }
}
