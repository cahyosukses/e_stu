<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class attendance_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'user_id', 'class_type_id', 'quran_level_id', 'class_level_id', 'due_date', 'title' );
    }
	
    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, ATTENDANCE);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, ATTENDANCE);
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
            $select_query  = "SELECT * FROM ".ATTENDANCE." WHERE id = '".$param['id']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$param['field_replace']['due_date_swap'] = 'attendance.due_date';
		
		$string_namelike = (!empty($param['namelike'])) ? "AND title LIKE '%".$param['namelike']."%'" : '';
		$string_class_type = (isset($param['class_type_id'])) ? "AND attendance.class_type_id = '".$param['class_type_id']."'" : '';
		$string_quran_level = (isset($param['quran_level_id'])) ? "AND attendance.quran_level_id = '".$param['quran_level_id']."'" : '';
		$string_class_level = (isset($param['class_level_id'])) ? "AND attendance.class_level_id = '".$param['class_level_id']."'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'attendance.due_date DESC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS attendance.*, user.user_display
			FROM ".ATTENDANCE." attendance
			LEFT JOIN ".USER." user ON user.user_id = attendance.user_id
			WHERE 1 $string_namelike $string_class_type $string_quran_level $string_class_level $string_filter
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
		$select_query = "SELECT FOUND_ROWS() total";
		$select_result = mysql_query($select_query) or die(mysql_error());
		$row = mysql_fetch_assoc($select_result);
		$total = $row['total'];
		
		return $total;
    }
	
    function delete($param) {
		$param['truncate'] = (isset($param['truncate'])) ? $param['truncate'] : false;
		
		if ($param['truncate']) {
			$delete_query  = "TRUNCATE TABLE ".ATTENDANCE_STUDENT;
			$delete_result = mysql_query($delete_query) or die(mysql_error());
			
			$delete_query  = "TRUNCATE TABLE ".ATTENDANCE;
			$delete_result = mysql_query($delete_query) or die(mysql_error());
		} else {
			$delete_query  = "DELETE FROM ".ATTENDANCE_STUDENT." WHERE attendance_id = '".$param['id']."'";
			$delete_result = mysql_query($delete_query) or die(mysql_error());
			
			$delete_query  = "DELETE FROM ".ATTENDANCE." WHERE id = '".$param['id']."' LIMIT 1";
			$delete_result = mysql_query($delete_query) or die(mysql_error());
		}
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( 'due_date' ));
		
		// due date swap
		$row['due_date_swap'] = ExchangeFormatDate($row['due_date']);
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
}