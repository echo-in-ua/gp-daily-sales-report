<?php

namespace GPDailyReport\components;

class Settings
{
    public function __construct()
    {
        add_action('admin_init',[$this,'addSettings']);
        add_action('admin_menu', [$this,'addOptionsPage']);
    }

    public function addOptionsPage()
    {
        add_options_page( 'Daily Sales Report Options', 'Daily Sales Report', 'manage_options', 'gp_daily_sales_report_settings_page',[$this,'optionsPageRender']);
    }
    
    public function addSettings(){
        register_setting('gp_daily_sales_report_settings_group','gp_daily_sales_report_api_token');
        
        add_settings_section('gp_daily_sales_report_settings_options','Налаштування токену для API.',[$this,'tokenOptionsRender'],'gp_daily_sales_report_settings_page');
        
        add_settings_field('gp_daily_sales_report_api_token','Daily Sales Report Report API token', [$this, 'apiTokenFieldRender'],'gp_daily_sales_report_settings_page', 'gp_daily_sales_report_settings_options');
    }

    public function apiTokenFieldRender()
    {
        $apiToken = esc_attr( get_option('gp_daily_sales_report_api_token') );
        $html = '<input type="text" name="gp_daily_sales_report_api_token" value="'.$apiToken.'" placeholder="API Bearer token" /> ';
        echo $html;
    }

    public function tokenOptionsRender(): void
    {
        
    }

    public function optionsPageRender(): void
    {
        $html ='<form method="post" action="options.php">';
        echo $html;
        settings_fields( 'gp_daily_sales_report_settings_group' );
        do_settings_sections('gp_daily_sales_report_settings_page');
        submit_button();
        $html='</form>';
        echo $html;
    }

    
}
