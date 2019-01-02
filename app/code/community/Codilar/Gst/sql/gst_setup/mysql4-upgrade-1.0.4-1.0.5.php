<?php
/**
 * Upgrade script for adding gst_price_rules attribute to category entity
 */
$installer = $this;
$installer->startSetup();

$attribute  = array(
        'group'                     => 'General',
        'input'                     => 'text',
        'type'                      => 'text',
        'label'                     => 'GST Price Rules',
        'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'                   => 0,
        'required'                  => 0,
        'visible_on_front'          => 0,
        'is_html_allowed_on_front'  => 0,
        'is_configurable'           => 0,
        'searchable'                => 0,
        'filterable'                => 0,
        'comparable'                => 0,
        'unique'                    => false,
        'user_defined'              => false,
        'default'                   => '',
        'is_user_defined'           => false,
        'used_in_product_listing'   => false
);
$installer->addAttribute('catalog_category', 'gst_price_rules', $attribute);
$installer->endSetup();