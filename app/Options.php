<?php

namespace user_mistake\App;

class Options
{
	private static $instance;

	public function __construct()
	{
		add_action('update_option' , [$this , 'run'] , 1 , 3);
	}

	public function run($option , $old_value , $new_value)
	{
		$block_names = ["_transient_doing_cron" , "action_scheduler_lock_async" , "cron" , "rewrite_rules" , "_transient_jetpack_autoloader_plugin_paths" , "woocommerce_admin_notices"];
		if(in_array($option , $block_names))
			return;
		if($option == "active_plugins")
		{
			$array = $this->get_plugins($old_value , $new_value);
			return ;

		}
		/*$the_array = [
			'option' => $option ,
			'old_val' => print_r($old_value),
			'new_val' => print_r($new_value)
		];*///$_POST['xts-woodmart-options']
		//xts-woodmart-options[sidebar_width]
		//$this->comparison_two_array($test_array_one , $the_array_two);
		//$result = $this->comparison_two_array($old_value , $new_value);
		if(is_array($old_value) && is_array($new_value))
		{
			$array  = $this->comparison_two_array($old_value , $new_value);
			$this->insert_to_data_base($array);

		}else{
			return;
		}

	}

	/**
	 * به صورت درختی دو ارایه رو مقایسه میکنه و به صورت داده قبل از تغییر و بعد از تغغیر برمی گردونه
	 * @var array
	 */
	private static $computes_array  = [] , $count_recursive;
	private function comparison_two_array($base_array ,$other_array)
	{
		$array_one_keys = array_keys($base_array);
		$array_two_keys = array_keys($other_array);
		$keys_base = (count($array_one_keys) >= count($array_two_keys)) ?  $array_one_keys : $array_two_keys;
		foreach ($keys_base as $key)
		{
			if(array_key_exists($key , $base_array) && array_key_exists($key , $other_array))
			{
				if(is_array($base_array[$key]) && is_array($other_array[$key]))
				{
					self::$count_recursive++;
					$this->comparison_two_array($base_array[$key] ,$other_array[$key]);
				}else
				{
					if(array_key_exists($key , self::$computes_array))
					{
						self::$computes_array[$key."_".self::$count_recursive]['before_change'] = $base_array[$key];
						self::$computes_array[$key."_".self::$count_recursive]['after_change'] = $other_array[$key];
					}else
					{
						if($base_array[$key] != $other_array[$key]) {
							self::$computes_array[$key]['before_change'] = $base_array[$key];
							self::$computes_array[$key]['after_change'] = $other_array[$key];
						}
					}
				}
			}else
			{
				if(array_key_exists($key , $base_array) && !array_key_exists($key , $other_array))
				{
					self::$computes_array[$key] = "deleted";
				}elseif (!array_key_exists($key , $base_array) && array_key_exists($key , $other_array))
				{
					self::$computes_array[$key] = $other_array[$key];
				}
			}


		}

		$final_array = self::$computes_array;
		$this->reset_property();
		return $final_array;

	}

	private function get_plugins($old , $new)
	{
		$flipped_old = array_flip($old);
		$flipped_new = array_flip($new);
		$old_keys = array_keys($flipped_old);
		$new_keys = array_keys($flipped_new);
		$stack_array = [];
		$keys = (count($old_keys) > count($new_keys)) ? $old_keys : $new_keys;
		foreach ($keys as $key)
		{
			if(array_key_exists($key , $flipped_old) && !array_key_exists($key , $flipped_new))
			{
				$stack_array[$key] = "deleted";
			}elseif (!array_key_exists($key , $flipped_old) && array_key_exists($key , $flipped_new))
			{
				$stack_array[$key] = "added";
			}
		}

		return $stack_array;
	}
	private function insert_to_data_base($json)
	{
		$data =
			[
				'post_id' => '-10' ,
				'post_type' => 'option' ,
				'user_id' => get_current_user_id() ,
				'user_ip' => $this->getUserIP(),
				'changes' => json_encode($json),
				'edit_date' =>  date("Y-m-d H:i:s ")
			];
		\user_mistake\App\Data_base::insert_table($data);
	}

	private function reset_property()
	{
		self::$computes_array = [];
		self::$count_recursive = 0;
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
		if (Options::$instance == null) {
			Options::$instance = new Options();
		}
		return Options::$instance;
	}
}
