<?php

class attendance extends SE_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'attendance' );
    }
	
	function grid() {
		// user
		$user = $this->user_model->get_session();
		
		$grid_type = (isset($_POST['grid_type'])) ? $_POST['grid_type'] : 'attendance';
		
		if ($grid_type == 'teacher') {
			$_POST['is_custom']  = '<span class="cursor-font-awesome icon-pencil btn-edit" title="Edit"></span>';
			$_POST['is_custom'] .= '<span class="cursor-font-awesome icon-list btn-attendance-student" title="Update Attendance"></span>';
			$_POST['is_custom'] .= '<span class="cursor-font-awesome icon-trash btn-delete" title="Delete"></span>';
			$_POST['column'] = array( 'due_date_swap', 'title' );
			
			$array = $this->attendance_model->get_array($_POST);
			$count = $this->attendance_model->get_count();
		}
		else if ($grid_type == 'parent') {
			$_POST['column'] = array( 'due_date_swap', 'class_type_name', 'attendance_title', 'award_title' );
			
			$array = $this->attendance_student_model->get_array($_POST);
			$count = $this->attendance_student_model->get_count();
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
		
		// attendance
		if ($action == 'update_attendance') {
			// insert or update
			$is_insert = (empty($_POST['id'])) ? true : false;
			
			// add user
			$_POST['user_id'] = $user['user_id'];
			
			// update it
			$result = $this->attendance_model->update($_POST);
			
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
					$this->attendance_student_model->update(array( 'attendance_id' => $result['id'], 'student_id' => $row['s_id'] ));
				}
			}
		}
		else if ($action == 'delete_attendance') {
			$result = $this->attendance_model->delete($_POST);
		}
		
		else if ($action == 'attendance_student_update') {
			foreach ($_POST['array_award'] as $raw) {
				$array_temp = explode(',', $raw);
				
				// update
				$param_update['id'] = $array_temp[0];
				$param_update['award'] = $array_temp[1];
				$param_update['update_time'] = $this->config->item('current_datetime');
				$result = $this->attendance_student_model->update($param_update);
			}
		}
		
		echo json_encode($result);
	}
	
	function get_view() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// result default
		$result = '';
		
		if ($action == 'attendance_student_list') {
			$result = $this->load->view( 'attendance_student_list', array(), true );
		}
		
		echo $result;
	}
}