<?php

class setup_account extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'setup_account' );
    }
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// result default
		$result = array( 'status' => false );
		
		if ($action == 'sendgrid') {
			// raw param
			$param['user'] = $_POST['user'];
			$param['passwd'] = $_POST['passwd'];
			
			// update
			$param_update['config_key'] = $action;
			$param_update['config_value'] = json_encode($param);
			$result = $this->config_model->update_by_key($param_update);
		}
		else if ($action == 'twilio') {
			// raw param
			$param['sid'] = $_POST['sid'];
			$param['token'] = $_POST['token'];
			$param['phone_no'] = $_POST['phone_no'];
			
			// update
			$param_update['config_key'] = $action;
			$param_update['config_value'] = json_encode($param);
			$result = $this->config_model->update_by_key($param_update);
		}
		
		echo json_encode($result);
	}
}