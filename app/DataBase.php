<?php

namespace user_mistake\App;

class DataBase
{
	private static $instance = null;

    public function __construct()
    {
        global $wpdb;
        $table_name = $wpdb->prefix.TABLE_NAME;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $check = $wpdb->prepare( 'SHOW TABLES LIKE %s ', $table_name );
            if(!$wpdb->get_var($check))
            {
                $query = "CREATE TABLE `$table_name` ( id BIGINT AUTO_INCREMENT , user_id BIGINT NOT NULL , user_ip VARCHAR(25) , post_id INT NOT NULL , post_type VARCHAR(30) ,  changes JSON NOT NULL , edit_date DATE , PRIMARY KEY(id)  )";
                dbDelta($query);

            }
    }

	/**
	 * @param $data
	 *
	 * @return void
	 */

    public static function insert_table($data = [ 'post_id' => '' ,'post_type' => '' , 'user_id'=> '' , 'user_ip' =>'' , 'changes'=> '' , 'edit_date' => '' ] )
    {
		global $wpdb;
		$table_name = $wpdb->prefix.TABLE_NAME;
		$wpdb->insert(
			"$table_name",
			$data
		);
    }

	/**
	 * #--[پست تاییپ ها رو میگیره و اینجوری میفهمیم چندتا جدول داریم ]--#
	 *
	 * @return array|object|stdClass[]|null
	 */

	public static function get_post_types()
	{
		global $wpdb;
		$table_name = $wpdb->prefix.TABLE_NAME;
		$sql = "SELECT DISTINCT post_type FROM `$table_name` ";
		return $wpdb->get_results($sql , "ARRAY_A");
	}

	public static function get_json($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix.TABLE_NAME;
		$sql = "SELECT changes FROM `$table_name` WHERE id = $id";
		return json_decode($wpdb->get_var($sql) , 1);
	}



	public static function get_instance()
	{
		if(DataBase::$instance == null)
		{
			DataBase::$instance = new DataBase();
		}
		return DataBase::$instance;
	}
}
