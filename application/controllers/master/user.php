<?php

class user extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'master/user' );
    }
	
	function grid() {
		$_POST['is_edit'] = 1;
		$_POST['column'] = array( 'user_type_title', 'user_display', 'user_email', 'phone' );
		
		$array = $this->user_model->get_array($_POST);
		$count = $this->user_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// result default
		$result = array( 'status' => false );
		
		if ($action == 'update') {
			// unset password if empty
			if (empty($_POST['user_pword'])) {
				unset($_POST['user_pword']);
			}
			
			// update
			$result = $this->user_model->update($_POST);
		} else if ($action == 'delete') {
			$result = $this->user_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}