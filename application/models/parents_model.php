<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class parents_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array(
			'p_id', 'p_status', 'p_father_name', 'p_father_email', 'p_father_cell', 'p_mother_name', 'p_mother_email',
			'p_mother_cell', 'p_phone', 'p_address', 'p_apt', 'p_city', 'p_state', 'p_zip', 'p_signature', 'p_passwd', 'p_pdf2014', 'p_sign_image',
			'report_card'
		);
    }

    function update($param) {
        $result = array();
       
        if (empty($param['p_id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, PARENTS);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, PARENTS);
            $update_result = mysql_query($update_query) or die(mysql_error());
           
            $result['id'] = $param['p_id'];
            $result['status'] = '1';
            $result['message'] = 'Data successfully updated.';
        }
       
        return $result;
    }

    function get_by_id($param) {
        $array = array();
		
        if (isset($param['p_id'])) {
            $select_query  = "SELECT * FROM ".PARENTS." WHERE p_id = '".$param['p_id']."' LIMIT 1";
        } else if (isset($param['p_father_email'])) {
            $select_query  = "
				SELECT *
				FROM ".PARENTS."
				WHERE
					p_father_email = '".$param['p_father_email']."'
					OR p_mother_email = '".$param['p_father_email']."'
				LIMIT 1";
        }
		
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$string_namelike = (!empty($param['namelike'])) ? "AND parents.p_father_name LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'parents.p_father_name ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS parents.*
			FROM ".PARENTS." parents
			WHERE 1 $string_namelike $string_filter
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
    }

    function get_array_child($param = array()) {
        $array = array();
		
		$param['field_replace']['student_count'] = '';
		$param['field_replace']['father_name'] = 'parents.p_father_name';
		$param['field_replace']['mother_name'] = 'parents.p_mother_name';
		
		$string_namelike = (!empty($param['namelike'])) ? "AND parents.p_father_name LIKE '%".$param['namelike']."%'" : '';
		$string_parent = (isset($param['parent_id'])) ? "AND parents.p_id = '".$param['parent_id']."'" : '';
		$string_quran_level_in = (isset($param['quran_level_in'])) ? "AND student.quran_level_id IN (".$param['quran_level_in'].")" : '';
		$string_class_level_in = (isset($param['class_level_in'])) ? "AND student.class_level_id IN (".$param['class_level_in'].")" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'parents.p_id ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS parents.p_id parent_id, p_father_name father_name, p_mother_name mother_name,
				p_father_email, p_mother_email, report_card, COUNT(*) AS student_count
			FROM ".PARENTS." parents
			LEFT JOIN ".STUDENT." student ON student.s_parent_id = parents.p_id
			WHERE 1
				$string_namelike $string_parent
				$string_quran_level_in $string_class_level_in
				$string_filter
			GROUP BY parents.p_id
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
    }
	
	function get_array_autocomplete($param = array()) {
        $array = array();
		
		$string_sorting = GetStringSorting($param, @$param['column'], 'parents.p_father_name ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			(	SELECT p_id, p_father_name p_father_name, p_father_email
				FROM ".PARENTS."
				WHERE p_father_name LIKE '%".$param['namelike']."%'
			) UNION (
				SELECT p_id, p_mother_name p_father_name, p_father_email
				FROM ".PARENTS." parents
				WHERE p_mother_name LIKE '%".$param['namelike']."%'
			)
			ORDER BY p_father_name ASC
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
	}
	
    function get_count($param = array()) {
		if (isset($param['is_query'])) {
			$select_query = "SELECT COUNT(*) total FROM ".PARENTS."";
		} else {
			$select_query = "SELECT FOUND_ROWS() total";
		}
		
		$select_result = mysql_query($select_query) or die(mysql_error());
		$row = mysql_fetch_assoc($select_result);
		$total = $row['total'];
		
		return $total;
    }
	
	function generate_report_card($param = array()) {
		// generate report card
		@mkdir($this->config->item('base_path').'/static/temp/'.date("Y/"));
		@mkdir($this->config->item('base_path').'/static/temp/'.date("Y/m"));
		@mkdir($this->config->item('base_path').'/static/temp/'.date("Y/m/d"));
		$pdf_name = date("Y/m/d/YmdHis_").rand(1000,9998).'.pdf';
		$pdf_path = $this->config->item('base_path').'/static/temp/'.$pdf_name;
		$template = $this->load->view( 'report_card_pdf', $param, true );
		$this->mpdf->WriteHTML($template);
		$this->mpdf->Output($pdf_path, 'F');
		
		// update report card
		$param = array( 'p_id' => $param['parent_id'], 'report_card' => $pdf_name );
		$this->parents_model->update($param);
	}
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".PARENTS." WHERE p_id = '".$param['p_id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( ));
		
		// sync with user table
		if (isset($row['p_id'])) {
			$row['user_id'] = $row['p_id'];
		}
		if (isset($row['p_father_name'])) {
			$row['user_display'] = $row['p_father_name'];
		}
		$row['user_type_id'] = USER_TYPE_PARENT;
		if (isset($row['p_passwd'])) {
			$row['user_pword'] = $row['p_passwd'];
		}
		if (isset($row['p_father_email'])) {
			$row['user_email'] = $row['p_father_email'];
		}
		
		// link
		if (!empty($row['p_sign_image'])) {
			$row['p_sign_image_link'] = base_url('static/upload/'.$row['p_sign_image']);
		}
		if (!empty($row['report_card'])) {
			$row['report_card_link'] = base_url('static/temp/'.$row['report_card']);
		}
		
		if (count(@$param['column']) > 0) {
			if (isset($param['grid_type'])) {
				if ($param['grid_type'] == 'report_card') {
					$param['is_custom'] = '<span class="cursor-font-awesome icon-pencil btn-edit" title="Generate Report Card"></span>';
					if (!empty($row['report_card'])) {
						$param['is_custom'] .= '<span class="cursor-font-awesome icon-link btn-preview" title="View Report Card"></span>';
						$param['is_custom'] .= '<span class="cursor-font-awesome icon-envelope btn-email" title="Send Email"></span>';
					}
				}
			}
			
			
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
	
	function send_report_card($param = array()) {
		// user
		$user = $this->user_model->get_session();
		$user_type = $this->user_type_model->get_by_id(array( 'id' => $user['user_type_id'] ));
		
		// add email
		$array_to = $array_sub = array();
		$array_parent = $this->parents_model->get_array_child($param);
		foreach ($array_parent as $row) {
			if (!empty($row['p_father_email'])) {
				$array_to[] = array(
					'name' => $row['father_name'],
					'email' => strtolower($row['p_father_email'])
				);
				$array_sub['-parent_name-'][] = $row['father_name'];
				$array_sub['-link_report_card-'][] = '<a href="'.$row['report_card_link'].'" target="_blank">Report Card</a>';
			}
			if (!empty($row['p_mother_email'])) {
				$array_to[] = array(
					'name' => $row['mother_name'],
					'email' => strtolower($row['p_mother_email'])
				);
				$array_sub['-parent_name-'][] = $row['mother_name'];
				$array_sub['-link_report_card-'][] = '<a href="'.$row['report_card_link'].'" target="_blank">Report Card</a>';
			}
		}
		
		// get content
		$content = $this->config_model->get_by_id(array( 'config_key' => 'report-card-email' ));
		
		// sent grid
		$param_mail = array(
			'user_email' => $user['user_email'],
			'user_display' => $user['user_display'],
			'array_to' => $array_to,
			'array_sub' => $array_sub,
			'subject' => 'Report Card',
			'content' => $content['config_value'],
			'title' => $user_type['title']
		);
		$this->mail_model->sent_grid($param_mail);
		
		$result = array( 'status' => true, 'message' => 'Email sent.' );
		return $result;
	}
}
