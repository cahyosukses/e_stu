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
		$_POST['column'] = array( 'time_frame_title', 'father_name', 'mother_name', 'user_display', 'student_name' );
		
		$array = $this->schedule_model->get_array($_POST);
		$count = $this->schedule_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// user
		$user = $this->user_model->get_session();
		$user_type = $this->user_type_model->get_by_id(array( 'id' => $user['user_type_id'] ));
		
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
			foreach ($array_user as $key => $teacher) {
				$array_schedule_pending = array();
				$time_generate_start = $schedule_start;
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
					
					#region pending flow
					foreach ($array_schedule_pending as $key => $pending) {
						if ($pending['is_done']) {
							continue;
						}
						
						$pending_check = array(
							'parent_id' => $pending['parent_id'],
							'time_frame' => $time_generate_start
						);
						$pending_check = $this->schedule_model->get_array($pending_check);
						if (count($pending_check) == 0) {
							$param_insert = array(
								'user_id' => $teacher['user_id'],
								'parent_id' => $pending['parent_id'],
								'time_frame' => $time_generate_start
							);
							$result = $this->schedule_model->update($param_insert);
							$array_schedule_pending[$key]['is_done'] = true;
							
							// update next schedule time
							$time_generate_start = $time_generate_end;
							$time_generate_end = add_date($time_generate_start, $_POST['length_of_time'].' minutes', array( 'date_format' => 'Y-m-d H:i:s' ));
							if (	!empty($_POST['busy_time_start'])
									&& ConvertToUnixTime($time_generate_end) > ConvertToUnixTime($busy_start)
									&& ConvertToUnixTime($time_generate_end) < ConvertToUnixTime($busy_end)
								) {
								$time_generate_start = $busy_end;
								$time_generate_end = add_date($time_generate_start, $_POST['length_of_time'].' minutes', array( 'date_format' => 'Y-m-d H:i:s' ));
							}
						}
					}
					#endregion pending flow
					
					#region normal flow
					// make sure parent do not have schedule at this time
					$param_check = array(
						'parent_id' => $parent['parent_id'],
						'time_frame' => $time_generate_start
					);
					$array_check = $this->schedule_model->get_array($param_check);
					
					// insert or pending
					if (count($array_check) == 0) {
						$param_insert = array(
							'user_id' => $teacher['user_id'],
							'parent_id' => $parent['parent_id'],
							'time_frame' => $time_generate_start
						);
						$result = $this->schedule_model->update($param_insert);
						
						// update next schedule time
						$time_generate_start = $time_generate_end;
					} else {
						$array_schedule_pending[] = array( 'parent_id' => $parent['parent_id'], 'is_done' => false );
					}
					#endregion normal flow
				}
				
				// last parent with pending status
				foreach ($array_schedule_pending as $key => $pending) {
					if ($pending['is_done']) {
						continue;
					}
					
					for ($i = 0; $i <= 10; $i++) {
						$pending_check = array(
							'parent_id' => $pending['parent_id'],
							'time_frame' => $time_generate_start
						);
						$pending_check = $this->schedule_model->get_array($pending_check);
						
						// insert or get next schedule
						if (count($pending_check) == 0) {
							$param_insert = array(
								'user_id' => $teacher['user_id'],
								'parent_id' => $pending['parent_id'],
								'time_frame' => $time_generate_start
							);
							$result = $this->schedule_model->update($param_insert);
							$array_schedule_pending[$key]['is_done'] = true;
							break;
						} else {
							// update next schedule time
							$time_generate_start = $time_generate_end;
							$time_generate_end = add_date($time_generate_start, $_POST['length_of_time'].' minutes', array( 'date_format' => 'Y-m-d H:i:s' ));
							if (	!empty($_POST['busy_time_start'])
									&& ConvertToUnixTime($time_generate_end) > ConvertToUnixTime($busy_start)
									&& ConvertToUnixTime($time_generate_end) < ConvertToUnixTime($busy_end)
								) {
								$time_generate_start = $busy_end;
								$time_generate_end = add_date($time_generate_start, $_POST['length_of_time'].' minutes', array( 'date_format' => 'Y-m-d H:i:s' ));
							}
						}
					}
				}
			}
		}
		else if ($action == 'mail_parent') {
			// get array parent
			$param_parent = array( 'limit' => 100 );
			if (!empty($_POST['p_id'])) {
				$param_parent['p_id'] = $_POST['p_id'];
			}
			$array_parent = $this->parents_model->get_array($param_parent);
			
			// get schedule
			foreach ($array_parent as $key => $parent) {
				$param_schedule = array(
					'parent_id' => $parent['p_id'],
					'time_frame_min' => $this->config->item('current_date'),
					'sort' => '[{"property":"time_frame","direction":"ASC"}]'
				);
				$array_parent[$key]['array_schedule'] = $this->schedule_model->get_array($param_schedule);
				
				// remove parent with no schedule
				if (count($array_parent[$key]['array_schedule']) == 0) {
					unset($array_parent[$key]);
				}
			}
			
			#region sent mail
				foreach ($array_parent as $key => $parent) {
					// generate table
					$table_schedule = '<table style="min-width: 500px;" border="1"><tr><td style="width: 30%;">Time Frame</td><td style="width: 30%;">Teacher</td><td style="width: 40%;">Student</td></tr>';
					foreach ($parent['array_schedule'] as $schedule) {
						$table_schedule .= '<tr><td>'.$schedule['time_frame_title'].'</td><td>'.$schedule['user_display'].'</td><td>'.$schedule['student_name'].'</td></tr>';
					}
					$table_schedule .= '</table>';
					
					// add email
					if (!empty($parent['p_father_email'])) {
						$array_to[] = array(
							'name' => $parent['p_father_name'],
							'email' => strtolower($parent['p_father_email'])
						);
						$array_sub['-list_schedule-'][] = $table_schedule;
					}
					if (!empty($parent['p_mother_email'])) {
						$array_to[] = array(
							'name' => $parent['p_mother_name'],
							'email' => strtolower($parent['p_mother_email'])
						);
						$array_sub['-list_schedule-'][] = $table_schedule;
					}
				}
				
				// get content parent
				$content = $this->config_model->get_by_id(array( 'config_key' => 'schedule-email-parent' ));
				
				// sent grid
				$param_mail = array(
					'user_email' => $user['user_email'],
					'user_display' => $user['user_display'],
					'array_to' => $array_to,
					'array_sub' => $array_sub,
					'subject' => 'Schedule',
					'content' => $content['config_value'],
					'title' => $user_type['title']
				);
				$this->mail_model->sent_grid($param_mail);
			#endregion sent mail
			
            $result['status'] = '1';
            $result['message'] = count($array_to).' email sent.';
		}
		else if ($action == 'mail_teacher') {
			// get array teacher
			$param_teacher = array( 'limit' => 100 );
			if (!empty($_POST['user_id'])) {
				$param_teacher['user_id'] = $_POST['user_id'];
			}
			$array_teacher = $this->user_model->get_array($param_teacher);
			
			// get schedule
			foreach ($array_teacher as $key => $teacher) {
				$param_schedule = array(
					'user_id' => $teacher['user_id'],
					'time_frame_min' => $this->config->item('current_date'),
					'sort' => '[{"property":"time_frame","direction":"ASC"}]'
				);
				$array_teacher[$key]['array_schedule'] = $this->schedule_model->get_array($param_schedule);
				
				// remove teacher with no schedule
				if (count($array_teacher[$key]['array_schedule']) == 0) {
					unset($array_teacher[$key]);
				}
			}
			
			#region sent mail
				foreach ($array_teacher as $key => $teacher) {
					// generate table
					$table_schedule = '<table style="min-width: 500px;" border="1"><tr><td style="width: 20%;">Time Frame</td><td style="width: 20%;">Father</td><td style="width: 20%;">Mother</td><td style="width: 40%;">Student</td></tr>';
					foreach ($teacher['array_schedule'] as $schedule) {
						$table_schedule .= '<tr><td>'.$schedule['time_frame_title'].'</td><td>'.$schedule['father_name'].'</td><td>'.$schedule['mother_name'].'</td><td>'.$schedule['student_name'].'</td></tr>';
					}
					$table_schedule .= '</table>';
					
					// add email
					$array_to[] = array(
						'name' => $teacher['user_display'],
						'email' => strtolower($teacher['user_email'])
					);
					$array_sub['-list_schedule-'][] = $table_schedule;
				}
				
				// get content parent
				$content = $this->config_model->get_by_id(array( 'config_key' => 'schedule-email-teacher' ));
				
				// sent grid
				$param_mail = array(
					'user_email' => $user['user_email'],
					'user_display' => $user['user_display'],
					'array_to' => $array_to,
					'array_sub' => $array_sub,
					'subject' => 'Schedule',
					'content' => $content['config_value'],
					'title' => $user_type['title']
				);
				$this->mail_model->sent_grid($param_mail);
			#endregion sent mail
			
            $result['status'] = '1';
            $result['message'] = count($array_to).' email sent.';
		}
		else if ($action == 'delete') {
			$result = $this->schedule_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}