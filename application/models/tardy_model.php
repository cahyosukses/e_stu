<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class tardy_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'student_id', 'due_date', 'parent_subject', 'minute_late', 'reason', 'is_excuse' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, TARDY);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, TARDY);
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
            $select_query  = "SELECT * FROM ".TARDY." WHERE id = '".$param['id']."' LIMIT 1";
        } else if (isset($param['due_date']) && isset($param['student_id'])) {
            $select_query  = "SELECT * FROM ".TARDY." WHERE due_date = '".$param['due_date']."' AND student_id = '".$param['student_id']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$param['field_replace']['total_tardy'] = '';
		$param['field_replace']['student_name'] = 'student.s_name';
		$param['field_replace']['due_date_swap'] = 'tardy.due_date';
		
		$string_namelike = (!empty($param['namelike'])) ? "AND s_name LIKE '%".$param['namelike']."%'" : '';
		$string_due_date = (isset($param['due_date'])) ? "AND tardy.due_date = '".$param['due_date']."'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'due_date DESC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS
				tardy.*, student.s_name student_name, student.s_parent_id parent_id,
				(SELECT COUNT(*) FROM ".TARDY." temp_table_1 WHERE temp_table_1.student_id = tardy.student_id) total_tardy
			FROM ".TARDY." tardy
			LEFT JOIN ".STUDENT." student ON student.s_id = tardy.student_id
			WHERE 1 $string_namelike $string_due_date $string_filter
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
		if (!empty($param['student_id'])) {
			$select_query = "SELECT COUNT(*) total FROM ".TARDY." WHERE student_id = '".$param['student_id']."'";
		} else {
			$select_query = "SELECT FOUND_ROWS() total";
		}
		
		$select_result = mysql_query($select_query) or die(mysql_error());
		$row = mysql_fetch_assoc($select_result);
		$total = $row['total'];
		
		return $total;
    }
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".TARDY." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( 'due_date' ));
		
		// swap data
		if (isset($row['due_date'])) {
			$row['due_date_swap'] = ExchangeFormatDate($row['due_date']);
		}
		
		if (count(@$param['column']) > 0) {
			if (isset($param['grid_type']) && $param['grid_type'] == 'tardy_tracker') {
				$param['is_custom'] = '<span class="cursor-font-awesome icon-pencil btn-edit" title="Edit"></span>';
				/*
				if (empty($row['is_excuse'])) {
					$param['is_custom'] .= '<span class="cursor-font-awesome icon-ok btn-excuse" title="Excuse"></span>';
				}
				/*	*/
				$param['is_custom'] .= '<span class="cursor-font-awesome icon-trash btn-delete" title="Delete"></span> ';
			}
			
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
}
