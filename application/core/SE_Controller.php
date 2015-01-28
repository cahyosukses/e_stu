<?php

class SE_Controller extends CI_Controller {
    function __construct() {
        parent::__construct();
		
		$this->task_model->generate_quran_task();
    }
}

class SE_Login_Controller extends SE_Controller {
    function __construct() {
        parent::__construct();
		
		$this->user_model->required_login();
    }
}