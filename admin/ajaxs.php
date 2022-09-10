<?php

namespace user_mistake\Admin;


	use user_mistake\App\Data_base;

	class Ajaxs
	{
		private static $instance = null;

		public function __construct()
		{
			add_action('wp_ajax_insert_per_page' , [$this , 'insert_per_page']);
			add_action('wp_ajax_get_json' , [$this , 'get_modal_content']);
		}

		public function insert_per_page()
		{
			Data_base::insert_per_page($_POST['per_page']);
		}

		public function get_modal_content()
		{
			$data = Table::generate_modal_body($_POST['changes_id'] , $_POST['type']);
			die($data);
		}


		public static function get_instance()
		{
			if(Ajaxs::$instance == null)
			{
				Ajaxs::$instance = new Ajaxs();
			}
			return Ajaxs::$instance;
		}

	}

