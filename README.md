# WARNING, this module is in development, you can contribute on develop branch but we do not advise you to use this for the moment in production environment

# PH2M GDPR

Magento 1 module for respect reform of EU data protection rules (GDPR)


## Requirements

- Only test on Magento 1.9.x (but probably work on magento 1.4.x to 1.9.x)
- Aoe_Scheduler : https://github.com/AOEpeople/Aoe_Scheduler

## Changelog 
See RELEASE_NOTES.txt

## Installation
Composer :
```
composer require ph2m/gdpr
```
Manual :
Download this module and add 'app' and 'skin' directory to you magento

## Feature
- Full manageable, you can enabled / disabled all functionality from your back-office
- Download, remove and anonimyse customer data
    - Live system: customer can directly download or remove their own data from their dashboard
    - Queue system: you can enabled queue system for remove or download customer data if you have heavy treatment
    - Email notification 
- Extra features 
    - Enable complex password for respect CNIL recommendation 
    - Enable login attempt lock after 5 try
- Check GDPR validity
    - Check if all config for respect GDPR is enabled
    
- Remove and anonymse customer data:
    - Remove from newsletter (can be disabled)
    - Remove customer account (can be disabled)
    - Remove customer quote (can be disabled)
    - Anonimyse customer order (can be disabled)
- Download customer data:
    - Download customer account data (you can choose attributes to export or disabled)
    - Download customer addresses data (you can choose attributes to export or disabled)
    - Download customer orders data (you can choose attributes to export or disabled)
   
## Usage
- Enable all feature who you want on 'System > Configuration > General > GDPR'
- Run cron phgdpr_check_rules and check GDPR validity on 'System > Configuration > General > GDPR > Status'
- You can test download data or remove data from your customer dashboard

## Developer guide
#### ADD Custom process before / after customer download or remove data

```
before_request_delete_customer_data [customer, customer_email]
```
call before delete customer data, or add delete action in queue

```
after_request_delete_customer_data [customer, customer_email]
```
call after delete customer data, or add delete action in queue

```
before_delete_customer_data [customer, customer_email]
```
call just before delete customer data

```
after_delete_customer_data [customer, customer_email]
```
call just after delete customer data

```
before_download_customer_construct_data [customer, fileData]
```
call before construct customer data file, you can add your custom data on fileData varien_object

```
before_download_customer_construct_data [customer, fileData]
```
call after construct customer data file, you can add, update or remove customer data on fileData varien_object

#### ADD Custom process with queue system
You can add your own custom process thanks to the queue system. for this, follow this step :
1. create your own model, you need to implement `PH2M_Gdpr_Model_Interface` interface.
2. add your process to queue tab, you need to specified (use `Mage::getModel('phgdpr/queue')->addEntity()`:
 - entity_type (is the name of your class, look `PH2M_Gdpr_Model_Queue_Entitytype` for example)
 - params (you can add all info you want to get for you process)
 - run_date (date when you want run your process, keep empty for run at the next queue running)



## Licence

GNU General Public License, version 3 (GPLv3)


## Mini-help for contribution

Auto-generate modman with https://github.com/mhauri/generate-modman:

```
generate-modman --include-others  --include-others-files
```

## Configuration recommended (With magerun)
 ```

magerun config:set  "phgdpr/fonctionality/password_format_validation" "1"
magerun config:set  "phgdpr/fonctionality/login_limit_attempts" "1"

magerun config:set  "phgdpr/customer_data_remove/enable_customer_data_remove" "1"
magerun config:set  "phgdpr/customer_data_remove/enable_password_confirmation_for_delete" "1"
magerun config:set  "phgdpr/customer_data_remove/enable_remove_from_newsletter" "1"
magerun config:set  "phgdpr/customer_data_remove/enable_remove_quotes" "1"
magerun config:set  "phgdpr/customer_data_remove/enable_remove_customer_account" "1"


magerun config:set  "phgdpr/customer_data_download/enable_customer_data_download" "1"
magerun config:set  "phgdpr/customer_data_download/customer_attribute_to_export" "prefix,firstname,middlename,lastname,suffix,email,created_at,dob,gender"
magerun config:set  "phgdpr/customer_data_download/enable_customer_download_addresses" "1"
magerun config:set  "phgdpr/customer_data_download/address_attribute_to_export" "prefix,firstname,middlename,lastname,suffix,company,street,city,country_id,region,postcode,telephone,fax"
magerun config:set  "phgdpr/customer_data_download/enable_customer_download_orders" "1"
magerun config:set  "phgdpr/customer_data_download/order_attribute_to_export" "created_at,customer_dob,customer_email,customer_firstname,customer_gender,customer_lastname,customer_middlename,customer_prefix,customer_suffix,discount_amount,grand_total,shipping_amount,increment_id"

 ```
