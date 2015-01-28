<?php

class student extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'master/student' );
    }
	
	function grid() {
		$_POST['is_edit'] = 1;
		$_POST['column'] = array( 's_name', 'father_name', 'mother_name', 'quran_level_name', 'class_level_name' );
		
		$array = $this->student_model->get_array($_POST);
		$count = $this->student_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// result default
		$result = array( 'status' => false );
		
		if ($action == 'update') {
			$result = $this->student_model->update($_POST);
		} else if ($action == 'delete') {
			$result = $this->student_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}