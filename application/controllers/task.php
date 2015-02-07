<?php

class task extends SE_Login_Controller {
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
		$user_type = $this->user_type_model->get_by_id(array( 'id' => $user['user_type_id'] ));
		
		// result default
		$result = array( 'status' => false );
		
		// task
		if ($action == 'update') {
			// insert or update
			$is_insert = (empty($_POST['id'])) ? true : false;
			$is_send_mail = (isset($_POST['send_mail']) && $_POST['send_mail'] == 1) ? true : false;
			
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
			
			// update
			$result = $this->task_model->update($_POST);
			
			// add student
			if ($is_insert && $is_send_mail) {
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
				
				// sent mail
				$array_to = $array_sub = array();
				foreach ($array_student as $row) {
					// add email
					if (!empty($row['parent_email'])) {
						$array_to[] = array(
							'name' => $row['father_name'],
							'email' => strtolower($row['parent_email'])
						);
						$array_sub['-name-'][] = $row['s_name'];
					}
					if (!empty($row['mother_email'])) {
						$array_to[] = array(
							'name' => $row['mother_name'],
							'email' => strtolower($row['mother_email'])
						);
						$array_sub['-name-'][] = $row['s_name'];
					}
				}
				
				// data
				$task = $this->task_model->get_by_id(array( 'id' => $result['id'] ));
				if (!empty($_POST['quran_level_id'])) {
					$class_level = $this->quran_level_model->get_by_id(array( 'id' => $_POST['quran_level_id'] ));
					$string_class = 'New Task for Quran Level';
				} else {
					$class_level = $this->class_level_model->get_by_id(array( 'id' => $_POST['class_level_id'] ));
					$string_class = 'New Task for Class Level';
				}
				
				// content
				$content = 'Dear All Parent,
				
'.$string_class.' : '.$class_level['name'].'
Task Type : '.$task['task_type_name'].'
Task Title : '.$task['title'].'
Student Name : -name-
Due Date : '.$task['due_date_swap'].'
Task Content : '.$task['content'];
				
				// add attachment
				$string_attachment = '';
				$array_attachment = object_to_array(json_decode($_POST['attachment']));
				foreach ($array_attachment as $key => $row) {
					$string_attachment .= "\n".'- <a href="'.base_url('static/upload/'.$row['file_name']).'" target="_blank">'.$row['file_only'].'</a>';
				}
				if (!empty($string_attachment)) {
					$content .= "\n\nAttachment files :$string_attachment";
				}
				
				// email category
				$email_category = 'New Task Assignment-'.$user['user_display'];
				
				// sent grid
				$param_mail = array(
					'category' => array(
						'asm_group_id' => 7,
						'title' => $email_category
					),
					'user_email' => $user['user_email'],
					'user_display' => $user['user_display'],
					'array_to' => $array_to,
					'array_sub' => $array_sub,
					'subject' => 'New Task Assignment',
					'content' => $content,
					'title' => $user_type['title']
				);
				$this->mail_model->sent_grid($param_mail);
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
			// update task
			$param_task['id'] = $_POST['task_id'];
			$param_task['is_complete'] = 1;
			$this->task_model->update($param_task);
			
			// update grade
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