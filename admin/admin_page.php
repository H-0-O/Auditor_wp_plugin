<?php

namespace user_mistake\Admin;

use user_mistake\App\DataBase;

class Admin_Page
{
	private static $instance = null;

	public function __construct()
	{
		if (is_admin()) {
			add_action('admin_menu', [$this, 'admin_setting_page']);
			add_action('admin_enqueue_scripts', [$this, 'add_files']);
		}
	}
	public function add_files()
	{
		wp_enqueue_style('boot-strap', "//cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css", [], false, 'all');
		wp_enqueue_script('bootstrap-js' , "//cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" , [] , false , true);
		wp_enqueue_script('admin-js', PLUGIN_MAIN_DIR . '/assets/js/admin_script.js', ['jquery'], false, true);
		wp_localize_script('admin-js', 'ajaxObject', ['url' => admin_url('admin-ajax.php')]);
	}

	public function admin_setting_page()
	{
		add_submenu_page('themes.php', 'رخداد های سایت', 'خداد های سایت', 'manage_options', 'auditor', [$this, 'admin_page']);

	}


	public function admin_page()
	{
		echo $this->admin_page_modal_box();
		echo $this->admin_page_html();
		Table::create_tables(DataBase::get_post_types());

	}

	private function admin_page_html()
	{
		$post_types = DataBase::get_post_types();
		$html = "<div class='container'>";
		$html .= "<div class='user-mik_button'>";
		if ($post_types != null) {
			foreach ($post_types as $type) {
				$name = __($type['post_type'], 'auditor');
				$html .= "<p class='btn btn-primary'> $name </p>";
			}
		}
		$html .= "</div> </div>";
		return $html;
	}

	private function admin_page_modal_box()
	{
		$html = <<<MODAL
			<div class="modal fade " id="detail" tabindex="-1" aria-labelledby="detail-label" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detail-label"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
						   <table  class="table table-dark modal-table">
						      		<thead class="modal-head"><tr><th id="key-name" scope="col">آپشن</th><th id="before-change" scope="col">از</th><th id="mark" scope="col">به</th><th id="after-change" scope="col">تغییر</th><th id="etc" scope="col">کرده است</th></tr></thead>
						      		<tbody class="table-hover"></tbody>
							</table >
        									
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
MODAL;
	return $html;
	}

	public static function get_instance()
	{
		if (Admin_Page::$instance == null) {
			Admin_Page::$instance = new Admin_Page();
		}
		return Admin_Page::$instance;
	}

}