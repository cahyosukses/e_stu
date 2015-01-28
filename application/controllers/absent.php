<?php

class absent extends SE_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$is_login = $this->user_model->is_login(array( 'user_type_id' => USER_TYPE_TEACHER ));
		
		if ($is_login) {
			$this->load->view( 'attendance', array( 'view_type' => 'stand-alone' ) );
		} else {
			$this->load->view( 'signin' );
		}
    }
}