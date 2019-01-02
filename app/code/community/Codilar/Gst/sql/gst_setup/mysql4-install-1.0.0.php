<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order_tax'), 'cgst', 'DECIMAL( 11, 5 ) NOT NULL ');
$installer->getConnection()->addColumn($installer->getTable('sales/order_tax'), 'sgst', 'DECIMAL( 11, 5 ) NOT NULL ');
$installer->getConnection()->addColumn($installer->getTable('sales/order_tax'), 'igst', 'DECIMAL( 11, 5 ) NOT NULL ');

$installer->getConnection()->addColumn($installer->getTable('tax/tax_order_aggregated_created'), 'total_cgst', 'DECIMAL( 11, 5 ) NOT NULL ');
$installer->getConnection()->addColumn($installer->getTable('tax/tax_order_aggregated_created'), 'total_sgst', 'DECIMAL( 11, 5 ) NOT NULL ');
$installer->getConnection()->addColumn($installer->getTable('tax/tax_order_aggregated_created'), 'total_igst', 'DECIMAL( 11, 5 ) NOT NULL ');

$installer->getConnection()->addColumn($installer->getTable('tax/tax_order_aggregated_updated'), 'total_cgst', 'DECIMAL( 11, 5 ) NOT NULL ');
$installer->getConnection()->addColumn($installer->getTable('tax/tax_order_aggregated_updated'), 'total_sgst', 'DECIMAL( 11, 5 ) NOT NULL ');
$installer->getConnection()->addColumn($installer->getTable('tax/tax_order_aggregated_updated'), 'total_igst', 'DECIMAL( 11, 5 ) NOT NULL ');

$installer->getConnection()->addColumn($installer->getTable('sales/order_aggregated_created'), 'total_cgst', 'DECIMAL( 11, 5 ) NOT NULL ');
$installer->getConnection()->addColumn($installer->getTable('sales/order_aggregated_created'), 'total_sgst', 'DECIMAL( 11, 5 ) NOT NULL ');
$installer->getConnection()->addColumn($installer->getTable('sales/order_aggregated_created'), 'total_igst', 'DECIMAL( 11, 5 ) NOT NULL ');

$installer->getConnection()->addColumn($installer->getTable('sales/order_aggregated_updated'), 'total_cgst', 'DECIMAL( 11, 5 ) NOT NULL ');
$installer->getConnection()->addColumn($installer->getTable('sales/order_aggregated_updated'), 'total_sgst', 'DECIMAL( 11, 5 ) NOT NULL ');
$installer->getConnection()->addColumn($installer->getTable('sales/order_aggregated_updated'), 'total_igst', 'DECIMAL( 11, 5 ) NOT NULL ');

$installer->endSetup();