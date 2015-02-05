<?php

class report_card extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'report_card' );
    }
	
	function grid() {
		$grid_type = (isset($_POST['grid_type'])) ? $_POST['grid_type'] : 'report_card';
		
		if ($grid_type == 'report_card_teacher') {
			$_POST['is_custom'] = '<span class="cursor-font-awesome icon-envelope btn-email" title="Send Email"></span>';
			$_POST['column'] = array( 'class_type_name', 'class_level_name', 'teacher_name' );
			
			$array_recap = $this->class_level_model->get_array_recap($_POST);
			$array = $array_recap['array'];
			$count = $array_recap['count'];
		} else if ($grid_type == 'report_card') {
			$_POST['column'] = array( 'father_name', 'mother_name', 'student_count' );
			
			$array = $this->parents_model->get_array_child($_POST);
			$count = $this->parents_model->get_count();
		}
		
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		echo json_encode($grid);
	}
	
	function action() {
		ini_set("memory_limit", "256M");
		$this->load->library('mpdf');
		
		// result default
		$result = array( 'status' => false );
		
		// page data
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// action
		if ($action == 'generate_report') {
			// generate pdf
			$this->parents_model->generate_report_card(array( 'parent_id' => $_POST['parent_id'] ));
			
			// set result
			$result = array( 'status' => true, 'message' => 'Report generated successfully' );
		}
		else if ($action == 'generate_all') {
			// parent total
			$result['parent_total'] = $this->parents_model->get_count(array( 'is_query' => true ));
			
			// selected parent
			$param_parent = array( 'start' => $_POST['start'], 'limit' => 1 );
			$array_parent = $this->parents_model->get_array($param_parent);
			foreach ($array_parent as $row) {
				$this->parents_model->generate_report_card(array( 'parent_id' => $row['p_id'] ));
			}
			
			// result data
			$parent_counter = $_POST['start'] + 1;
			$parent_percent = round(($parent_counter / $result['parent_total']) * 100);
			$is_complete = ($parent_counter >= $result['parent_total']) ? true : false;
			
			// finalize
			if ($is_complete && isset($_POST['finalize']) && $_POST['finalize']) {
				// update config
				$report_card_finalize = $this->config_model->get_by_id(array( 'config_key' => 'report-card-finalize' ));
				$param_update = array( 'config_id' => $report_card_finalize['config_id'], 'config_value' => $this->config->item('current_datetime') );
				$result_update = $this->config_model->update($param_update);
				
				// sent email
				$this->parents_model->send_report_card(array());
			}
			
			// set status
			sleep(1);
			$result['status'] = true;
			$result['is_complete'] = $is_complete;
			$result['message'] = $is_complete ? 'Generate complete' : 'Generating for parent #'.$parent_counter.' - '.$parent_percent.'%, Generating for Parent #'.($parent_counter + 1);
		}
		else if ($action == 'email_report') {
			$result = $this->parents_model->send_report_card($_POST);
		}
		else if ($action == 'sent_mail_to_all') {
			$array_user = array();
			$array_class = $this->class_level_model->get_array_recap(array( 'status_finalize' => $_POST['status_finalize'] ));
			foreach ($array_class['array'] as $row) {
				$param_temp = array( 'class_type_id' => $row['class_type_id'] );
				if (!empty($row['quran_level_id'])) {
					$param_temp['quran_level_id'] = $row['quran_level_id'];
				}
				if (!empty($row['class_level_id'])) {
					$param_temp['class_level_id'] = $row['class_level_id'];
				}
				$array_temp = $this->teacher_class_model->get_array($param_temp);
				foreach ($array_temp as $user_temp) {
					if (!in_array($user_temp['user_id'], $array_user)) {
						$array_user[] = $user_temp['user_id'];
					}
				}
			}
			
			$result = $this->teacher_mail($array_user);
		}
		else if ($action == 'sent_mail_to_single') {
			$array_user = array();
			$array_temp = $this->teacher_class_model->get_array($_POST);
			foreach ($array_temp as $user_temp) {
				if (!in_array($user_temp['user_id'], $array_user)) {
					$array_user[] = $user_temp['user_id'];
				}
			}
			
			$result = $this->teacher_mail($array_user);
		}
		
		echo json_encode($result);
	}
	
	function teacher_mail($array_user_id) {
		// user
		$user = $this->user_model->get_session();
		$user_type = $this->user_type_model->get_by_id(array( 'id' => $user['user_type_id'] ));
		
		// add email
		$array_to = $array_sub = array();
		$array_user = $this->user_model->get_array(array( 'user_id_in' => implode(',', $array_user_id) ));
		foreach ($array_user as $row) {
			if (!empty($row['user_email'])) {
				// user class
				$class_info = '';
				$array_class = $this->teacher_class_model->get_array(array( 'user_id' => $row['user_id'] ));
				foreach ($array_class as $class_teacher) {
					// record from class note
					$param_check = array( 'class_type_id' => $class_teacher['class_type_id'] );
					if (!empty($class_teacher['quran_level_id'])) {
						$param_check['quran_level_id'] = $class_teacher['quran_level_id'];
					}
					if (!empty($class_teacher['class_level_id'])) {
						$param_check['class_level_id'] = $class_teacher['class_level_id'];
					}
					$row_check = $this->class_note_model->get_by_id($param_check);
					if (count($row_check) > 0) {
						continue;
					}
					
					// add to class info
					if (!empty($class_teacher['quran_level_id'])) {
						$class_info .= '* '.$class_teacher['class_type_name'].' - '.$class_teacher['quran_level_name']."<br />\n";
					}
					if (!empty($class_teacher['class_level_id'])) {
						$class_info .= '* '.$class_teacher['class_type_name'].' - '.$class_teacher['class_level_name']."<br />\n";
					}
				}
				
				// set email parameter
				$array_to[] = array(
					'name' => $row['user_display'],
					'email' => strtolower($row['user_email'])
				);
				$array_sub['-list_of_the_classes-'][] = $class_info;
			}
		}
		
		// get content
		$content = $this->config_model->get_by_id(array( 'config_key' => 'report-card-teacher' ));
		
		// sent grid
		$param_mail = array(
			'user_email' => $user['user_email'],
			'user_display' => $user['user_display'],
			'array_to' => $array_to,
			'array_sub' => $array_sub,
			'subject' => 'Report Card',
			'content' => $content['config_value'],
			'title' => $user_type['title']
		);
		$this->mail_model->sent_grid($param_mail);
		
		$result = array( 'status' => true, 'message' => count($array_to).' email sent.' );
		return $result;
	}
}
