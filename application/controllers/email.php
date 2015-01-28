<?php

class email extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'email' );
    }
	
	function grid() {
		// user
		$user = $this->user_model->get_session();
		
		$_POST['is_detail'] = 1;
		$_POST['is_delete'] = 1;
		$_POST['user_id'] = $user['user_id'];
		$_POST['user_type_id'] = $user['user_type_id'];
		$_POST['column'] = array( 'from_title', 'subject', 'due_date_title' );
		
		$array = $this->mail_model->get_array($_POST);
		$count = $this->mail_model->get_count();
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
		if ($action == 'update') {
			$result = $this->mail_model->update($_POST);
		}
		else if ($action == 'sent_mail') {
			// mail info
			$mail_info = preg_replace('/\d+/i', 'x', $_POST['mail_info']);
			
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
			
			// get email to
			$array_to = array();
			if ($_POST['mail_info'] == 'Principal') {
				$array_principal = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_PRINCIPAL ));
				foreach ($array_principal as $row) {
					// add email
					$array_to[] = array(
						'name' => $row['user_display'],
						'email' => $row['user_email']
					);
					
					// add inbox message
					$mail_update = array(
						'user_id' => $row['user_id'],
						'user_type_id' => $row['user_type_id'],
						'from_title' => $user['user_display'],
						'from_email' => $user['user_email'],
						'to_email' => $row['user_email'],
						'subject' => $_POST['subject'],
						'content' => $_POST['content'],
						'mail_info' => $_POST['mail_info'],
						'attachment' => $_POST['attachment'],
						'due_date' => $this->config->item('current_datetime')
					);
					$this->mail_model->update($mail_update);
				}
			}
			else if ($_POST['mail_info'] == 'Administrator') {
				$array_administrator = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_ADMINISTRATOR ));
				foreach ($array_administrator as $row) {
					// add email
					$array_to[] = array(
						'name' => $row['user_display'],
						'email' => $row['user_email']
					);
					
					// add inbox message
					$mail_update = array(
						'user_id' => $row['user_id'],
						'user_type_id' => $row['user_type_id'],
						'from_title' => $user['user_display'],
						'from_email' => $user['user_email'],
						'to_email' => $row['user_email'],
						'subject' => $_POST['subject'],
						'content' => $_POST['content'],
						'mail_info' => $_POST['mail_info'],
						'attachment' => $_POST['attachment'],
						'due_date' => $this->config->item('current_datetime')
					);
					$this->mail_model->update($mail_update);
				}
			}
			else if ($_POST['mail_info'] == 'All Teachers') {
				$array_teacher = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_TEACHER, 'limit' => 250 ));
				foreach ($array_teacher as $row) {
					// add email
					$array_to[] = array(
						'name' => $row['user_display'],
						'email' => $row['user_email']
					);
					
					// add inbox message
					$mail_update = array(
						'user_id' => $row['user_id'],
						'user_type_id' => $row['user_type_id'],
						'from_title' => $user['user_display'],
						'from_email' => $user['user_email'],
						'to_email' => $row['user_email'],
						'subject' => $_POST['subject'],
						'content' => $_POST['content'],
						'mail_info' => $_POST['mail_info'],
						'attachment' => $_POST['attachment'],
						'due_date' => $this->config->item('current_datetime')
					);
					$this->mail_model->update($mail_update);
				}
			}
			else if ($_POST['mail_info'] == 'Specific Teachers') {
				$teacher = $this->user_model->get_by_id(array( 'user_id' => $_POST['teacher_select'] ));
				
				// add email
				$array_to[] = array(
					'name' => $teacher['user_display'],
					'email' => $teacher['user_email']
				);
				
				// add inbox message
				$mail_update = array(
					'user_id' => $teacher['user_id'],
					'user_type_id' => $teacher['user_type_id'],
					'from_title' => $user['user_display'],
					'from_email' => $user['user_email'],
					'to_email' => $teacher['user_email'],
					'subject' => $_POST['subject'],
					'content' => $_POST['content'],
					'mail_info' => $_POST['mail_info'],
					'attachment' => $_POST['attachment'],
					'due_date' => $this->config->item('current_datetime')
				);
				$this->mail_model->update($mail_update);
			}
			else if ($_POST['mail_info'] == 'All Parents') {
				$array_parent = $this->student_model->get_array(array( 'limit' => 1000 ));
				foreach ($array_parent as $row) {
					// add email
					if (!empty($row['parent_email'])) {
						$array_to[] = array(
							'name' => $row['father_name'],
							'email' => strtolower($row['parent_email'])
						);
					}
					if (!empty($row['mother_email'])) {
						$array_to[] = array(
							'name' => $row['mother_name'],
							'email' => strtolower($row['mother_email'])
						);
					}
					
					// add inbox message
					$mail_update = array(
						'user_id' => $row['s_id'],
						'user_type_id' => USER_TYPE_PARENT,
						'from_title' => $user['user_display'],
						'from_email' => $user['user_email'],
						'to_email' => $row['parent_email'],
						'subject' => $_POST['subject'],
						'content' => $_POST['content'],
						'mail_info' => $_POST['mail_info'],
						'attachment' => $_POST['attachment'],
						'due_date' => $this->config->item('current_datetime')
					);
					$this->mail_model->update($mail_update);
				}
			}
			else if ($_POST['mail_info'] == 'Specific Parents') {
				$student = $this->student_model->get_by_id(array( 's_id' => $_POST['student_id'] ));
				
				// add email
				if (!empty($student['father_email'])) {
					$array_to[] = array(
						'name' => $student['father_name'],
						'email' => strtolower($student['father_email'])
					);
				}
				if (!empty($student['mother_email'])) {
					$array_to[] = array(
						'name' => $student['mother_name'],
						'email' => strtolower($student['mother_email'])
					);
				}
				
				// add inbox message
				$mail_update = array(
					'user_id' => $student['s_parent_id'],
					'user_type_id' => USER_TYPE_PARENT,
					'from_title' => $user['user_display'],
					'from_email' => $user['user_email'],
					'to_email' => $student['parent_email'],
					'subject' => $_POST['subject'],
					'content' => $_POST['content'],
					'mail_info' => $_POST['mail_info'],
					'attachment' => $_POST['attachment'],
					'due_date' => $this->config->item('current_datetime')
				);
				$this->mail_model->update($mail_update);
			}
			else if ($_POST['mail_info'] == 'Teacher Classroom') {
				$array_user_id = $array_teacher = array();
				$array_teacher_temp = $this->teacher_class_model->get_array($_POST);
				
				// check duplicate teacher
				foreach ($array_teacher_temp as $row) {
					if (in_array($row['user_id'], $array_user_id)) {
						continue;
					} else {
						$array_user_id[] = $row['user_id'];
					}
					
					// add teacher
					$array_teacher[] = $row;
				}
				
				// insert to table
				foreach ($array_teacher as $row) {
					// add email
					$array_to[] = array(
						'name' => $row['user_display'],
						'email' => $row['user_email']
					);
					
					// add inbox message
					$mail_update = array(
						'user_id' => $row['user_id'],
						'user_type_id' => $row['user_type_id'],
						'from_title' => $user['user_display'],
						'from_email' => $user['user_email'],
						'to_email' => $row['user_email'],
						'subject' => $_POST['subject'],
						'content' => $_POST['content'],
						'mail_info' => $_POST['mail_info'],
						'attachment' => $_POST['attachment'],
						'due_date' => $this->config->item('current_datetime')
					);
					$this->mail_model->update($mail_update);
				}
			}
			else if ($mail_info == 'All Parents of Class (x)') {
				preg_match('/\((\d+)\)/i', $_POST['mail_info'], $macth);
				$class_level_id = (isset($macth[1])) ? $macth[1] : 0;
				
				$array_parent = $this->student_model->get_array(array( 'class_level_id' => $class_level_id, 'limit' => 1000 ));
				foreach ($array_parent as $row) {
					// add email
					if (!empty($row['parent_email'])) {
						$array_to[] = array(
							'name' => $row['father_name'],
							'email' => strtolower($row['parent_email'])
						);
					}
					if (!empty($row['mother_email'])) {
						$array_to[] = array(
							'name' => $row['mother_name'],
							'email' => strtolower($row['mother_email'])
						);
					}
					
					// add inbox message
					$mail_update = array(
						'user_id' => $row['s_id'],
						'user_type_id' => USER_TYPE_PARENT,
						'from_title' => $user['user_display'],
						'from_email' => $user['user_email'],
						'to_email' => $row['parent_email'],
						'subject' => $_POST['subject'],
						'content' => $_POST['content'],
						'mail_info' => $_POST['mail_info'],
						'attachment' => $_POST['attachment'],
						'due_date' => $this->config->item('current_datetime')
					);
					$this->mail_model->update($mail_update);
				}
			}
			else if ($mail_info == 'All Parents of Quran (x)') {
				preg_match('/\((\d+)\)/i', $_POST['mail_info'], $macth);
				$quran_level_id = (isset($macth[1])) ? $macth[1] : 0;
				
				$array_parent = $this->student_model->get_array(array( 'quran_level_id' => $quran_level_id, 'limit' => 1000 ));
				foreach ($array_parent as $row) {
					// add email
					if (!empty($row['parent_email'])) {
						$array_to[] = array(
							'name' => $row['father_name'],
							'email' => strtolower($row['parent_email'])
						);
					}
					if (!empty($row['mother_email'])) {
						$array_to[] = array(
							'name' => $row['mother_name'],
							'email' => strtolower($row['mother_email'])
						);
					}
					
					// add inbox message
					$mail_update = array(
						'user_id' => $row['s_id'],
						'user_type_id' => USER_TYPE_PARENT,
						'from_title' => $user['user_display'],
						'from_email' => $user['user_email'],
						'to_email' => $row['parent_email'],
						'subject' => $_POST['subject'],
						'content' => $_POST['content'],
						'mail_info' => $_POST['mail_info'],
						'attachment' => $_POST['attachment'],
						'due_date' => $this->config->item('current_datetime')
					);
					$this->mail_model->update($mail_update);
				}
			}
			else if ($mail_info == 'Custom Teacher (x)') {
				preg_match('/\((\d+)\)/i', $_POST['mail_info'], $macth);
				$user_id = (isset($macth[1])) ? $macth[1] : 0;
				
				// teacher detail
				$teacher = $this->user_model->get_by_id(array( 'user_id' => $user_id ));
				
				// add email
				$array_to[] = array(
					'name' => $teacher['user_display'],
					'email' => $teacher['user_email']
				);
				
				// add inbox message
				$mail_update = array(
					'user_id' => $teacher['user_id'],
					'user_type_id' => $teacher['user_type_id'],
					'from_title' => $user['user_display'],
					'from_email' => $user['user_email'],
					'to_email' => $teacher['user_email'],
					'subject' => $_POST['subject'],
					'content' => $_POST['content'],
					'mail_info' => $_POST['mail_info'],
					'attachment' => $_POST['attachment'],
					'due_date' => $this->config->item('current_datetime')
				);
				$this->mail_model->update($mail_update);
			}
			else if ($_POST['mail_info'] == 'sent_absence') {
				// add student name
				if (empty($_POST['student_id'])) {
					$result['status'] = false;
					$result['message'] = 'Please select student.';
					echo json_encode($result);
					exit;
				} else if ($_POST['student_id'] == 'all') {
					$array_student = $user['array_student'];
				} else {
					$array_student[] = $this->student_model->get_by_id(array( 's_id' => $_POST['student_id'] ));
				}
				
				// insert record
				foreach ($array_student as $row) {
					$param_insert = array(
						'student_id' => $row['s_id'],
						'absence_date' => $_POST['absence_date'],
						'reason' => $_POST['reason'],
						'content' => $_POST['more_detail']
					);
					$this->attendance_absence_model->update($param_insert);
				}
				
				// email check
				$email_check = array();
				
				// add principal
				$array_principal = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_PRINCIPAL ));
				foreach ($array_principal as $row) {
					// email check duplicate
					if (in_array($row['user_email'], $email_check)) {
						continue;
					} else {
						$email_check[] = $row['user_email'];
					}
					
					// add email
					$array_to[] = array(
						'name' => $row['user_display'],
						'email' => $row['user_email']
					);
				}
				
				// add administrator
				$array_administrator = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_ADMINISTRATOR ));
				foreach ($array_administrator as $row) {
					// email check duplicate
					if (in_array($row['user_email'], $email_check)) {
						continue;
					} else {
						$email_check[] = $row['user_email'];
					}
					
					// add email
					$array_to[] = array(
						'name' => $row['user_display'],
						'email' => $row['user_email']
					);
				}
				
				// generate string student
				$string_student = '';
				foreach ($array_student as $key => $row) {
					$string_student .= (empty($string_student)) ? $row['s_name'] : ', '.$row['s_name'];
				}
				
				// collect teacher
				$array_teacher = array();
				foreach ($array_student as $row) {
					$param_teacher = array( 'class_level_id' => $row['class_level_id'], 'quran_level_id' => $row['quran_level_id'] );
					$array_teacher_temp = $this->user_model->get_array($param_teacher);
					$array_teacher = array_merge($array_teacher, $array_teacher_temp);
				}
				foreach ($array_teacher as $row) {
					// email check duplicate
					if (in_array($row['user_email'], $email_check)) {
						continue;
					} else {
						$email_check[] = $row['user_email'];
					}
					
					// add email
					$array_to[] = array(
						'name' => $row['user_display'],
						'email' => $row['user_email']
					);
				}
				
				// generate email body
				$_POST['content'] = 'Dear,
				
