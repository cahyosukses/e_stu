<?php

class config extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'master/config' );
    }
	
	function grid() {
		$_POST['is_edit'] = 1;
		$_POST['is_hidden'] = 0;
		$_POST['column'] = array( 'config_desc' );
		
		$array = $this->config_model->get_array($_POST);
		$count = $this->config_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// result default
		$result = array( 'status' => false );
		
		if ($action == 'update') {
			$result = $this->config_model->update($_POST);
		} else if ($action == 'delete') {
			$result = $this->config_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}