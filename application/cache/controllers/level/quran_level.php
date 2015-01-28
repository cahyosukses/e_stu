<?php

class quran_level extends SE_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'level/quran_level' );
    }
	
	function grid() {
		$_POST['is_edit'] = 1;
		$_POST['column'] = array( 'name', 'no_order' );
		
		$array = $this->quran_level_model->get_array($_POST);
		$count = $this->quran_level_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// result default
		$result = array( 'status' => false );
		
		if ($action == 'update') {
			$result = $this->quran_level_model->update($_POST);
		} else if ($action == 'delete') {
			$result = $this->quran_level_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}