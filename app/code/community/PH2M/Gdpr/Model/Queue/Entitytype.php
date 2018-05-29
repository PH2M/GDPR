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


class PH2M_Gdpr_Model_Queue_Entitytype extends Varien_Object
{

    const CUSTOMER_DELETE_DATA            = 'phgdpr/customer_data_remove';
    const CUSTOMER_DOWNLOAD_DATA          = 'phgdpr/customer_data_download';
    const CUSTOMER_REMOVE_DOWNLOAD_FILE   = 'phgdpr/clean_removefile';
}
