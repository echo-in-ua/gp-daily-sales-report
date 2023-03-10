<?php

namespace GPDailyReport\components;
require_once 'Order.php';


class DefaultWooCommerceOrder extends Order 
{

	public function __construct (\WC_Order $order)
	{
		parent::__construct($order);
	}

	public function getSalesPersonName(): string
	{
		return "woocommerce";
	}
	
	public function getItemsCount(): int
	{
		return $this->wcOrder->get_item_count();
	}

	public function getSubTotal(): float
	{
		return $this->wcOrder->get_subtotal();
	}

	public function getDiscountFinalAmount(): float
	{
		return $this->wcOrder->get_discount_total();
	}

	public function getGrandTotal(): float
	{
		return  $this->wcOrder->calculate_totals();
	}
}

?>
