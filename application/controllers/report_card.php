<?php

class report_card extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'report_card' );
    }
	
	function grid() {
		$_POST['grid_type'] = 'report_card';
		$_POST['column'] = array( 'father_name', 'mother_name', 'student_count' );
		
		$array = $this->parents_model->get_array_child($_POST);
		$count = $this->parents_model->get_count();
		
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		echo json_encode($grid);
	}
	
	function action() {
		ini_set("memory_limit", "256M");
		$this->load->library('mpdf');
		
		// result default
		$result = array( 'status' => false );
		
		// page data
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// action
		if ($action == 'generate_report') {
			// generate pdf
			$this->parents_model->generate_report_card(array( 'parent_id' => $_POST['parent_id'] ));
			
			// set result
			$result = array( 'status' => true, 'message' => 'Report generated successfully' );
		}
		else if ($action == 'generate_all') {
			// parent total
			$result['parent_total'] = $this->parents_model->get_count(array( 'is_query' => true ));
			
			// selected parent
			$param_parent = array( 'start' => $_POST['start'], 'limit' => 1 );
			$array_parent = $this->parents_model->get_array($param_parent);
			foreach ($array_parent as $row) {
				$this->parents_model->generate_report_card(array( 'parent_id' => $row['p_id'] ));
			}
			
			// result data
			$parent_counter = $_POST['start'] + 1;
			$parent_percent = round(($parent_counter / $result['parent_total']) * 100);
			$is_complete = ($parent_counter >= $result['parent_total']) ? true : false;
			
			// finalize
			if ($is_complete && isset($_POST['finalize']) && $_POST['finalize']) {
				// update config
				$report_card_finalize = $this->config_model->get_by_id(array( 'config_key' => 'report-card-finalize' ));
				$param_update = array( 'config_id' => $report_card_finalize['config_id'], 'config_value' => $this->config->item('current_datetime') );
				$result_update = $this->config_model->update($param_update);
				
				// sent email
				$this->parents_model->send_report_card(array());
			}
			
			// set status
			sleep(1);
			$result['status'] = true;
			$result['is_complete'] = $is_complete;
			$result['message'] = $is_complete ? 'Generate complete' : 'Generating for parent #'.$parent_counter.' - '.$parent_percent.'%, Generating for Parent #'.($parent_counter + 1);
		}
		else if ($action == 'email_report') {
			$result = $this->parents_model->send_report_card($_POST);
		}
		
		echo json_encode($result);
	}
}
