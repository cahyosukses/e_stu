<?php

class email extends SE_Controller {
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
						'due_date' => $this->config->item('current_datetime')
					);
					$this->mail_model->update($mail_update);
				}
			}
			else if ($_POST['mail_info'] == 'All Teachers') {
				$array_teacher = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_TEACHER, 'limit' => 50 ));
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
					'user_id' => $student['s_id'],
					'user_type_id' => USER_TYPE_PARENT,
					'from_title' => $user['user_display'],
					'from_email' => $user['user_email'],
					'to_email' => $student['parent_email'],
					'subject' => $_POST['subject'],
					'content' => $_POST['content'],
					'mail_info' => $_POST['mail_info'],
					'due_date' => $this->config->item('current_datetime')
				);
				$this->mail_model->update($mail_update);
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
					'due_date' => $this->config->item('current_datetime')
				);
				$this->mail_model->update($mail_update);
			}
			else {
				echo $_POST['mail_info']; exit;
			}
			
			// sent grid
			$param_sent_grid = array(
				'from' => $user['user_email'],
				'fromname' => $user['user_display'],
				'to' => $array_to,
				'subject' => $_POST['subject'],
				'text' => $_POST['content'],
				'html' => '
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
	<span style="font-family: book antiqua, palatino; font-size: 12pt;">'.nl2br($_POST['content']).'<br />
	<br />
	JazakAllah,</span></p>
<p style="font-family: arial, sans-serif; font-size: 13px; line-height: normal; background-color: rgb(255, 255, 255);">
	<span style="font-family: book antiqua, palatino; font-size: 12pt;">'.$user['user_display'].'</span><br />
	<span style="font-family: book antiqua, palatino; font-size: 12pt;">Title '.$user_type['title'].',&nbsp;Jafaria Education Center</span><br />
	<span style="font-family: book antiqua, palatino; font-size: 12pt;">Email.&nbsp;</span><a href="mailto:'.$user['user_email'].'" style="font-family: book antiqua, palatino; font-size: 12pt; color: rgb(17, 85, 204);" target="_blank">'.$user['user_email'].'</a><br />
	<span style="font-family: book antiqua, palatino; font-size: 12pt;">Web. &nbsp;&nbsp;</span><a href="http://www.jafariaschool.org/" style="font-family: book antiqua, palatino; font-size: 12pt; color: rgb(17, 85, 204);" target="_blank">www.jafariaschool.org</a></p>
</div></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td width="100%" style="width:100%;border-top-color:#000;border-top-style:solid;border-top-width:8px;" ></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;background:#ffffff;" cellspacing="15" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td style="vertical-align:middle;font-size:11px;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="font-size:11px;margin:0px;text-align:left;">
</div></td><td style="vertical-align:middle;font-size:11px;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="font-size:11px;margin:0px;text-align:left;">Jafaria Education Center<br />1546 E La Palma Ave, Anaheim , CA, 92805</p></div></td></tr></tbody></table></td></tr></tbody></table></div></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></div></td></tr></tbody></table>  </body>
</html>
					'
			);
			sent_grid($param_sent_grid);
			
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