<?php

class task extends SE_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'task' );
    }
	
	function grid() {
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
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// user
		$user = $this->user_model->get_session();
		
		// result default
		$result = array( 'status' => false );
		
		// task
		if ($action == 'update') {
			// insert or update
			$is_insert = (empty($_POST['id'])) ? true : false;
			
			// add user id
			$_POST['assign_by'] = $user['user_id'];
			
			// update
			$result = $this->task_model->update($_POST);
			
			// add student
			if ($is_insert) {
				$param_student = array();
				if (!empty($_POST['quran_level_id'])) {
					$param_student['quran_level_id'] = $_POST['quran_level_id'];
				}
				if (!empty($_POST['class_level_id'])) {
					$param_student['class_level_id'] = $_POST['class_level_id'];
				}
				$array_student = $this->student_model->get_array($param_student);
				foreach ($array_student as $row) {
					$this->task_class_model->update(array( 'task_id' => $result['id'], 'student_id' => $row['s_id'] ));
				}
			}
		}
		else if ($action == 'delete') {
			$result = $this->task_model->delete($_POST);
		}
		
		// task class
		else if ($action == 'task_class_add') {
			$check = $this->task_class_model->get_by_id(array( 'task_id' => $_POST['task_id'], 'student_id' => $_POST['student_id'] ));
			if (count($check) > 0) {
				$result['message'] = 'Student already exist at current class.';
				echo json_encode($result);
				exit;
			}
			
			$result = $this->task_class_model->update($_POST);
		}
		else if ($action == 'task_class_update') {
			foreach ($_POST['array_grade'] as $raw_value) {
				$array_temp = explode(',', $raw_value);
				
				// update grade
				$param_grade = array( 'id' => $array_temp[0], 'grade' => $array_temp[1] );
				$result = $this->task_class_model->update($param_grade);
			}
		}
		else if ($action == 'task_class_delete') {
			$result = $this->task_class_model->delete($_POST);
		}
		
		// task grade
		else if ($action == 'task_type_info') {
			if ($user['user_type_id'] == USER_TYPE_TEACHER) {
				$teacher = $this->user_model->get_by_id(array( 'user_id' => $user['user_id'] ));
				if (!empty($teacher['json_meta'])) {
					$json_meta = object_to_array(json_decode($teacher['json_meta']));
					if (isset($json_meta['array_task_type'])) {
						$result['array_task_type'] = $json_meta['array_task_type'];
					}
				}
				
				// get default if still empty
				if (! isset($result['array_task_type'])) {
					$result['array_task_type'] = $this->task_type_model->get_array();
				}
			}
		}
		else if ($action == 'task_type_update') {
			if ($user['user_type_id'] == USER_TYPE_TEACHER) {
				$teacher = $this->user_model->get_by_id(array( 'user_id' => $user['user_id'] ));
				
				// generate task type
				$array_task_type = array();
				foreach($_POST['array_weight'] as $value) {
					list($task_type_id, $task_type_weight) = explode(',', $value);
					$task_type = $this->task_type_model->get_by_id(array( 'id' => $task_type_id ));
					$task_type['weight'] = $task_type_weight;
					unset($task_type['name']);
					$array_task_type[] = $task_type;
				}
				
				// update meta
				$param_update['user_id'] = $user['user_id'];
				$param_update['json_meta']['array_task_type'] = $array_task_type;
				$result = $this->user_model->update_meta($param_update);
			} else {
				foreach ($_POST['array_weight'] as $raw) {
					$array_temp = explode(',', $raw);
					
					// update
					$param_update['id'] = $array_temp[0];
					$param_update['weight'] = $array_temp[1];
					$result = $this->task_type_model->update($param_update);
				}
			}
		}
		
		echo json_encode($result);
	}
	
	function get_view() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// result default
		$result = '';
		
		if ($action == 'task_grade') {
			$result = $this->load->view( 'task_grade', array(), true );
		}
		
		echo $result;
	}
}