<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    GST Module
 * @package     GST
 * @copyright   Copyright (c) 20170 .Codilar. (http://www.codilar.com)
 * @purpose     [BRIEF ABOUT THE FILE]
 * @author       Codilar Team
 **/

class Codilar_Gst_Model_Observer
{
    /**
     * @param $event
     * @throws Exception
     */
    public function gstAdd($event)
    {
        $order = $event->getEvent()->getOrder();
        $id= $order->getId();
        $store_id =  $order->getStoreId();
        $_shippingAddress = $order->getShippingAddress();
        $shipping_city = $_shippingAddress->getRegion();
        $storeId_site = Mage::app()->getStore()->getStoreId();
        $gst_status = Mage::getStoreConfig('general/gst_options/gst_status',$storeId_site);
        $production_state = Mage::getStoreConfig('general/gst_options/gst_state', $store_id);
        $tax = Mage::getModel('sales/order_tax')->load($id, 'order_id');
        $tax_amount =  $tax->getAmount();
        if(strcasecmp($shipping_city,$production_state) == 0 ){
            $tax_finale = $tax_amount/2;
            $tax_Igst = 0;
        }else{
            $tax_finale = 0;
            $tax_Igst = $tax_amount;
        }
        if($gst_status == 1) {
            $tax->setIgst($tax_Igst)
                ->setCgst($tax_finale)
                ->setSgst($tax_finale);
            $tax->save();
        }
    }

    public function gstCategoryAddTab($observer){
        $observer->getTabs()->addTab('gst_configuration', array(
            'label'     => Mage::helper('catalog')->__('GST Price Rules'),
            'content'   => Mage::app()->getLayout()->createBlock(
                'codilar_gst/adminhtml_catalog_category_tab_gstPriceRules',
                'category.gst.price.rules'
            )->toHtml(),
        ));
    }

    public function gstCategorySaveAfter($observer){
        $formData = $observer->getEvent()->getRequest()->getPost(Codilar_Gst_Block_Adminhtml_Catalog_Category_Tab_GstPriceRules::FORM_ELEMENT_ARRAY_NAME);
        if($formData){
            $data = Mage::helper('core')->jsonEncode($formData);
            $observer->getEvent()->getCategory()->setData('gst_price_rules', $data);
        }
    }

    public function gstProductTaxClassSet($observer){
        $item = $observer->getEvent()->getItem();
        //echo $item->getQty()
        $result = $observer->getEvent()->getResult();
        $discountperItem = $result->getDiscountAmount()/$item->getQty();
        $priceAfterDiscount = $item->getPrice() - $discountperItem;
        $categories = Mage::getModel('catalog/category')->getCollection()->addAttributeToFilter('entity_id', array('in' => $item->getProduct()->getCategoryIds()))->addAttributeToSelect('gst_price_rules');
        foreach($categories as $category){
            if($priceRules = $category->getGstPriceRules()){
                try{
                    $priceRules = Mage::helper('core')->jsonDecode($priceRules);
                    foreach ($priceRules['price_min'] as $key => $minPrice) {
                        if($priceAfterDiscount >= $minPrice && $priceAfterDiscount <= $priceRules['price_max'][$key]){
                            $item->getProduct()->setTaxClassId($priceRules['tax_class'][$key]);
                            break;
                        }
                    }
                }
                catch(Exception $e){
                    
                }
            }
        }
    }
}