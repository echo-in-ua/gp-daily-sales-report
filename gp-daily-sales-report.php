<?php
/*
Plugin Name: GP Daily Sales Report
Plugin URI:
Description: Add REST end-point for daily sales reports for salary calculation
Version: 1.0
Author: GP
*/
require_once 'components/Logger.php';
require_once 'components/Report.php';
require_once 'components/REST.php';
use GPDailyReport\components\Logger;
use GPDailyReport\components\Report;
use GPDailyReport\components\REST;

class GPDailyReport
{
	private static $instance = null;
	public $log;

	private function __construct()
	{
		$this->log = new Logger();
		new REST(); 
	}

	static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new GPDailyReport();
        return self::$instance;
    } 
}

add_action('wp_loaded', function ()  {
   $app = GPDailyReport::getInstance();
});

?>