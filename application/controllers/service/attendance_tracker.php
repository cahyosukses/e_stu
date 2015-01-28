<?php

class attendance_tracker extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$result = $this->attendance_student_model->sent_notification(array( 'force_sent' => true, 'due_date' => $_POST['due_date'] ));
		echo json_encode($result);
		exit;
    }
}
