<?php

namespace GPDailyReport\components;
require_once 'Order.php';

class Report

{
	private string $date;
	private $dateFormat = 'Y-m-d';
	private array $wcOrders;
	private array $orders;
	private array $cashiers;
	private array $cashierAccumulationRegister;
	private array $dailySalesReport;

	public function __construct($date)
	{
		$this->setDate($date);
		$this->fetchOrders();
		if ( empty($this->wcOrders) )
		{
			$this->buildEmptyReport();
		} else
		{
			$this->buildOrders();
			$this->buildReport();
		}
	}

	private function setDate($dateString): void
	{
		$date = \DateTime::createFromFormat($this->dateFormat, $dateString);
		if ($dateString === $date->format($this->dateFormat))
		{
			$this->date = $dateString;
		} else {
			throw new \Exception('Format for date "'.$dateString.'" is not valid. Expect format is "'.$this->dateFormat.'"', 1);
			
		}
	}

	private function dayRangeInTimestamp(): string
	{
		// Bug in wc_get_orders. If use raw date like '2020-06-13' return results for day before …
		$dayStart = strtotime($this->date);
		$dayEnd = strtotime($this->date.' +1 day -1 second');
		return $dayStart.'...'.$dayEnd;
	}
	private function fetchOrders(): void
	{
		$args = [
			'limit'=>-1,
			'type'=> 'shop_order',
		    'status'=> ['wc-completed'],
		    'date_completed' => $this->dayRangeInTimestamp()	
		];
		$this->wcOrders = wc_get_orders($args);
	}

	private function buildOrders(): void
	{
		foreach ($this->wcOrders as $wcOrder) {
			$order = new Order($wcOrder);
			$this->orders[] = $order;
		}
	}

	private function buildReport(): void
	{
		array_walk($this->orders,[$this, 'processSingleOrderToReport']);
		
		$this->cashiers = array_map(
		
			[$this,'processCashierAccumulationRegister'],
			array_keys($this->cashierAccumulationRegister), 
			$this->cashierAccumulationRegister
		
		);

		$this->dailySalesReport = [
			'date' 		=> $this->date,
			'cashiers' 	=> $this->cashiers
		];
	}
	private function buildEmptyReport(): void
	{
		$this->dailySalesReport = [
			'date' 		=> $this->date,
			'cashiers' 	=> []
		];
	}
	private function processCashierAccumulationRegister($salesPersonName, $register): array
	{
		return [
			'sales_person_name' 			=> $salesPersonName,
			'orders_count'					=> $register['orders_count'],
			'sub_total' 					=> round($register['sub_total'],2),
			'discount_final_amount' 		=> round($register['discount_final_amount'],2),
			'grand_total' 					=> round($register['grand_total'],2),
			'average_order_value' 			=> round($register['average_order_value']/$register['orders_count'],2),
			'average_items_per_order' 		=> round($register['average_items_per_order']/$register['orders_count'],1)
		];
	}
	private function processSingleOrderToReport($order): void
	{
		$name = $order->getSalesPersonName();
		if ( $this->isCasherInRegister($order->getSalesPersonName()) )
		{
			$this->cashierAccumulationRegister[$name] = [
				'orders_count' 				=> $this->addValue($name,'orders_count',1),	
				'sub_total' 				=> $this->addValue($name,'sub_total',$order->getSubTotal()),				
				'discount_final_amount' 	=> $this->addValue($name,'discount_final_amount',$order->getDiscountFinalAmount()),
				'grand_total'				=> $this->addValue($name,'grand_total',$order->getGrandTotal()),
				'average_order_value'		=> $this->addValue($name,'average_order_value',$order->getSubTotal()),
				'average_items_per_order'	=> $this->addValue($name,'average_items_per_order',$order->getItemsCount()),
			];
		} else
		{
			$this->cashierAccumulationRegister[$name] = [
				'orders_count' 				=> 1,
				'sub_total' 				=> $order->getSubTotal(),
				'discount_final_amount' 	=> $order->getDiscountFinalAmount(),
				'grand_total' 				=> $order->getGrandTotal(),
				'average_order_value'		=> $order->getSubTotal(),
				'average_items_per_order'	=> $order->getItemsCount()
			];
		}
	}

	private function addValue($target,$key,$value)
	{
		return $this->cashierAccumulationRegister[$target][$key] + $value;
	} 
	private function isCasherInRegister(string $salesPersonName): bool
	{
		return ( !empty($this->cashierAccumulationRegister) && array_key_exists($salesPersonName, $this->cashierAccumulationRegister) ) ? true : false;
	}

	public function dailySalesReport(): array
	{
		return $this->dailySalesReport;
	}

	// public function getOrders(): array
	// {
	// 	return $this->wcOrders;
	// }
	// public function getOrdersCount(): int
	// {
	// 	return count($this->wcOrders);
	// }
	// public function getWcOrderById(int $id): \WC_Order
	// {
	// 	foreach ($this->wcOrders as $wcOrder) {
	// 		if ( $wcOrder->get_id() === $id ) return $wcOrder;
	// 	}
	// }
	// public function dateTest(): string
	// {
	// 	$str = 'Report date "'.$this->date.'"'.PHP_EOL;
	// 	$str .= 'Search date range is "'.$this->dayRangeInTimestamp().'"'.PHP_EOL;
	// 	foreach ($this->wcOrders as $order) {
	// 		$str .=$order->get_id().' - '. $order->get_date_completed();
	// 		$str .=PHP_EOL;
	// 	}
	// 	return $str;

	// }

	// public function cashierAccumulationRegister(): array
	// {
	// 	return $this->cashierAccumulationRegister;
	// }
	// public function cashiers(): array
	// {
	// 	return $this->cashiers;
	// }
}

?>