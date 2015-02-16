<?php
class combo extends CI_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$action = (!empty($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		$array = array();
		if ($action == 'schedule') {
			$param = $_POST;
			$param['limit'] = 500;
			$param['parent_id'] = 0;
			$param['sort'] = '[{"property":"time_frame","direction":"ASC"}]';
			$param['time_frame_min'] = $this->config->item('current_datetime');
			$array = $this->schedule_model->get_array($param);
			echo ShowOption(array( 'Array' => $array, 'ArrayID' => 'id', 'ArrayTitle' => 'time_frame_title' ));
		} else if ($action == 'student') {
			$array = $this->student_model->get_array($_POST);
			echo ShowOption(array( 'Array' => $array, 'ArrayID' => 's_id', 'ArrayTitle' => 's_name' ));
		}
	}
}
