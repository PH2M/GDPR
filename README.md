GDPR Module for Magento 1
==================
Free Magento 1 module for respect reform of EU data protection rules (GDPR)

![Latest version](https://img.shields.io/badge/latest-v1.1.5-green.svg)
![PHP >= 5.3](https://img.shields.io/badge/php-%3E=5.3-green.svg)
![Magento 1.9.3](https://img.shields.io/badge/magento-1.9.3-blue.svg)


Changelog 
--------
See RELEASE_NOTES.txt

Installation
---------
Composer :
```
composer require ph2m/gdpr
```

Manual :
Download this module and add 'app' and 'skin' directory to you magento

Feature
-------
**Full manageable, you can enabled / disabled all functionality from your back-office**
- Download, remove and anonymize customer data
    - Live system: customer can directly download or remove their own data from their dashboard
    - [developer only] Queue system: you can enabled queue system for remove or download customer data if you have heavy treatment 
    - Email notification 
- Extra features 
    - Enable complex password for respect [CNIL recommendation](https://www.cnil.fr/fr/authentification-par-mot-de-passe-les-mesures-de-securite-elementaires) 
    - Enable login attempt lock multi try
- Check GDPR validity
    - Check if all config for respect GDPR is enabled
- Remove and anonymize customer data:
    - Remove from newsletter
    - Remove customer account
    - Remove customer quote
    - Anonymize customer order
    - Anonymise customer product reviews
- Download customer data:
    - Download customer account data (you can choose attributes to export)
    - Download customer addresses data (you can choose attributes to export)
    - Download customer orders data (you can choose attributes to export)
    - Donwload customer product reviews
- Manage cookies (with [tarteaucitron.js](https://github.com/AmauriC/tarteaucitron.js))
    - Display cookies consent banner and popup
    - Compatible with magento google analytics (can be disabled)
    
Usage
------
- Enable all feature who you want on 'System > Configuration > General > GDPR'
- You can test download data or remove data from your customer dashboard

Documentation
-------
[Module documentation](https://github.com/PH2M/GDPR/wiki/Documentation).

[Developer documentation](https://github.com/PH2M/GDPR/wiki/Developer-guide).

Licence
-------
GNU General Public License, version 3 (GPLv3)

Mini-help for contribution
--------
Auto-generate [modman](https://github.com/mhauri/generate-modman)
```
make modman
```

Configuration recommended (with [magerun](https://github.com/netz98/n98-magerun))
--------
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
