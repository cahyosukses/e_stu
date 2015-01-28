<?php
class combo extends CI_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$action = (!empty($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		$array = array();
		if ($action == 'student') {
			$array = $this->student_model->get_array($_POST);
			echo ShowOption(array( 'Array' => $array, 'ArrayID' => 's_id', 'ArrayTitle' => 's_name' ));
		}
	}
}
