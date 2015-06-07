<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class register_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'student_id', 'is_paid', 'handbook', 'due_date', 'status' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, REGISTER);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, REGISTER);
            $update_result = mysql_query($update_query) or die(mysql_error());
           
            $result['id'] = $param['id'];
            $result['status'] = '1';
            $result['message'] = 'Data successfully updated.';
        }
       
        return $result;
    }

    function get_by_id($param) {
        $array = array();
		
        if (isset($param['id'])) {
            $select_query  = "SELECT * FROM ".REGISTER." WHERE id = '".$param['id']."' LIMIT 1";
        }
		
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$param['field_replace']['paid_text'] = '';
		$param['field_replace']['student_name'] = 'student.s_name';
		$param['field_replace']['due_date_text'] = 'register.due_date';
		$param['field_replace']['father_name'] = 'parents.p_father_name';
		$param['field_replace']['mother_name'] = 'parents.p_mother_name';
		
		$string_id = (!empty($param['id'])) ? "AND register.id = '".$param['id']."'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'register.due_date DESC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS
				register.*, student.s_name student_name,
				parents.p_father_name father_name, parents.p_mother_name mother_name
			FROM ".REGISTER." register
			LEFT JOIN ".STUDENT." student ON student.s_id = register.student_id
			LEFT JOIN ".PARENTS." parents ON student.s_parent_id = parents.p_id
			WHERE 1 $string_id $string_filter
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
    }
	
    function get_non_register($param = array()) {
        $array = array();
		
		$param['field_replace']['paid_text'] = '';
		$param['field_replace']['student_name'] = 'student.s_name';
		$param['field_replace']['father_name'] = 'parents.p_father_name';
		$param['field_replace']['father_email'] = 'parents.p_father_email';
		$param['field_replace']['mother_name'] = 'parents.p_mother_name';
		
		$string_parent = (isset($param['parent_id'])) ? "AND student.s_parent_id = '".$param['parent_id']."'" : '';
		$string_sorting = GetStringSorting($param, @$param['column'], 'student.s_name ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS
				student.*, student.s_name student_name, register.id register_id,
				parents.p_id parent_id, parents.p_father_name father_name, parents.p_father_email father_email,
				parents.p_mother_name mother_name, parents.p_mother_email mother_email
			FROM ".STUDENT." student
			LEFT JOIN ".REGISTER." register ON register.student_id = student.s_id
			LEFT JOIN ".PARENTS." parents ON student.s_parent_id = parents.p_id
			WHERE register.id IS NULL $string_parent
			ORDER BY $string_sorting
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
			$select_query = "SELECT COUNT(*) total FROM ".REGISTER."";
		} else {
			$select_query = "SELECT FOUND_ROWS() total";
		}
		
		$select_result = mysql_query($select_query) or die(mysql_error());
		$row = mysql_fetch_assoc($select_result);
		$total = $row['total'];
		
		return $total;
    }
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".REGISTER." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( ));
		
		// labeling
		$row['paid_text'] = '-';
		$row['due_date_text'] = '-';
		if (isset($row['due_date']) && isset($row['status'])) {
			$row['due_date_text'] = ($row['status'] == 'register') ? $row['due_date'] : 'unregister';
		}
		if (isset($row['is_paid'])) {
			$row['paid_text'] = ($row['is_paid'] == 0) ? 'No' : 'Yes';
		}
		
		// link
		if (isset($row['handbook'])) {
			$row['handbook_link'] = base_url('static/upload/'.$row['handbook']);
		}
		
		if (count(@$param['column']) > 0) {
			if (isset($param['grid_type'])) {
				if ($param['grid_type'] == 'register_grid') {
					$param['is_custom'] = '&nbsp;';
					if ($row['status'] == 'register') {
						$param['is_custom'] = '<span class="cursor-font-awesome icon-link btn-document" title="Document"></span>';
						/*
						if (empty($row['is_paid'])) {
							$param['is_custom'] .= '<span class="cursor-font-awesome icon-ok btn-paid" title="Paid"></span>';
						} else {
							$param['is_custom'] .= '<span class="cursor-font-awesome icon-remove btn-unpaid" title="Unpaid"></span>';
						}
						/*	*/
					}
				}
			}
			
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
}
