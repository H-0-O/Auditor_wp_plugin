<?php

namespace user_mistake\App;


class Posts
{
	private static $instance = null, $data = null, $post_id = null, $post_type = null;

	private function __construct()
	{
		// action for run when user change anything
		add_action('pre_post_update', [$this, 'changes'], 1, 2);
		add_action('wp_insert_post', [$this, 'get_new_meta_data_and_insert'], 1  , 3);
	}

	public function changes($post_id, $new_data)
	{
		Posts::$post_id = $post_id;
		Posts::$post_type = $new_data['post_type'];
		Posts::$data['previous']['post'] = $this->get_previous_info_post(Posts::$post_id);
		Posts::$data['previous']['post_meta'] = $this->get_previous_info_post_meta(Posts::$post_id);
		Posts::$data['new']['post'] = $new_data;
	}

	#--[اطلاعات پست ، قبل از اپدیت رو از دیتا بیس میگیره]--#
	private function get_previous_info_post($post_id)
	{
		global $wpdb;
		$post_table = $wpdb->prefix . "posts";
		$query = $wpdb->prepare("SELECT * FROM `$post_table` WHERE ID = %d ", Posts::$post_id);
		return $wpdb->get_row($query, "ARRAY_A");


	}
	/**
	 * @param $post_id
	 *
	 * [اطلاعات پست متا قبل از اپدیت رو از دیتا بیس میگیره ] *
	 * @return array|object|\stdClass[]|null
	 */
	#--[اطلاعات پست متا قبل از اپدیت رو از دیتا بیس میگیره ]--#
	private function get_previous_info_post_meta($post_id)
	{
		global $wpdb;
		$post_table = $wpdb->prefix . "postmeta";
		$query = $wpdb->prepare("SELECT meta_key , meta_value FROM `$post_table` WHERE post_id = %d ", Posts::$post_id);
		return $wpdb->get_results($query, "ARRAY_A");
	}


	public function get_new_meta_data_and_insert()
	{
		global $wpdb;
		$post_table = $wpdb->prefix . "postmeta";
		$query = $wpdb->prepare("SELECT meta_key , meta_value FROM `$post_table` WHERE post_id = %d ", Posts::$post_id);
		$result = $wpdb->get_results($query, "ARRAY_A");
		Posts::$data['new']['post_meta'] = $result;

		$this->check_change_data();

	}

	private function check_change_data()
	{
		$json = [];
		//post data
		$keys = array_keys(Posts::$data['previous']['post']);
		foreach ($keys as $key)
		{
			if(Posts::$data['previous']['post'][$key] != Posts::$data['new']['post'][$key])
			{
				$json['post'][$key]['before_change'] = Posts::$data['previous']['post'][$key];
				$json['post'][$key]['after_change'] = Posts::$data['new']['post'][$key];
			}
		}

		for($i = 0 ; $i < count(Posts::$data['previous']['post_meta']) ; $i++)
		{
			if(Posts::$data['previous']['post_meta'][$i]['meta_value'] != Posts::$data['new']['post_meta'][$i]['meta_value'])
			{
				$json['post_meta'] [ Posts::$data['previous']['post_meta'][$i]['meta_key'] ]['before_change'] =  Posts::$data['previous']['post_meta'][$i]['meta_value'];
				$json['post_meta'] [ Posts::$data['previous']['post_meta'][$i]['meta_key'] ]['after_change'] =  Posts::$data['new']['post_meta'][$i]['meta_value'];
			}
		}

		$this->inset_to_data_base(json_encode($json));
		$this->rest_property();
	}

	private function rest_property()
	{
		Posts::$post_id = null;
		Posts::$post_type = null;
		Posts::$data = null;
	}

	private function inset_to_data_base($json)
	{

		$data =
			[
				'post_id' => Posts::$post_id,
				'post_type' => Posts::$post_type,
				'user_id' => get_current_user_id(),
				'user_ip' => $this->getUserIP(),
				'changes' => $json,
				'edit_date' => date("Y-m-d H:i:s ")
			];
		Data_base::insert_table($data);
		$this->reset_json();
	}
	
	private function reset_json()
	{
		Posts::$data = [];
	}

	private function getUserIP()
	{
		// Get real visitor IP behind CloudFlare network
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
			$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}
		$client = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote = $_SERVER['REMOTE_ADDR'];

		if (filter_var($client, FILTER_VALIDATE_IP)) {
			$ip = $client;
		} elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}

		return $ip;
	}

	public static function get_instance()
	{
		if (Posts::$instance == null) {
			Posts::$instance = new Posts();
		}
		return Posts::$instance;
	}
}

