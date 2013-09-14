<?php

class Itweb_Installments_Model_Sales_Order_Invoice_Item
    extends Mage_Sales_Model_Order_Invoice_Item
{

    /**
     * Declare qty
     *
     * @param   float $qty
     *
     * @return  Mage_Sales_Model_Order_Invoice_Item
     */
    public function setQty($qty)
    {
        $Order = $this->getOrderItem()->getOrder();
        if ($Order->getUseInstallments() > 0 && $this->getOrderItem()->getParentItemId() <= 0) {
            if ($qty > 0) {
                $Breakdown = Itweb_Installments_Helper_Data::getInstallmentBreakdown($Order);
                if (!isset($Breakdown[$this->getOrderItem()->getId()])) {
                    Mage::throwException('Item ' . $this->getOrderItem()->getName() . ' Is Not In THe Original Order!');
                }
            }

            /**
             * If it's an installment, ignore the decimal setting because we need
             * to invoice parts of a product in order for things to work correctly
             */
            $qty = (float)$qty;
            $qty = $qty > 0 ? $qty : 0;
            /**
             * Check qty availability
             */
            $qtyToInvoice = sprintf("%.4F", $this->getOrderItem()->getQtyToInvoice());
            /*$qty = sprintf("%.4F", $qty);*/
            if (sprintf("%.4F", $qty) <= $qtyToInvoice || $this->getOrderItem()->isDummy()) {
                $this->setData('qty', $qty);
            } else {
                Mage::throwException(
                    Mage::helper('sales')->__('Invalid qty to invoice item "%s"', $this->getName())
                );
            }
            return $this;
        } else {
            return parent::setQty($qty);
        }
    }
}
