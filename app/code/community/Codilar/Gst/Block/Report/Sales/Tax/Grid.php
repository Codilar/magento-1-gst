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
 * Class Codilar_Gst_Block_Report_Sales_Tax_Grid
 */
class Codilar_Gst_Block_Report_Sales_Tax_Grid extends Mage_Adminhtml_Block_Report_Sales_Tax_Grid
{
    protected $_columnGroupBy = 'period';

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        parent::_prepareColumns();

        $this->addColumn('period', array(
            'header'            => Mage::helper('sales')->__('period'),
            'index'             => 'period',
            'width'             => '100',
            'sortable'          => false,
            'period_type'       => $this->getPeriodType(),
            'renderer'          => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'      => Mage::helper('sales')->__('Total'),
            'subtotals_label'   => Mage::helper('sales')->__('Subtotal'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('code', array(
            'header'    => Mage::helper('sales')->__('Tax'),
            'index'     => 'code',
            'type'      => 'string',
            'sortable'  => false
        ));

        $this->addColumn('percent', array(
            'header'    => Mage::helper('sales')->__('Rate'),
            'index'     => 'percent',
            'type'      => 'number',
            'width'     => '100',
            'sortable'  => false
        ));

        $this->addColumn('orders_count', array(
            'header'    => Mage::helper('sales')->__('Number of Orders'),
            'index'     => 'orders_count',
            'total'     => 'sum',
            'type'      => 'number',
            'width'     => '100',
            'sortable'  => false
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('tax_base_amount_sum', array(
            'header'        => Mage::helper('sales')->__('Total GST '),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'tax_base_amount_sum',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $this->getRate($currencyCode),
        ));

        $this->addColumn('total_cgst', array(
            'header'        => Mage::helper('sales')->__('CGST'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_cgst',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $this->getRate($currencyCode),
        ));

        $this->addColumn('total_sgst', array(
            'header'        => Mage::helper('sales')->__('SGST'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_sgst',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $this->getRate($currencyCode),
        ));

        $this->addColumn('total_igst', array(
            'header'        => Mage::helper('sales')->__('IGST'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_igst',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $this->getRate($currencyCode),
        ));


        $this->addExportType('*/*/exportTaxCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportTaxExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return $this;
    }
}
