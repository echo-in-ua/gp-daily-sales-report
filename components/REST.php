<?php

namespace GPDailyReport\components;
require_once 'Logger.php';
require_once 'Report.php';

class REST
{
	private $logger;
	private $token;

	public function __construct()
	{
		$this->token = esc_attr( get_option('gp_daily_sales_report_api_token') );
		$this->logger = new Logger();
		add_action('rest_api_init',[$this,'init']);
	}

	public function init()
	{
		register_rest_route( 'gp-daily-report/v1', '/sales_report', [
			'methods' => 'GET',
			'callback' => [$this, 'salesReport'],
			'permission_callback' => [$this, 'checkBearer']
		]);

	}


	public function salesReport($request)
	{
		$this->logger->write_log('Request gp-daily-report/v1/sales_report from '.$_SERVER['REMOTE_ADDR']);
		if ( $request->has_param('date') )
		{
			$queryParams = $request->get_query_params();
			$date = $queryParams['date'];
			$report = new Report($date);
			$this->logger->write_log($report->dailySalesReport());
			
			return $report->dailySalesReport();
		}
		return new \WP_Error ('Bad input parameters','Mondatory "date" parameter in format "Y-m-d".',array( 'status' => 400 )); 		
	}

	public function checkBearer($request)
	{
		$autorization = explode(' ',$request->get_header('Authorization'),2);
		$autorizationType = $autorization[0];
		$autorizationToken = $autorization[1];

		return ( $autorizationType === 'Bearer' && $autorizationToken === $this->token ) ? true : false; 
	}
	
}