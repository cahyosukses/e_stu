<?php

class report_card extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'report_card' );
    }
	
	/*
	function grid() {
		$_POST['default_value'] = false;
		$_POST['is_edit_only'] = 1;
		$_POST['column'] = array( 'name', 'quran_summary', 'figh_summary', 'akhlaq_summary', 'tareekh_summary' );
		
		$array = $this->student_model->get_grade($_POST);
		$count = count($array);
		
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// user
		$user = $this->user_model->get_session();
		$user_type = $this->user_type_model->get_by_id(array( 'id' => $user['user_type_id'] ));
		
		// result default
		$result = array( 'status' => false );
		
		// student
		if ($action == 'update_comment') {
			$result = $this->student_model->update($_POST);
		} else if ($action == 'get_student') {
			$result = $this->student_model->get_by_id(array( 's_id' => $_POST['student_id'] ));
		}
		
		echo json_encode($result);
	}
	/*	*/
}
