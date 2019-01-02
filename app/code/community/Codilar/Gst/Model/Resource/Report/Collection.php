<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    GST Module
 * @package     GST
 * @copyright   Copyright (c) 20170 .Codilar. (http://www.codilar.com)
 * @purpose     [BRIEF ABOUT THE FILE]
 * @author       Codilar Team
 **/

class Codilar_Gst_Model_Resource_Report_Collection extends Mage_Tax_Model_Resource_Report_Collection
{
    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_periodFormat;

    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'tax/tax_order_aggregated_created';

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_selectedColumns    = array();

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    /**
     * @return array|unknown
     */
    protected function _getSelectedColumns()
    {
        if ('month' == $this->_period) {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y');
        } else {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y-%m-%d');
        }

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->_selectedColumns = array(
                'period'                => $this->_periodFormat,
                'code'                  => 'code',
                'percent'               => 'percent',
                'orders_count'          => 'SUM(orders_count)',
                'tax_base_amount_sum'   => 'SUM(tax_base_amount_sum)',
                'total_cgst'            => 'SUM(total_cgst)',
                'total_sgst'            => 'SUM(total_sgst)',
                'total_igst'            => 'SUM(total_igst)'
            );
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        if ($this->isSubTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns() + array('period' => $this->_periodFormat);
        }

        return $this->_selectedColumns;
    }

}
