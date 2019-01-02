<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    GST Module
 * @package     GST
 * @copyright   Copyright (c) 20170 .Codilar. (http://www.codilar.com)
 * @purpose     [BRIEF ABOUT THE FILE]
 * @author       Codilar Team
 **/

class Codilar_Gst_Model_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
    /**
     * Draw item line
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        
        //gst
        $store_id =  $order->getStoreId();
        $CreatedAt = $order->getCreatedAt();
        $helper = Mage::helper('codilar_gst');
        $lstdate = $helper->orderDate();
        $_shippingAddress = $order->getShippingAddress();
        $shipping_city = $_shippingAddress->getRegion() ;
        $production_state = Mage::getStoreConfig('general/gst_options/gst_state', $store_id);
        $sku = $item->getSku();
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        $hsn_code = $product->getHsnCode();
        if(strcasecmp($shipping_city,$production_state) == 0 ){
            $tax_amount_new = $item->getOrderItem()->getTaxAmount()/2;
            $gst_percent = $item->getOrderItem()->getTaxPercent()/2;
            $igst_percent = 0;
            $tax_igst = 0;
        }else {
            $tax_igst = $item->getOrderItem()->getTaxAmount();
            $gst_percent = 0;
            $igst_percent = $item->getOrderItem()->getTaxPercent();
        }
        
        $lines  = array();

        // draw Product name
        $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 30, true, true),
            'feed' => 35,
        ));

        // draw SKU
        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 10),
            'feed'  => 200,
            'align' => 'right'
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty() * 1,
            'feed'  => 360,
            'align' => 'right'
        );
        $lines[0][] = array(
            'text'  => $hsn_code,
            'feed'  => 250,
            'align' => 'right'
        );

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 320;
        $feedSubtotal = $feedPrice + 245;
        foreach ($prices as $priceData){
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = array(
                    'text'  => $priceData['label'],
                    'feed'  => $feedPrice,
                    'align' => 'right'
                );
                // draw Subtotal label
                $lines[$i][] = array(
                    'text'  => $priceData['label'],
                    'feed'  => $feedSubtotal,
                    'align' => 'right'
                );
                $i++;
            }
            // draw Price
            $lines[$i][] = array(
                'text'  => $priceData['price'],
                'feed'  => $feedPrice,
                'font'  => 'bold',
                'align' => 'right'
            );
            // draw Subtotal
            $lines[$i][] = array(
                'text'  => $priceData['subtotal'],
                'feed'  => $feedSubtotal,
                'font'  => 'bold',
                'align' => 'right'
            );
            $i++;
        }
        
        if($CreatedAt  < $lstdate){
        // draw Tax
       $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => 430,
            'font'  => 'bold',
            'align' => 'right'
        );
        }else{
        //draw cgst
        if($tax_amount_new == 0){
            $total_cgst = $order->formatPriceTxt($tax_amount_new);
        }else {
            $data = $order->formatPriceTxt($tax_amount_new);
            $per =  "(" . number_format($gst_percent, 2, ".", " ") . "%" . ")";
            $total_cgst = $this->splitString($data, $per);
        }
        $lines[0][] = array(
            'text'  => $total_cgst,
            'feed'  => 410,
            'font'  => 'bold',
            'align' => 'right'
        );
        
        //draw sgst
        $lines[0][] = array(
            'text'  => $total_cgst,
            'feed'  => 460,
            'font'  => 'bold',
            'align' => 'right'
        );
        
        //draw igst
        if($tax_igst == 0) {
            $price_perc = $order->formatPriceTxt($tax_igst);
        }else{
            $data =  $order->formatPriceTxt($tax_igst);
            $per =  '(' . number_format($igst_percent, 2, '.', '') . '%' . ')';
            $price_perc = $this->splitString($data, $per);
        }
        $lines[0][] = array(
            'text'  => $price_perc,
            'feed'  => 510,
            'font'  => 'bold',
            'align' => 'right'
        );
        }

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $_printValue = $option['print_value'];
                    } else {
                        $_printValue = strip_tags($option['value']);
                    }
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 30, true, true),
                            'feed' => 40
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }

    /**
     * @param $data
     * @param $per
     * @return array
     */
    public function splitString($data, $per)
    {
        if (strlen($data) > strlen($per)) {
            $total_cgst = str_split($data . $per, strlen($data));
            return $total_cgst;
        } else {
            for ($i = strlen($data); $i < strlen($per); $i++) {
                $data = $data . " ";
            }
            $total_cgst = str_split($data . $per, strlen($per));
            return $total_cgst;
        }
    }
}
