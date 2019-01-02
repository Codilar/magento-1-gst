<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    GST Module
 * @package     GST
 * @copyright   Copyright (c) 20170 .Codilar. (http://www.codilar.com)
 * @purpose     [BRIEF ABOUT THE FILE]
 * @author       Codilar Team
 **/

class Codilar_Gst_Helper_Data extends Mage_Core_Helper_Abstract{

    public function orderDate(){
        $date = '1-7-2017';
        $lstdate = date('Y-m-d H:i:s', strtotime($date));
        return $lstdate;
    }

}