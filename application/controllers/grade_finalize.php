<?php

class grade_finalize extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'grade_finalize' );
    }
	
	function grid() {
		$_POST['default_value'] = false;
		$_POST['is_edit_only'] = 1;
		$_POST['column'] = array( 'name', 'quran_summary', 'figh_summary', 'akhlaq_summary', 'tareekh_summary', 'aqaid_summary' );
		
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
			$result = $this->teacher_comment_model->update($_POST);
		}
		else if ($action == 'get_teacher_comment') {
			$result = $this->teacher_comment_model->get_by_id($_POST);
			
			// make record exist
			if (count($result) == 0) {
				$this->teacher_comment_model->update($_POST);
				$result = $this->teacher_comment_model->get_by_id($_POST);
			}
		}
		else if ($action == 'finalize') {
			$result = $this->teacher_comment_model->is_complete($_POST);
			if ($result['status']) {
				// update class note
				$param_class_note = array( 'class_type_id' => $_POST['class_type_id'] );
				if (!empty($_POST['class_level_id'])) {
					$param_class_note['class_level_id'] = $_POST['class_level_id'];
				}
				if (!empty($_POST['quran_level_id'])) {
					$param_class_note['quran_level_id'] = $_POST['quran_level_id'];
				}
				$class_note = $this->class_note_model->get_by_id($param_class_note);
				if (count($class_note) == 0) {
					$param_insert = $param_class_note;
					$param_insert['finalize_date'] = $this->config->item('current_datetime');
					$this->class_note_model->update($param_insert);
				} else {
					$param_update['id'] = $class_note['id'];
					$param_update['finalize_date'] = $this->config->item('current_datetime');
					$this->class_note_model->update($param_update);
				}
				
				// set message
				$class_type = $this->class_type_model->get_by_id(array( 'id' => $_POST['class_type_id'] ));
				set_flash_message('Grades finalized for class '.$class_type['name']);
			} else {
				$limit = 3;
				$string_student = '';
				foreach ($result['array_student'] as $row) {
					$string_student .= (empty($string_student)) ? $row['s_name'] : ', '.$row['s_name'];
					$limit--;
					if ($limit <= 0) {
						$string_student .= ' and other student';
						break;
					}
				}
				
				$result['message'] = $string_student.' has no comments';
			}
		}
		
		echo json_encode($result);
	}
}
