<?php

class teacher extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'teacher' );
    }
	
	function grid() {
		// user
		$user = $this->user_model->get_session();
		
		// student
		$student = $this->student_model->get_by_id(array( 's_id' => $user['student_id'] ));
		
		// column
		$_POST['column'] = array( 'user_display', 'phone', 'user_email' );
		
		// add parameter
		$_POST['with_subject'] = 1;
		$_POST['class_level_id'] = $student['class_level_id'];
		$_POST['quran_level_id'] = $student['quran_level_id'];
		$array = $this->user_model->get_array($_POST);
		$count = $this->user_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
}