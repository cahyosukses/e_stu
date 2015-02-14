<?php

class schedule extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'schedule' );
    }
	
	function grid() {
		$_POST['is_edit'] = 1;
		$_POST['column'] = array( 'time_frame_title', 'father_name', 'mother_name', 'user_display' );
		
		$array = $this->schedule_model->get_array($_POST);
		$count = $this->schedule_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		$result = array( 'status' => false );
		if ($action == 'generate') {
			// validate
			$schedule_start = $_POST['date_only'].' '.$_POST['available_time_start'];
			$schedule_end = $_POST['date_only'].' '.$_POST['available_time_to'];
			$busy_start = $_POST['date_only'].' '.$_POST['busy_time_start'];
			$busy_end = $_POST['date_only'].' '.$_POST['busy_time_to'];
			if (ConvertToUnixTime($schedule_start) >= ConvertToUnixTime($schedule_end)) {
				$result['message'] = 'Time To must greather than Time From';
				echo json_encode($result);
				exit;
			} else if (!empty($_POST['busy_time_start']) && ConvertToUnixTime($busy_start) > ConvertToUnixTime($busy_end)) {
				$result['message'] = 'Busy Time To must greather than Busy Time From';
				echo json_encode($result);
				exit;
			}
			
			// get array user
			$param_user = array(
				'user_type_id' => USER_TYPE_TEACHER,
				'limit' => 50
			);
			if (!empty($_POST['user_id'])) {
				$param_user['user_id'] = $_POST['user_id'];
			}
			$array_user = $this->user_model->get_array($param_user);
			
			// collect teacher class
			foreach ($array_user as $key => $teacher) {
				$array_quran_class = $array_level_class = array();
				$teacher_class = $this->teacher_class_model->get_array(array( 'user_id' => $teacher['user_id'] ));
				foreach ($teacher_class as $class_info) {
					if ($class_info['class_type_id'] == CLASS_TYPE_QURAN) {
						if (!in_array($class_info['quran_level_id'], $array_quran_class)) {
							$array_quran_class[] = $class_info['quran_level_id'];
						}
					} else {
						if (!in_array($class_info['class_level_id'], $array_level_class)) {
							$array_level_class[] = $class_info['class_level_id'];
						}
					}
				}
				
				$array_user[$key]['array_quran_class'] = $array_quran_class;
				$array_user[$key]['array_level_class'] = $array_level_class;
			}
			
			// collect parent
			$max_no_parent = 0;
			foreach ($array_user as $key => $teacher) {
				$param_parent = array();
				if (count($teacher['array_quran_class']) > 0) {
					$param_parent['quran_level_in'] = implode(',', $teacher['array_quran_class']);
				}
				if (count($teacher['array_level_class']) > 0) {
					$param_parent['class_level_in'] = implode(',', $teacher['array_level_class']);
				}
				$array_user[$key]['array_parent'] = $this->parents_model->get_array_child($param_parent);
				
				if (count($array_user[$key]['array_parent']) > $max_no_parent) {
					$max_no_parent = count($array_user[$key]['array_parent']);
				}
			}
			
			// calculate time
			$range_time = (ConvertToUnixTime($schedule_end) - ConvertToUnixTime($schedule_start)) / 60;
			$busy_time = (ConvertToUnixTime($busy_end) - ConvertToUnixTime($busy_start)) / 60;
			$available_time = $range_time - $busy_time;
			$required_time = $max_no_parent * $_POST['length_of_time'];
			if ($required_time > $available_time) {
				$result['message'] = ($required_time - $available_time).' more minutes required for '.$max_no_parent.' parents';
				echo json_encode($result);
				exit;
			}
			
			// generate each parent
			$time_generate_start = $schedule_start;
			foreach ($array_user as $key => $teacher) {
				foreach ($teacher['array_parent'] as $parent) {
					// validate
					$time_generate_end = add_date($time_generate_start, $_POST['length_of_time'].' minutes', array( 'date_format' => 'Y-m-d H:i:s' ));
					if (	!empty($_POST['busy_time_start'])
							&& ConvertToUnixTime($time_generate_end) > ConvertToUnixTime($busy_start)
							&& ConvertToUnixTime($time_generate_end) < ConvertToUnixTime($busy_end)
						) {
						$time_generate_start = $busy_end;
						$time_generate_end = add_date($time_generate_start, $_POST['length_of_time'].' minutes', array( 'date_format' => 'Y-m-d H:i:s' ));
					}
					
					// insert
					$param_insert = array(
						'user_id' => $teacher['user_id'],
						'parent_id' => $parent['parent_id'],
						'time_frame' => $time_generate_start
					);
					$result = $this->schedule_model->update($param_insert);
					
					// update next schedule time
					$time_generate_start = $time_generate_end;
				}
			}
		}
		else if ($action == 'delete') {
			$result = $this->schedule_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}