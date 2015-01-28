<?php

class grade extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'grade' );
    }
	
	function grid() {
		// user
		$user = $this->user_model->get_session();
		
		$_POST['is_custom']  = '<span class="cursor-font-awesome icon-link btn-dashboard" title="Go to Dashboard"></span> ';
		$_POST['is_custom'] .= '<span class="cursor-font-awesome icon-envelope btn-report" title="Sent Mail"></span> ';
		$_POST['column'] = array( 's_name', 'father_name', 'father_cell' );
		
		$array = $this->student_model->get_array($_POST);
		$count = $this->student_model->get_count();
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
		
		// email category
		$email_category = $_POST['subject'].'-'.$user['user_display'];
		
		if ($action == 'sent_report') {
			// sent grid
			$param_sent_grid = array(
				'category' => array(
					'asm_group_id' => 10,
					'title' => $email_category
				),
				'user_email' => $user['user_email'],
				'user_display' => $user['user_display'],
				'array_to' => array( array( 'email' => $_POST['to'] ) ),
				'subject' => $_POST['subject'],
				'content' => $_POST['message']
			);
			$this->mail_model->sent_grid($param_sent_grid);
			
			// set result
			$result['status'] = true;
			$result['message'] = 'Email has been sent successfully';
		}
		
		echo json_encode($result);
	}
}