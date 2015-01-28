<?php

class sms extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'sms' );
    }
	
	function grid() {
		// user
		$user = $this->user_model->get_session();
		
		$_POST['is_detail'] = 1;
		$_POST['user_id'] = $user['user_id'];
		$_POST['column'] = array( 'sms_name', 'message', 'create_date_title' );
		
		$array = $this->sms_model->get_array($_POST);
		$count = $this->sms_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// load twilio
		$twilio_status = '';
		ini_set("memory_limit", "256M");
		$this->load->library('twilio');
		
		// user
		$user = $this->user_model->get_session();
		
		$result = array( 'status' => false );
		if ($action == 'sent_sms') {
			// sms info
			$sms_info = preg_replace('/\d+/i', 'x', $_POST['sms_info']);
			
			// get phone no
			$array_phone = array();
			if ($_POST['sms_info'] == 'Principal') {
				$array_principal = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_PRINCIPAL ));
				foreach ($array_principal as $row) {
					if (!empty($row['phone'])) {
						$array_phone[] = $row['phone'];
					}
				}
			}
			else if ($_POST['sms_info'] == 'Administrator') {
				$array_administrator = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_ADMINISTRATOR ));
				foreach ($array_administrator as $row) {
					if (!empty($row['phone'])) {
						$array_phone[] = $row['phone'];
					}
				}
			}
			else if ($_POST['sms_info'] == 'All Teachers') {
				$array_teacher = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_TEACHER, 'limit' => 250 ));
				foreach ($array_teacher as $row) {
					if (!empty($row['phone'])) {
						$array_phone[] = $row['phone'];
					}
				}
			}
			else if ($_POST['sms_info'] == 'Specific Teachers') {
				$teacher = $this->user_model->get_by_id(array( 'user_id' => $_POST['teacher_select'] ));
				if (!empty($teacher['phone'])) {
					$array_phone[] = $teacher['phone'];
				}
			}
			else if ($_POST['sms_info'] == 'All Parents') {
				$array_parent = $this->student_model->get_array(array( 'limit' => 1000 ));
				foreach ($array_parent as $row) {
					if (!empty($row['father_cell']) && !in_array($row['father_cell'], $array_phone)) {
						$array_phone[] = $row['father_cell'];
					}
					if (!empty($row['mother_cell']) && !in_array($row['mother_cell'], $array_phone)) {
						$array_phone[] = $row['mother_cell'];
					}
				}
			}
			else if ($_POST['sms_info'] == 'Specific Parents') {
				$student = $this->student_model->get_by_id(array( 's_id' => $_POST['student_id'] ));
				if (!empty($student['father_cell'])) {
					$array_phone[] = $student['father_cell'];
				}
				if (!empty($student['mother_cell'])) {
					$array_phone[] = $student['mother_cell'];
				}
			}
			else if ($sms_info == 'All Parents of Class (x)') {
				preg_match('/\((\d+)\)/i', $_POST['sms_info'], $macth);
				$class_level_id = (isset($macth[1])) ? $macth[1] : 0;
				
				$array_parent = $this->student_model->get_array(array( 'class_level_id' => $class_level_id, 'limit' => 1000 ));
				foreach ($array_parent as $row) {
					if (!empty($row['father_cell'])) {
						$array_phone[] = $row['father_cell'];
					}
					if (!empty($row['mother_cell'])) {
						$array_phone[] = $row['mother_cell'];
					}
				}
			}
			else {
				echo $_POST['sms_info']; exit;
			}
			
			// validation
			if (count($array_phone) == 0) {
				$result['status'] = false;
				$result['message'] = 'Sorry, no phone number available.';
				echo json_encode($result);
				exit;
			}
			
			// twilio config
			$twilio = $this->config_model->get_row(array( 'config_key' => 'twilio' ));
			
			// sent sms
			$client = new Services_Twilio($twilio['sid'], $twilio['token']);
			try {
				foreach ($array_phone as $phone_no) {
					$message = $client->account->messages->sendMessage(
						// Step 6: Change the 'From' number below to be a valid Twilio number 
						// that you've purchased, or the (deprecated) Sandbox number
						$twilio['phone_no'],
						
						// the number we are sending to - Any phone number
						$phone_no,

						// the sms body
						$_POST['message']
					);
					$twilio_status .= (empty($twilio_status)) ? $message->sid : ','.$message->sid;
				}
				
				/*
				// it's work
				foreach ($array_phone as $phone_no) {
					$param_message = array( "From" => $twilio['phone_no'], "To" => $phone_no, "Body" => $_POST['message'] );
					$message = $client->account->messages->create($param_message);
					$twilio_status .= (empty($twilio_status)) ? $message->sid : ','.$message->sid;
				}
				/*	*/
			} catch(Exception $e) {
				$result['status'] = false;
				$result['message'] = $e->getMessage();
				$result['message'] = preg_replace('/(\.|\;) /i', "$1\n", $result['message']);
				echo json_encode($result);
				exit;
			}
			
			// update
			$param_sms = $_POST;
			$param_sms['user_id'] = $user['user_id'];
			$param_sms['sms_no'] = implode(',', $array_phone);
			$param_sms['twilio_status'] = $twilio_status;
			$param_sms['create_date'] = $this->config->item('current_datetime');
			$result = $this->sms_model->update($param_sms);
			$result['message'] = count($array_phone).' SMS successfully sent.';
		}
		else if ($action == 'delete') {
			$result = $this->sms_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}