Student : '.$string_student.'
Date of Expected Absence : '.get_format_date($_POST['absence_date'], array( 'date_format' => 'm-d-Y' )).'
Reason for Absence : '.$_POST['reason'].'

'.$_POST['more_detail'].'
				';
			}
			else {
				echo $_POST['mail_info']; exit;
			}
			
			// email content
			$content = $_POST['content'];
			
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
			$email_category = $_POST['subject'].'-'.$user['user_display'];
			
			// fix array_to
			$array_temp = $array_to;
			$array_to = array();
			foreach ($array_temp as $row) {
				if (!empty($row['email'])) {
					$array_to[] = $row;
				}
			}
			
			// sent grid
			$param_mail = array(
				'category' => array(
					'asm_group_id' => 10,
					'title' => $email_category
				),
				'user_email' => $user['user_email'],
				'user_display' => $user['user_display'],
				'array_to' => $array_to,
				'subject' => $_POST['subject'],
				'content' => $content,
				'title' => $user_type['title']
			);
			$this->mail_model->sent_grid($param_mail);
			
			// sent a copy
			if (!empty($_POST['send_copy'])) {
				// send grid
				$sendgrid = $this->config_model->get_row(array( 'config_key' => 'sendgrid' ));
				
				$url = 'https://api.sendgrid.com/';
				$params = array(
					'api_user'  => $sendgrid['user'],
					'api_key'   => $sendgrid['passwd'],
					'to'        => $user['user_email'],
					'subject'   => '[copy] '.$_POST['subject'],
					'html'      => '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <style type="text/css">
      body,td{font-size:14px;font-family:Arial, sans-serif; word-wrap: break-word;}
      p{margin: 0; padding: 0}
    </style>
  </head>
  <body style="width: 100%; margin: 0 auto; text-align: left; font-family: Arial;">
    <style type="text/css">.ReadMsgBody{width:100%;}.ExternalClass{width:100%;}span.yshortcuts{color:#000;background-color:none;border:none;}span.yshortcuts:hover,span.yshortcuts:active,span.yshortcuts:focus{color:#000;background-color:none;border:none;}p{margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;}*{-webkit-text-size-adjust:none;}</style><table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;"><tbody><tr><td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;"><div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;"><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td align="center"><table width="600" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#050505;"><tbody><tr><td style="padding-right:4px;padding-top:4px;padding-bottom:4px;padding-left:4px;"><table cellpadding="0" cellspacing="0" style="width:592px;border-collapse:collapse;table-layout:fixed;background:#ffffff;"><tbody><tr><td width="100%" style="vertical-align:top;"><div><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#fcfcfc;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;">
	<tbody><tr><td style="vertical-align:middle;padding-top:10px;padding-bottom:10px;padding-left:0;padding-right:0;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="text-align:center;font-size:11px;margin:0;">
	<em>In The Name of Allah, The Most Gracious, The Most Merciful</em></p>
</div></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td width="100%" style="width:100%;border-top-color:#000;border-top-style:solid;border-top-width:8px;" ></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td style="vertical-align: top;" align="center"><div><img style="border:medium none;width:592px;height:186px;resize:none;position:relative;display:block;top:0px;left:0px;" width="592" height="186" src="http://static.sendgrid.com/uploads/UID_1327300_NL_2714447_dff7cd7f7d21aff2568903893d30c4df/c316cbc174fe8efcdd76a27c06696b79" /></div></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td width="100%" style="width:100%;border-top-color:#000;border-top-style:solid;border-top-width:8px;" ></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;background:#ffffff;" cellspacing="15" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td style="vertical-align:top;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="line-height: normal; background-color: rgb(255, 255, 255);">
	<span style="font-family: book antiqua, palatino; font-size: 12pt;">'.nl2br($content).'<br />
	<br />
	JazakAllah,</span></p>
<p style="font-family: arial, sans-serif; font-size: 13px; line-height: normal; background-color: rgb(255, 255, 255);">
	<span style="font-family: book antiqua, palatino; font-size: 12pt;">Jafaria Education Center</span><br />
	<span style="font-family: book antiqua, palatino; font-size: 12pt;">Web. &nbsp;&nbsp;</span><a href="http://www.jafariaschool.org/" style="font-family: book antiqua, palatino; font-size: 12pt; color: rgb(17, 85, 204);" target="_blank">www.jafariaschool.org</a></p>
</div></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td width="100%" style="width:100%;border-top-color:#000;border-top-style:solid;border-top-width:8px;" ></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;background:#ffffff;" cellspacing="15" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td style="vertical-align:middle;font-size:11px;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="font-size:11px;margin:0px;text-align:left;">
</div></td><td style="vertical-align:middle;font-size:11px;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="font-size:11px;margin:0px;text-align:left;">Jafaria Education Center<br />1546 E La Palma Ave, Anaheim , CA, 92805</p></div></td></tr></tbody></table></td></tr></tbody></table></div></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></div></td></tr></tbody></table>  </body>
</html>
				',
					'text'      => strip_tags($content),
					'from'      => 'school@jafaria.org'
				  );
				$request =  $url.'api/mail.send.json';

				// Generate curl request
				$curl = curl_init($request);
				curl_setopt ($curl, CURLOPT_POST, true);
				curl_setopt ($curl, CURLOPT_POSTFIELDS, $params);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($curl);
				curl_close($curl);
			}
			
			// result
			$result['status'] = true;
			$result['message'] = 'Email successfully sent.';
		}
		else if ($action == 'delete') {
			$result = $this->mail_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}