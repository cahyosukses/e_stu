<?php

class parents extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'master/parents' );
    }
	
	function grid() {
		$_POST['is_edit'] = 1;
		$_POST['column'] = array( 'p_father_name', 'p_father_email', 'p_father_cell', 'p_mother_name' );
		
		$array = $this->parents_model->get_array($_POST);
		$count = $this->parents_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// result default
		$result = array( 'status' => false );
		
		if ($action == 'update') {
			$result = $this->parents_model->update($_POST);
		} else if ($action == 'delete') {
			$result = $this->parents_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}