<?php

class classes extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'classes' );
    }
	
	function grid() {
		// user
		$user = $this->user_model->get_session();
		
		$grid_type = (isset($_POST['grid_type'])) ? $_POST['grid_type'] : 'teacher';
		
		if ($grid_type == 'teacher') {
			if ($user['user_type_id'] == USER_TYPE_ADMINISTRATOR) {
				$_POST['is_delete'] = 1;
			} else {
				$_POST['is_custom'] = '&nbsp;';
			}
			$_POST['column'] = array( 'user_display' );
			
			$array = $this->teacher_class_model->get_array($_POST);
			$count = $this->teacher_class_model->get_count();
		}
		else if ($grid_type == 'student') {
			if ($user['user_type_id'] == USER_TYPE_ADMINISTRATOR) {
				$_POST['is_delete'] = 1;
			} else {
				$_POST['is_custom'] = '&nbsp;';
			}
			$_POST['column'] = array( 's_name', 'father_name', 'father_cell' );
			
			$array = $this->student_model->get_array($_POST);
			$count = $this->student_model->get_count();
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
		
		// teacher
		if ($action == 'update_teacher') {
			$param_check = array(
				'user_id' => $_POST['user_id'],
				'class_type_id' => $_POST['class_type_id'],
				'quran_level_id' => $_POST['quran_level_id'],
				'class_level_id' => $_POST['class_level_id']
			);
			$check = $this->teacher_class_model->get_by_id($param_check);
			if (count($check) > 0) {
				$result['message'] = 'Teacher already exist at current class.';
				echo json_encode($result);
				exit;
			}
			
			$result = $this->teacher_class_model->update($_POST);
		}
		else if ($action == 'delete_teacher') {
			$result = $this->teacher_class_model->delete($_POST);
		}
		
		// student
		else if ($action == 'update_student') {
			$check = $this->student_model->get_by_id(array( 's_id' => $_POST['s_id'] ));
			if (count($check) > 0) {
				$exist_current_class = false;
				if (empty($_POST['quran_level_id']) && $_POST['class_level_id'] == $check['class_level_id']) {
					$exist_current_class = true;
				} else if (empty($_POST['class_level_id']) && $_POST['quran_level_id'] == $check['quran_level_id']) {
					$exist_current_class = true;
				}
				
				if ($exist_current_class) {
					$result['message'] = 'Student already exist at current class.';
					echo json_encode($result);
					exit;
				}
			}
			
			// update
			$param_update['s_id'] = $_POST['s_id'];
			if (!empty($_POST['quran_level_id'])) {
				$param_update['quran_level_id'] = $_POST['quran_level_id'];
			}
			if (!empty($_POST['class_level_id'])) {
				$param_update['class_level_id'] = $_POST['class_level_id'];
			}
			$result = $this->student_model->update($param_update);
		}
		else if ($action == 'delete_student') {
			$result = $this->student_model->update($_POST);
		}
		
		echo json_encode($result);
	}
	
	function get_view() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// result default
		$result = '';
		
		if ($action == 'get_class_grade') {
			$result = $this->load->view( 'class_grade', array(), true );
		}
		
		echo $result;
	}
}