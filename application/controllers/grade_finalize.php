<?php

class grade_finalize extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'grade_finalize' );
    }
	
	function grid() {
		/*
		$grid_type = (isset($_POST['grid_type'])) ? $_POST['grid_type'] : 'teacher';
		
		if ($grid_type == 'teacher') {
			$_POST['is_custom']  = '<span class="cursor-font-awesome icon-pencil btn-edit" title="Edit"></span> ';
			$_POST['is_custom'] .= '<span class="cursor-font-awesome icon-plus-sign-alt btn-update-score" title="Update Grade"></span> ';
			$_POST['is_custom'] .= '<span class="cursor-font-awesome icon-trash btn-delete" title="Delete"></span> ';
			$_POST['column'] = array( 'task_type_name', 'user_display', 'title', 'due_date_swap' );
			
			$array = $this->task_model->get_array($_POST);
			$count = $this->task_model->get_count();
		}
		else if ($grid_type == 'parent') {
			$_POST['is_detail'] = 1;
			$_POST['column'] = array( 'class_type_name', 'task_type_name', 'task_title', 'task_due_date_swap', 'grade' );
			
			$array = $this->task_class_model->get_array($_POST);
			$count = $this->task_class_model->get_count();
		}
		
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		echo json_encode($grid);
		/*	*/
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// user
		$user = $this->user_model->get_session();
		$user_type = $this->user_type_model->get_by_id(array( 'id' => $user['user_type_id'] ));
		
		// result default
		$result = array( 'status' => false );
		
		/*
		// task
		if ($action == 'update') {
			// insert or update
			$is_insert = (empty($_POST['id'])) ? true : false;
			
			// add user id
			$_POST['assign_by'] = $user['user_id'];
			
			// check attachment
			$array_attachment = array();
			$_POST['array_attachment'] = (isset($_POST['array_attachment'])) ? $_POST['array_attachment'] : array();
			foreach ($_POST['array_attachment'] as $raw) {
				$row = object_to_array(json_decode($raw));
				$array_data = array( 'file_name' => $row['file_name'], 'file_only' => $row['file_only'] );
				$array_attachment[] = $array_data;
			}
			$_POST['attachment'] = json_encode($array_attachment);
			if (isset($_POST['array_attachment'])) {
				unset($_POST['array_attachment']);
			}
		}
		else if ($action == 'delete') {
			$result = $this->task_model->delete($_POST);
		}
		/*	*/
		
		echo json_encode($result);
	}
}
