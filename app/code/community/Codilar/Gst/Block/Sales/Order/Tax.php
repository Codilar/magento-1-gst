<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    GST Module
 * @package     GST
 * @copyright   Copyright (c) 20170 .Codilar. (http://www.codilar.com)
 * @purpose     [BRIEF ABOUT THE FILE]
 * @author       Codilar Team
 **/

/**
 * Tax totals modification block. Can be used just as subblock of Mage_Sales_Block_Order_Totals
 */
class Codilar_Gst_Block_Sales_Order_Tax extends Mage_Adminhtml_Block_Sales_Order_Totals_Tax
{
    /**
     * Tax configuration model
     *
     * @var Mage_Tax_Model_Config
     */
    protected $_config;
    protected $_order;
    protected $_source;



    protected function _initSubtotal()
    {
        $store  = $this->getStore();
        $parent = $this->getParentBlock();
        $subtotal = $parent->getTotal('subtotal');
        if (!$subtotal) {
            return $this;
        }
        if ($this->_config->displaySalesSubtotalBoth($store)) {
            $subtotal       = (float) $this->_source->getSubtotal();
            $baseSubtotal   = (float) $this->_source->getBaseSubtotal();
            $subtotalIncl   = (float) $this->_source->getSubtotalInclTax();
            $baseSubtotalIncl= (float) $this->_source->getBaseSubtotalInclTax();

            if (!$subtotalIncl || !$baseSubtotalIncl) {
                //Calculate the subtotal if not set
                $subtotalIncl = $subtotal + $this->_source->getTaxAmount()
                    - $this->_source->getShippingTaxAmount();
                $baseSubtotalIncl = $baseSubtotal + $this->_source->getBaseTaxAmount()
                    - $this->_source->getBaseShippingTaxAmount();

                if ($this->_source instanceof Mage_Sales_Model_Order) {
                    //Adjust the discount amounts for the base and well as the weee to display the right totals
                    foreach ($this->_source->getAllItems() as $item) {
                        $subtotalIncl += $item->getHiddenTaxAmount() + $item->getDiscountAppliedForWeeeTax();
                        $baseSubtotalIncl += $item->getBaseHiddenTaxAmount() +
                            $item->getBaseDiscountAppliedForWeeeTax();
                    }
                }
            }

            $subtotalIncl = max(0, $subtotalIncl);
            $baseSubtotalIncl = max(0, $baseSubtotalIncl);
            $totalExcl = new Varien_Object(array(
                'code'      => 'subtotal_excl',
                'value'     => $subtotal,
                'base_value'=> $baseSubtotal,
                'label'     => $this->__('Subtotal (Excl.GST)')
            ));
            $totalIncl = new Varien_Object(array(
                'code'      => 'subtotal_incl',
                'value'     => $subtotalIncl,
                'base_value'=> $baseSubtotalIncl,
                'label'     => $this->__('Subtotal (Incl.GST)')
            ));
            $parent->addTotal($totalExcl, 'subtotal');
            $parent->addTotal($totalIncl, 'subtotal_excl');
            $parent->removeTotal('subtotal');
        } elseif ($this->_config->displaySalesSubtotalInclTax($store)) {
            $subtotalIncl   = (float) $this->_source->getSubtotalInclTax();
            $baseSubtotalIncl= (float) $this->_source->getBaseSubtotalInclTax();

            if (!$subtotalIncl) {
                $subtotalIncl = $this->_source->getSubtotal()
                    + $this->_source->getTaxAmount()
                    - $this->_source->getShippingTaxAmount();
            }
            if (!$baseSubtotalIncl) {
                $baseSubtotalIncl = $this->_source->getBaseSubtotal()
                    + $this->_source->getBaseTaxAmount()
                    - $this->_source->getBaseShippingTaxAmount();
            }

            $total = $parent->getTotal('subtotal');
            if ($total) {
                $total->setValue(max(0, $subtotalIncl));
                $total->setBaseValue(max(0, $baseSubtotalIncl));
            }
        }
        return $this;
    }

    protected function _initShipping()
    {
        $store  = $this->getStore();
        $parent = $this->getParentBlock();
        $shipping = $parent->getTotal('shipping');
        if (!$shipping) {
            return $this;
        }

        if ($this->_config->displaySalesShippingBoth($store)) {
            $shipping           = (float) $this->_source->getShippingAmount();
            $baseShipping       = (float) $this->_source->getBaseShippingAmount();
            $shippingIncl       = (float) $this->_source->getShippingInclTax();
            if (!$shippingIncl) {
                $shippingIncl   = $shipping + (float) $this->_source->getShippingTaxAmount();
            }
            $baseShippingIncl   = (float) $this->_source->getBaseShippingInclTax();
            if (!$baseShippingIncl) {
                $baseShippingIncl = $baseShipping + (float) $this->_source->getBaseShippingTaxAmount();
            }

            $totalExcl = new Varien_Object(array(
                'code'      => 'shipping',
                'value'     => $shipping,
                'base_value'=> $baseShipping,
                'label'     => $this->__('Shipping & Handling (Excl.GST)')
            ));
            $totalIncl = new Varien_Object(array(
                'code'      => 'shipping_incl',
                'value'     => $shippingIncl,
                'base_value'=> $baseShippingIncl,
                'label'     => $this->__('Shipping & Handling (Incl.GST)')
            ));
            $parent->addTotal($totalExcl, 'shipping');
            $parent->addTotal($totalIncl, 'shipping');
        } elseif ($this->_config->displaySalesShippingInclTax($store)) {
            $shippingIncl       = $this->_source->getShippingInclTax();
            if (!$shippingIncl) {
                $shippingIncl = $this->_source->getShippingAmount()
                    + $this->_source->getShippingTaxAmount();
            }
            $baseShippingIncl   = $this->_source->getBaseShippingInclTax();
            if (!$baseShippingIncl) {
                $baseShippingIncl = $this->_source->getBaseShippingAmount()
                    + $this->_source->getBaseShippingTaxAmount();
            }
            $total = $parent->getTotal('shipping');
            if ($total) {
                $total->setValue($shippingIncl);
                $total->setBaseValue($baseShippingIncl);
            }
        }
        return $this;
    }


    protected function _initGrandTotal()
    {
        $store  = $this->getStore();
        $parent = $this->getParentBlock();
        $grandototal = $parent->getTotal('grand_total');
        if (!$grandototal || !(float)$this->_source->getGrandTotal()) {
            return $this;
        }

        if ($this->_config->displaySalesTaxWithGrandTotal($store)) {
            $grandtotal         = $this->_source->getGrandTotal();
            $baseGrandtotal     = $this->_source->getBaseGrandTotal();
            $grandtotalExcl     = $grandtotal - $this->_source->getTaxAmount();
            $baseGrandtotalExcl = $baseGrandtotal - $this->_source->getBaseTaxAmount();
            $grandtotalExcl     = max($grandtotalExcl, 0);
            $baseGrandtotalExcl = max($baseGrandtotalExcl, 0);
            $totalExcl = new Varien_Object(array(
                'code'      => 'grand_total',
                'strong'    => true,
                'value'     => $grandtotalExcl,
                'base_value'=> $baseGrandtotalExcl,
                'label'     => $this->__('Grand Total (Excl.GST)')
            ));
            $totalIncl = new Varien_Object(array(
                'code'      => 'grand_total_incl',
                'strong'    => true,
                'value'     => $grandtotal,
                'base_value'=> $baseGrandtotal,
                'label'     => $this->__('Grand Total (Incl.GST)')
            ));
            $parent->addTotal($totalExcl, 'grand_total');
            $this->_addTax('grand_total');
            $parent->addTotal($totalIncl, 'tax');
        }
        return $this;
    }

}
