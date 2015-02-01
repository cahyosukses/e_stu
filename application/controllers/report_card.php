<?php

class report_card extends SE_Login_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'report_card' );
    }
	
	function grid() {
		$_POST['is_custom'] = '<span class="cursor-font-awesome icon-pencil btn-edit" title="Edit"></span>';
		// $_POST['is_detail'] = '<span class="cursor-font-awesome icon-reorder btn-detail" title="Detail"></span> ';
		$_POST['column'] = array( 'father_name', 'mother_name', 'student_count' );
		
		$array = $this->parents_model->get_array_child($_POST);
		$count = $this->parents_model->get_count();
		
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		echo json_encode($grid);
	}
	
	function action() {
		ini_set("memory_limit", "256M");
		$this->load->library('mpdf');
		
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		$param = array('s_parent_id' => $action['parent_id']);
		$array = $this->student_model->get_array($param);
		
		$data = array(
			'parent'  => $action,
			'student' => $array
		);
		
		// generate pdf
		@mkdir($this->config->item('base_path').'/static/temp/'.date("Y/"));
		@mkdir($this->config->item('base_path').'/static/temp/'.date("Y/m"));
		@mkdir($this->config->item('base_path').'/static/temp/'.date("Y/m/d"));
		$pdf_name = date("Y/m/d/YmdHis_").rand(1000,9998).'.pdf';
		$pdf_path = $this->config->item('base_path').'/static/temp/'.$pdf_name;
		$template = $this->load->view( 'report_card_pdf', $data, true );
		$this->mpdf->WriteHTML($template);
		$this->mpdf->Output($pdf_path, 'F');
		// $this->mpdf->Output();
		
		// result default
		$result = array( 'status' => false );
		
		/*
		// user
		$user = $this->user_model->get_session();
		$user_type = $this->user_type_model->get_by_id(array( 'id' => $user['user_type_id'] ));
		
		// student
		if ($action == 'update_comment') {
			$result = $this->student_model->update($_POST);
		} else if ($action == 'get_student') {
			$result = $this->student_model->get_by_id(array( 's_id' => $_POST['student_id'] ));
		}
		*/
		echo json_encode($result);
	}
}
