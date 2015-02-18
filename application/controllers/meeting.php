<?php

class meeting extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'meeting' );
    }
	
	function grid() {
		$user = $this->user_model->get_session();
		$grid_type = (isset($_POST['grid_type'])) ? $_POST['grid_type'] : 'meeting_list';
		
		if ($grid_type == 'meeting_list') {
			$_POST['is_edit_only'] = 1;
			$_POST['parent_id'] = $user['p_id'];
			$_POST['column'] = array( 'time_frame_title', 'user_display', 'student_name' );
			
			$array = $this->schedule_model->get_array($_POST);
			$count = $this->schedule_model->get_count($_POST);
		} else if ($grid_type == 'meeting_required') {
			$_POST['is_edit_only'] = 1;
			$_POST['parent_id'] = $user['p_id'];
			$_POST['column'] = array( 'user_display', 'student_name' );
			
			$array_teacher = $this->schedule_model->get_teacher_without_schedule($_POST);
			$array = $array_teacher['datatable'];
			$count = count($array_teacher['array_teacher']);
		}
		
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
		if ($action == 'update_schdule') {
			$schedule = $this->schedule_model->get_by_id(array( 'id' => $_POST['id'] ));
			
			// validate
			$param_check = array(
				'parent_id' => $user['p_id'],
				'time_frame' => $schedule['time_frame']
			);
			$array_check = $this->schedule_model->get_array($param_check);
			if (count($array_check) > 0) {
				$result['status'] = '0';
				$result['message'] = 'You have already reserved this tme.';
				echo json_encode($result);
				exit;
			}
			
			// remove selected schedule
			$param_schedule = array(
				'user_id' => $schedule['user_id'],
				'parent_id' => $user['p_id']
			);
			$array_schedule = $this->schedule_model->get_array($param_schedule);
			foreach ($array_schedule as $row) {
				$this->schedule_model->update(array( 'id' => $row['id'], 'parent_id' => 0 ));
			}
			
			// update selected schedule
			$result = $this->schedule_model->update(array( 'id' => $_POST['id'], 'parent_id' => $user['p_id'] ));
		}
		
		echo json_encode($result);
	}
}