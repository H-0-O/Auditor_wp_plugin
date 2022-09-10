<?php

/**
 * Plugin Name: auditor
 * Author: Hossein Salehi
 * Text Domain: sh-auditor
 */
const TABLE_NAME  = 'auditor';
define('PLUGIN_MAIN_DIR' , plugin_dir_url(__FILE__));

require_once __DIR__.'/loader.php';

add_action('init',function ()
{
    \user_mistake\App\DataBase::get_instance();
	\user_mistake\Admin\Ajaxs::get_instance();
	\user_mistake\App\Posts::get_instance();
	\user_mistake\App\Options::get_instance();
	\user_mistake\Admin\Admin_Page::get_instance();
	\user_mistake\admin\Table::get_instance();

});
