<?php
namespace user_mistake\admin;

use user_mistake\App\DataBase;

class Table
{
	private static $instance = null;

	#--[تیبل رو میسازه]--#
	public function __construct()
	{


	}

	public static function create_tables($post_types)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . TABLE_NAME;
		foreach ($post_types as $post_type) {
			$html = null;
			$query = null;
			$html .= "<table id='{$post_type['post_type']}'  class='table table-dark' > ";
			$query = "SELECT * FROM `$table_name` WHERE post_type = '{$post_type['post_type']}' ";
			$result = $wpdb->get_results($query, "ARRAY_A");
			$html .= "<thead> <tr><th scope='col'>نام کاربر</th><th scope='col'>آی پی یوزر</th><th scope='col'>ایدی پست</th><th scope='col'>تغییر داده شده</th> <th scope='col'>زمان تغغیر</th></tr> </thead>";
			$html .= "<tbody>";
			foreach ($result as $row) {
				$html .= "<tr>";
				$user = $wpdb->get_row("SELECT display_name , user_url FROM {$wpdb->prefix}users WHERE ID = {$row['user_id']} ", 'ARRAY_A');
				$html .= "<td><a href='{$user['user_url']}' > {$user['display_name']} </a> </td>";
				$html .= "<td>{$row['user_ip']}</td>";
				$html .= "<td>{$row['post_id']}</td>";
				$html .= "<td><button data-id='{$row['id']}' data-type='{$post_type['post_type']}' class='btn btn-primary show_details' data-bs-toggle='modal' data-bs-target='#detail'>".__("نمایش جزئیات" , "user_mistake")."</button></td>";
				$html .= "<td>{$row['edit_date']}</td>";
				$html .= "</tr>";
			}
			$html .= "</tbody>";
			echo $html;
		}
	}

	public static function generate_modal_body($id , $type)
	{
		switch ($type)
		{

			case 'option':
				return self::generate_option($id);
				break;
			default:
				return self::generate_post($id);
				break;
		}
	}

	private static function generate_post($id)
	{
		$data = DataBase::get_json($id);
		$post_keys = array_keys($data['post']);
		$html = "";
		foreach($post_keys as $key)
		{
			$html .= "<tr>";
			$html .= "<th scope='row'>$key</th>";
			$html .= "<td>{$data['post'][$key]['before_change']}</td>";
			$html .= "<td>-></td>";
			$html .= "<td>{$data['post'][$key]['after_change']}</td>";
			$html .= "<td><p class='restore btn btn-primary'>".__("بازگردانی", 'user_mistake')."</p></td>";
			$html .= "</tr>";
		}

		$post_meta_keys = array_keys($data['post_meta']);
		foreach ($post_meta_keys as $key_meta)
		{
			$html .= "<tr>";
			$html .= "<th scope='row'>$key_meta</th>";
			$html .= "<td>{$data['post_meta'][$key_meta]['before_change']}</td>";
			$html .= "<td>-></td>";
			$html .= "<td>{$data['post_meta'][$key_meta]['after_change']}</td>";
			$html .= "<td><p class='restore btn btn-primary'>".__("بازگردانی", 'user_mistake')."</p></td>";
			$html .= "</tr>";
		}
		return $html;
	}

	private static function generate_option($id)
	{
		$data = DataBase::get_json($id);

		$post_keys = array_keys($data);
		$html = "";
		foreach($post_keys as $key)
		{
			$html .= "<tr>";
			$html .= "<th scope='row'>$key</th>";
			$html .= "<td>{$data[$key]['before_change']}</td>";
			$html .= "<td>-></td>";
			$html .= "<td>{$data[$key]['after_change']}</td>";
			$html .= "<td><p class='restore btn btn-primary'>".__("بازگردانی", 'user_mistake')."</p></td>";
			$html .= "</tr>";
		}

		return $html;
	}
	public static function get_instance()
	{
		if(Table::$instance == null)
		{
			Table::$instance = new Table();
		}

		return Table::$instance;
	}

}
