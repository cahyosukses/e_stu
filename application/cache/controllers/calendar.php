<?php

class calendar extends SE_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'calendar' );
    }
	
	function grid() {
		// user
		$user = $this->user_model->get_session();
		if ($user['user_type_id'] == USER_TYPE_ADMINISTRATOR) {
			$_POST['is_edit'] = 1;
		} else {
			$_POST['is_detail'] = 1;
		}
		$_POST['column'] = array( 'user_display', 'start_date_swap', 'title' );
		
		$array = $this->calendar_model->get_array($_POST);
		$count = $this->calendar_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// user
		$user = $this->user_model->get_session();
		
		// result default
		$result = array( 'status' => false );
		
		if ($action == 'update') {
			// add user id
			$_POST['user_id'] = $user['user_id'];
			
			// update
			$result = $this->calendar_model->update($_POST);
		} else if ($action == 'delete') {
			$result = $this->calendar_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}