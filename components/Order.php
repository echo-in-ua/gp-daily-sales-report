<?php

namespace GPDailyReport\components;

class Order
{
	protected $wcOrder;

	public function __construct (\WC_Order $order)
	{
		$this->wcOrder = $order; 
	}

	private function opMeta(): array
	{
		return $this->wcOrder->get_meta('_op_order');
	}
	
	public function getSalesPersonName(): string
	{
		return $this->opMeta()['sale_person_name'];
	}
	
	public function getItemsCount(): int
	{
		return count($this->opMeta()['items']);
	}

	public function getSubTotal(): float
	{
		return $this->opMeta()['sub_total'];
	}

	public function getDiscountFinalAmount(): float
	{
		return $this->opMeta()['discount_final_amount'];
	}

	public function getGrandTotal(): float
	{
		return $this->opMeta()['grand_total'];
	}
}

?>
