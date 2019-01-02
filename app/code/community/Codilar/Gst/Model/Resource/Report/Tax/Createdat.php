<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    GST Module
 * @package     GST
 * @copyright   Copyright (c) 20170 .Codilar. (http://www.codilar.com)
 * @purpose     [BRIEF ABOUT THE FILE]
 * @author       Codilar Team
 **/

class Codilar_Gst_Model_Resource_Report_Tax_Createdat extends Mage_Tax_Model_Resource_Report_Tax_Createdat
{
    /**
     * Aggregate Tax data by orders
     *
     * @throws Exception
     * @param string $aggregationField
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Tax_Model_Resource_Report_Tax_Createdat
     */
    protected function _aggregateByOrder($aggregationField, $from, $to)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('sales/order'),
                    'created_at', 'updated_at', $from, $to
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($this->getMainTable(), $from, $to, $subSelect);
            // convert dates from UTC to current admin timezone
            $periodExpr = $writeAdapter->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    array('e' => $this->getTable('sales/order')),
                    'e.' . $aggregationField,
                    $from, $to
                )
            );

            $columns = array(
                'period'                => $periodExpr,
                'store_id'              => 'e.store_id',
                'code'                  => 'tax.code',
                'order_status'          => 'e.status',
                'percent'               => 'MAX(tax.' . $writeAdapter->quoteIdentifier('percent') .')',
                'orders_count'          => 'COUNT(DISTINCT e.entity_id)',
                'tax_base_amount_sum'   => 'SUM(tax.base_amount * e.base_to_global_rate)',
                'total_cgst'            => 'SUM(tax.cgst)',
                'total_sgst'            => 'SUM(tax.sgst)',
                'total_igst'            => 'SUM(tax.igst)'
            );

            $select = $writeAdapter->select();
            $select->from(array('tax' => $this->getTable('tax/sales_order_tax')), $columns)
                ->joinInner(array('e' => $this->getTable('sales/order')), 'e.entity_id = tax.order_id', array())
                ->useStraightJoin();

            $select->where('e.state NOT IN (?)', array(
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage_Sales_Model_Order::STATE_NEW
            ));

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array($periodExpr, 'e.store_id', 'code', 'tax.percent', 'e.status'));

            $insertQuery = $writeAdapter->insertFromSelect($select, $this->getMainTable(), array_keys($columns));
            $writeAdapter->query($insertQuery);

            $select->reset();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr(Mage_Core_Model_App::ADMIN_STORE_ID),
                'code'                  => 'code',
                'order_status'          => 'order_status',
                'percent'               => 'MAX(' . $writeAdapter->quoteIdentifier('percent') . ')',
                'orders_count'          => 'SUM(orders_count)',
                'tax_base_amount_sum'   => 'SUM(tax_base_amount_sum)',
                'total_cgst'            => 'SUM(total_cgst)',
                'total_sgst'            => 'SUM(total_sgst)',
                'total_igst'            => 'SUM(total_igst)'
            );

            $select
                ->from($this->getMainTable(), $columns)
                ->where('store_id <> ?', 0);

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array('period', 'code', 'percent', 'order_status'));
            $insertQuery = $writeAdapter->insertFromSelect($select, $this->getMainTable(), array_keys($columns));
            $writeAdapter->query($insertQuery);
            $writeAdapter->commit();
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        return $this;
    }
}
