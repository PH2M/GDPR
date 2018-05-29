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


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */


/**
 * Create table 'phgdpr/queue'
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('phgdpr/queue'))
    ->addColumn('queue_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
		'unsigned'  => true,
        ), 'ID')
    ->addColumn('entity_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Entity type')
    ->addColumn('params', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        ), 'Params')
    ->addColumn('run_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Run date')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Modification Time')
    ->setComment('Queue Table');
$installer->getConnection()->createTable($table);
