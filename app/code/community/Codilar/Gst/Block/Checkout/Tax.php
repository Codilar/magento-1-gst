<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    GST Module
 * @package     GST
 * @copyright   Copyright (c) 20170 .Codilar. (http://www.codilar.com)
 * @purpose     [BRIEF ABOUT THE FILE]
 * @author       Codilar Team
 **/

class Codilar_Gst_Block_Checkout_Tax extends Mage_Tax_Block_Checkout_Tax
{
    /**
     * Template used in the block
     *
     * @var string
     */
    protected $_template = 'gst/tax/checkout/tax.phtml';

    /**
     * The factory instance to get helper
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

}
