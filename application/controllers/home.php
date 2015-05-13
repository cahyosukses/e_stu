<?php

class home extends SE_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$is_login = $this->user_model->is_login();
		
		if ($is_login) {
			$this->load->view( 'home' );
		} else {
			$this->load->view( 'signin' );
		}
    }
	
	function report() {
		$this->load->view( 'home' );
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		$result = array( 'status' => false );
		if ($action == 'signin') {
			$user = $this->user_model->get_by_id(array( 'user_uname' => $_POST['user_uname'] ));
			if (count($user) == 0) {
				$user = $this->parents_model->get_by_id(array( 'p_father_email' => $_POST['user_uname'] ));
				if (count($user) > 0 && $user['p_mother_email'] == $_POST['user_uname']) {
					$user['user_email'] = $_POST['user_uname'];
					$user['user_display'] = $user['p_mother_name'];
				}
			}
			
			$result = array( 'message' => '' );
			if (count($user) == 0) {
				$result['message'] = 'Sorry, username not found.';
			} else if ($user['user_pword'] == $_POST['user_pword']) {
				// additional data for parent
				if ($user['user_type_id'] == USER_TYPE_PARENT) {
					// get all student by parent
					$array_student = $this->student_model->get_array(array( 's_parent_id' => $user['user_id'] ));
					
					// stop if parent do not have student here
					if (count($array_student) == 0) {
						$result['message'] = 'Sorry, you do not have active student here.';
						echo json_encode($result);
						exit;
					}
					
					// add session
					$user['student_id'] = $array_student[0]['s_id'];
					$user['array_student'] = $array_student;
					
					// add student name
					$user['student_name'] = '';
					foreach ($array_student as $row) {
						$user['student_name'] .= (empty($user['student_name'])) ? $row['s_name'] : ', '.$row['s_name'];
					}
				} else {
					// update time
					$param_update['user_id'] = $user['user_id'];
					$param_update['user_logins'] = $user['user_logins'] + 1;
					$param_update['user_lastlogin'] = $this->config->item('current_datetime');
					$this->user_model->update($param_update);
				}
				
				// set session
				$result['status'] = true;
				$this->user_model->set_session($user);
			} else {
				$result['message'] = 'Sorry, passsword didnot match.';
			}
		}
		else if ($action == 'update_password') {
			// user
			$user = $this->user_model->get_session();
			
			// parent
			if ($user['user_type_id'] == USER_TYPE_PARENT) {
				$parent = $this->parents_model->get_by_id(array( 'p_id' => $user['user_id'] ));
				if ($parent['user_pword'] == $_POST['passwd_old']) {
					// update
					$param_update['p_id'] = $parent['p_id'];
					$param_update['p_passwd'] = $_POST['passwd_new'];
					$this->parents_model->update($param_update);
					
					// message
					$result['status'] = true;
					$result['message'] = 'Password successful updated.';
				} else {
					// message
					$result['message'] = 'Old passwords are not the same.';
				}
			}
			// admin
			else {
				$user = $this->user_model->get_by_id(array( 'user_id' => $user['user_id'] ));
				if ($user['user_pword'] == $_POST['passwd_old']) {
					// update
					$param_update['user_id'] = $user['user_id'];
					$param_update['user_pword'] = $_POST['passwd_new'];
					$this->user_model->update($param_update);
					
					// message
					$result['status'] = true;
					$result['message'] = 'Password successful updated.';
				} else {
					// message
					$result['message'] = 'Old passwords are not the same.';
				}
			}
		}
		else if ($action == 'forget_password') {
			// user
			$user = $this->user_model->get_by_id(array( 'user_email' => $_POST['email'] ));
			if (count($user) == 0) {
				$user = $this->parents_model->get_by_id(array( 'p_father_email' => $_POST['email'] ));
			}
			
			// result
			if (count($user) == 0) {
				$result['message'] = 'Sorry, username not found.';
			} else {
				// sent grid
				$param_sent_grid = array(
					'from' => 'noreply@jafariaschool.org',
					'fromname' => 'System',
					'to' => array( array( 'email' => $_POST['email'] ) ),
					'subject' => SITE_TITLE.' - Forget Password',
					'text' => 'You have request forget password, below information for your password.<br />Your password : '.$user['user_pword'],
					'html' => 'You have request forget password, below information for your password.<br />Your password : '.$user['user_pword']
				);
				sent_grid($param_sent_grid);
				
				// update result
				$result['status'] = true;
				$result['message'] = 'Please check your email to get your password.';
			}
		}
		else if ($action == 'change_student') {
			// update session
			$user = $this->user_model->get_session();
			$user['student_id'] = $_POST['student_id'];
			$this->user_model->set_session($user);
			
			$result['status'] = true;
		}
		else if ($action == 'reset_task') {
			$result = $this->task_model->delete(array( 'truncate' => true ));
		}
		else if ($action == 'reset_attendance') {
			$result = $this->attendance_model->delete(array( 'truncate' => true ));
		}
		else if ($action == 'update_signature') {
			ini_set("memory_limit", "256M");
			
			// user
			$user = $this->user_model->get_session();
			
			// generate signature
			if (!empty($_POST['output'])) {
				@mkdir($this->config->item('base_path').'/static/upload/'.date("Y/"));
				@mkdir($this->config->item('base_path').'/static/upload/'.date("Y/m"));
				@mkdir($this->config->item('base_path').'/static/upload/'.date("Y/m/d"));
				$img_filename = date("Y/m/d/YmdHis_").rand(1000,9998).'.png';
				$img_filepath = $this->config->item('base_path').'/static/upload/'.$img_filename;
				$img_raw = sigJsonToImage($_POST['output']);
				imagepng($img_raw, $img_filepath);
				imagedestroy($img_raw);
			}
			
			// update parent signature
			$param_update = array(
				'p_id' => $user['p_id'],
				'p_sign_image' => $img_filename
			);
			$result = $this->parents_model->update($param_update);
			
		}
		else if ($action == 'update_weekly_checklist') {
			// update weekly checklist
			$result = $this->weekly_checklist_model->update($_POST);
			
			// weekly checklist
			$weekly_checklist = $this->weekly_checklist_model->get_by_id($_POST);
			
			// get task title
			for ($i = 0; $i <= 10; $i++) {
				$date_counter = add_date($weekly_checklist['date_check'], "-$i days");
				$date_title = get_format_date($date_counter, array( 'date_format' => 'l' ));
				if ($date_title == 'Sunday') {
					$date_sunday = $date_counter;
					break;
				}
			}
			$task_title = 'Weekly Quran Checklist Week of '.get_format_date($date_sunday, array( 'date_format' => 'm/d/y' ));
			
			// get existing task
			$task_class = $this->task_class_model->get_by_id(array( 'task_title' => $task_title, 'student_id' => $weekly_checklist['student_id'] ));
			if (count($task_class) > 0) {
				// get summary reading time
				$reading_time = $this->weekly_checklist_model->get_summary_reading_time(array( 'start_date' => $date_sunday, 'student_id' => $weekly_checklist['student_id'] ));
				
				// update task grade
				$param_update = array( 'id' => $task_class['id'], 'grade' => $reading_time['score'] );
				$this->task_class_model->update($param_update);
			}
		}
		else if ($action == 'update_tardy_tracker') {
			$user = $this->user_model->get_session();
			$send_sms = (isset($_POST['send_sms'])) ? $_POST['send_sms'] : 0;
			$parent_student = $this->parents_model->get_by_id(array( 'p_id' => $_POST['parent_id'] ));
			if ($_POST['student_id'] == 'all') {
				$array_student = $this->student_model->get_array(array( 's_parent_id' => $_POST['parent_id'] ));
			} else {
				$array_student = $this->student_model->get_array(array( 's_id' => $_POST['student_id'] ));
			}
			
			#region sms
			if ($send_sms && empty($_POST['id']) && $user['user_type_id'] != USER_TYPE_PARENT) {
				
				// load twilio
				ini_set("memory_limit", "256M");
				$this->load->library('twilio');
				
				// get parent phone
				$array_phone = array();
				if (!empty($parent_student['p_father_cell'])) {
					$array_phone[] = $parent_student['p_father_cell'];
				}
				if (!empty($parent_student['p_mother_cell'])) {
					$array_phone[] = $parent_student['p_mother_cell'];
				}
				
				if (count($array_phone) > 0) {
					// twilio and sms config
					$twilio = $this->config_model->get_row(array( 'config_key' => 'twilio' ));
					$client = new Services_Twilio($twilio['sid'], $twilio['token']);
					$tardy_tracker_sms = $this->config_model->get_by_id(array( 'config_key' => 'tardy-tracker-sms' ));
					
					// sent sms
					foreach ($array_student as $row) {
						// generate message
						$sms_message = str_replace('--student_name--', $row['s_name'], $tardy_tracker_sms['config_value']);
						$sms_message = str_replace('--tardy_no--', $this->tardy_model->get_count(array( 'student_id' => $row['s_id'])) + 1, $sms_message);
						
						if (SENT_MAIL) {
							try {
								foreach ($array_phone as $phone_no) {
									$message = $client->account->messages->sendMessage($twilio['phone_no'], $phone_no, $sms_message);
								}
							} catch(Exception $e) {
								$result['status'] = false;
								$result['message'] = $e->getMessage();
								$result['message'] = preg_replace('/(\.|\;) /i', "$1\n", $result['message']);
								echo json_encode($result);
								exit;
							}
						}
					}
				}
				
			}
			#endregion sms
			
			// insert / update database
			foreach ($array_student as $row) {
				$check = $this->tardy_model->get_by_id(array( 'due_date' => $_POST['due_date'], 'student_id' => $row['s_id'] ));
				if (count($check) > 0 && empty($_POST['id'])) {
					$param_tardy = $check;
					$param_tardy['minute_late'] = $_POST['minute_late'];
					$param_tardy['reason'] = $_POST['reason'];
					$result = $this->tardy_model->update($param_tardy);
					$result['message'] = "Tardy record already exist, so it's only update 'Amount of Minutes Late' and 'Reason' data.";
				} else {
					$param_update = $_POST;
					$param_update['student_id'] = $row['s_id'];
					$result = $this->tardy_model->update($param_update);
				}
			}
		}
		else if ($action == 'update_tardy_excuse') {
			// tardy
			$tardy = $this->tardy_model->get_by_id(array( 'id' => $_POST['id'] ));
			
			// get attendance
			$attendance_student = $this->attendance_student_model->get_by_id(array( 'due_date' => $tardy['due_date'], 'student_id' => $tardy['student_id'] ));
			if (count($attendance_student) == 0) {
				$result['status'] = false;
				$result['message'] = 'Sorry, no attendance found.';
				echo json_encode($result);
				exit;
			}
			
			// update attendance
			$param_attendance  = array( 'id' => $attendance_student['id'], 'award' => 1 );
			$result_attendance = $this->attendance_student_model->update($param_attendance);
			
			// update tardy
			$param_tardy = array( 'id' => $tardy['id'], 'is_excuse' => 1 );
			$result = $this->tardy_model->update($param_tardy);
		}
		else if ($action == 'delete_tardy_tracker') {
			$result = $this->tardy_model->delete($_POST);
		}
		else if ($action == 'attendance_excuse') {
			// attendance student
			$param_attendance_student = array( 'student_id' => $_POST['student_id'], 'due_date' => $_POST['due_date'] );
			$attendance_student = $this->attendance_student_model->get_by_id($param_attendance_student);
			
			// update
			$param_update = array( 'id' => $attendance_student['id'], 'award' => 1 );
			$result = $this->attendance_student_model->update($param_update);
		}
		else if ($action == 'handbook_notification') {
			// get user
			$user = $this->user_model->get_session();
			$user_type = $this->user_type_model->get_by_id(array( 'id' => $user['user_type_id'] ));
			
			// array handbook
			$param_handbook_uncomplete = array( 'limit' => 500 );
			if (!empty($_POST['parent_id'])) {
				$param_handbook_uncomplete['parent_id'] = $_POST['parent_id'];
			}
			if (!empty($_POST['limit'])) {
				$param_handbook_uncomplete['limit'] = $_POST['limit'];
			}
			$array_handbook_uncomplete = $this->handbook_model->get_array_uncomplete($param_handbook_uncomplete);
			
			// collect email
			$array_to = array();
			foreach ($array_handbook_uncomplete as $row) {
				if (!empty($row['father_email'])) {
					$array_to[] = array(
						'name' => $row['father_name'],
						'email' => $row['father_email']
					);
				}
				if (!empty($row['mother_email'])) {
					$array_to[] = array(
						'name' => $row['mother_name'],
						'email' => $row['mother_email']
					);
				}
			}
			
			// email content
			$handbook_notification_email = $this->config_model->get_by_id(array( 'config_key' => 'handbook-notification-email' ));
			
			// sent grid
			$param_mail = array(
				'user_email' => $user['user_email'],
				'user_display' => $user['user_display'],
				'array_to' => $array_to,
				'subject' => 'Handbook Reminder',
				'content' => $handbook_notification_email['config_value'],
				'title' => $user_type['title']
			);
			$this->mail_model->sent_grid($param_mail);
			
            $result['status'] = true;
            $result['message'] = count($array_to).' notification has been sent.';
		}
		else if ($action == 'handbook_delete') {
			$result = $this->handbook_model->delete($_POST);
		}
		else if ($action == 'get_attendance_message') {
			$result = $this->attendance_student_model->get_recap_student($_POST);
		}
		else if ($action == 'get_tardy_count') {
			// tardy count
			$total = $this->tardy_model->get_count($_POST);
			
			// result
			$result = array( 'status' => true, 'tardy_next' => $total + 1 );
		}
		else if ($action == 'get_user') {
			$user_session = $this->user_model->get_session();
			if ($user_session['user_type_id'] == USER_TYPE_PARENT) {
				$result = $this->parents_model->get_by_id(array( 'p_id' => $user_session['p_id'] ));
			}
		}
		
		echo json_encode($result);
		exit;
	}
	
	function view() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		$result = array( 'status' => false );
		if ($action == 'weekly_checklist') {
			$this->load->view( 'home_weekly_checklist' );
		} else if ($action == 'class_ranking') {
			$this->load->view( 'class_ranking' );
		}
	}
	
	function grid() {
		$grid_type = (isset($_POST['grid_type'])) ? $_POST['grid_type'] : 'attendance_tracker';
		
		if ($grid_type == 'attendance_tracker') {
			$_POST['column'] = array( 'student_name', 'notification', 'total_absence' );
			$array = $this->attendance_student_model->get_array_notification($_POST);
			$count = $this->attendance_student_model->get_count();
		} else if ($grid_type == 'tardy_tracker') {
			$_POST['column'] = array( 'due_date_swap', 'student_name', 'reason', 'total_tardy' );
			$array = $this->tardy_model->get_array($_POST);
			$count = $this->tardy_model->get_count();
		}
		
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		echo json_encode($grid);
	}
	
	function print_weekly() {
		// exit if no parameter date
		if (empty($this->uri->segments[3])) {
			exit;
		}
		
		ini_set("memory_limit", "256M");
		$this->load->library('mpdf');
		
		// generate invoice
		$template = $this->load->view( 'home_weekly_pdf', array( ), true );
		$this->mpdf->WriteHTML($template);
		$this->mpdf->Output();
		exit;
	}
	
	function handbook_agreement() {
		ini_set("memory_limit", "256M");
		$this->load->library('mpdf');
		
		// get user
		$user = $this->user_model->get_session();
		$user_type = $this->user_type_model->get_by_id(array( 'id' => $user['user_type_id'] ));
		
		#region generate pdf
		
		// generate signature
		$link_signature = '';
		@mkdir($this->config->item('base_path').'/static/upload/'.date("Y/"));
		@mkdir($this->config->item('base_path').'/static/upload/'.date("Y/m"));
		@mkdir($this->config->item('base_path').'/static/upload/'.date("Y/m/d"));
		if (!empty($_POST['output'])) {
			$img_filename = date("Y/m/d/YmdHis_").rand(1000,9998).'.png';
			$img_filepath = $this->config->item('base_path').'/static/upload/'.$img_filename;
			$img_raw = sigJsonToImage($_POST['output']);
			imagepng($img_raw, $img_filepath);
			imagedestroy($img_raw);
			$link_signature = base_url('static/upload/'.$img_filename);
		}
		
		// generate pdf
		$pdf_name = date("Y/m/d/YmdHis_").rand(1000,9998).'.pdf';
		$pdf_link = base_url('static/upload/'.$pdf_name);
		$pdf_path = $this->config->item('base_path').'/static/upload/'.$pdf_name;
		$template = $this->load->view( 'home_handbook_agreement', array( 'link_signature' => $link_signature ), true );
		$this->mpdf->WriteHTML($template);
		$this->mpdf->Output($pdf_path, 'F');
		
		#endregion generate pdf
		
		#region mail
		
		// principal
		$array_principal = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_PRINCIPAL ));
		foreach ($array_principal as $row) {
			$array_to[] = array(
				'name' => $row['user_display'],
				'email' => $row['user_email']
			);
		}
		
		// administrator
		$array_administrator = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_ADMINISTRATOR ));
		foreach ($array_administrator as $row) {
			$array_to[] = array(
				'name' => $row['user_display'],
				'email' => $row['user_email']
			);
		}
		
		// parent
		if (!empty($user['p_father_email'])) {
			$array_to[] = array(
				'name' => $user['p_father_name'],
				'email' => $user['p_father_email']
			);
		}
		if (!empty($user['p_mother_email'])) {
			$array_to[] = array(
				'name' => $user['p_mother_name'],
				'email' => $user['p_mother_email']
			);
		}
		
		// attachment
		$string_attachment = "\n\nAttachment files :\n".'- <a href="'.$pdf_link.'" target="_blank">Parent / Student Handbook Agreement</a>';
		
		// content
		$content_mail = '
			Thank you for accepting Parent / Student Handbook Agreement for Jafaria Sunday School,
			Please find more information attached to this email.
			
			Let any of our staff know if you have any questions/comments/concerns.
			'.$string_attachment.'
		';
		
		// sent grid
		$param_mail = array(
			'category' => array(
				'title' => 'Parent / Student Handbook Agreement-'.$user['user_display']
			),
			'user_email' => 'system@jafariaschool.org',
			'user_display' => 'Notification System',
			'array_to' => $array_to,
			'subject' => 'Parent / Student Handbook Agreement',
			'content' => $content_mail,
			'title' => $user_type['title']
		);
		$this->mail_model->sent_grid($param_mail);
		
		#endregion mail
		
		#region save handbook
		
		// make sure only single record
		$array_handbook = $this->handbook_model->get_array(array( 'parent_id' => $user['p_id'] ));
		if (count($array_handbook) == 0) {
			
			// insert it
			$param_handbook = array(
				'parent_id' => $user['p_id'],
				'full_name' => $user['p_father_name'],
				'document' => $pdf_name,
				'due_date' => $this->config->item('current_datetime')
			);
			$result_handbook = $this->handbook_model->update($param_handbook);
		}
		
		#endregion save handbook
		
		// result
		$result = array(
			'status' => true,
			'pdf_link' => $pdf_link,
			'message' => 'Thank you for submmiting Parent / Student Handbook Agreement'
		);
		
		echo json_encode($result);
	}
	
	function logout() {
		$this->user_model->del_session();
		header("Location: ".base_url());
		exit;
	}
}