<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    GST Module
 * @package     GST
 * @copyright   Copyright (c) 20170 .Codilar. (http://www.codilar.com)
 * @purpose     [BRIEF ABOUT THE FILE]
 * @author       Codilar Team
 **/

class Codilar_Gst_Model_Order_Pdf_Total_Default extends Mage_Tax_Model_Sales_Pdf_Grandtotal
{
    /**
     * Get array of arrays with totals information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $store = $this->getOrder()->getStore();
        $config= Mage::getSingleton('tax/config');
        if (!$config->displaySalesTaxWithGrandTotal($store)) {
            return parent::getTotalsForDisplay();
        }
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $amountExclTax = $this->getAmount() - $this->getSource()->getTaxAmount();
        $amountExclTax = ($amountExclTax > 0) ? $amountExclTax : 0;
        $amountExclTax = $this->getOrder()->formatPriceTxt($amountExclTax);
        $tax = $this->getOrder()->formatPriceTxt($this->getSource()->getTaxAmount());
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        $totals = array(array(
            'amount'    => $this->getAmountPrefix().$amountExclTax,
            'label'     => Mage::helper('tax')->__('Grand Total (Excl. GST)') . ':',
            'font_size' => $fontSize
        ));

//        if ($config->displaySalesFullSummary($store)) {
//            $totals = array_merge($totals, $this->getFullTaxInfo());
//        }

        $totals[] = array(
        'amount'    => $this->getAmountPrefix().$tax,
        'label'     => Mage::helper('tax')->__('Total GST') . ':',
        'font_size' => $fontSize
        );

        $totals[] = array(
            'amount'    => $this->getAmountPrefix().$amount,
            'label'     => Mage::helper('tax')->__('Grand Total (Incl. GST)') . ':',
            'font_size' => $fontSize
        );
        return $totals;
    }



}
