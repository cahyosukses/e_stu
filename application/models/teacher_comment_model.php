<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class teacher_comment_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'student_id', 'class_type_id', 'comment_good', 'comment_bad' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, TEACHER_COMMENT);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, TEACHER_COMMENT);
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
            $select_query  = "SELECT * FROM ".TEACHER_COMMENT." WHERE id = '".$param['id']."' LIMIT 1";
        } else if (isset($param['student_id']) && isset($param['class_type_id'])) {
			$select_query  = "SELECT * FROM ".TEACHER_COMMENT." WHERE student_id = '".$param['student_id']."' AND class_type_id = '".$param['class_type_id']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'student_id ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS teacher_comment.*
			FROM ".TEACHER_COMMENT." teacher_comment
			WHERE 1 $string_filter
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
	
    function get_student($param = array()) {
        $array = array();
		
		$select_query = "
			SELECT 
				( SELECT CONCAT(comment_good, '<br />', comment_bad) FROM ".TEACHER_COMMENT." WHERE class_type_id = 1 AND student_id = '".$param['student_id']."' ) quran,
				( SELECT CONCAT(comment_good, '<br />', comment_bad) FROM ".TEACHER_COMMENT." WHERE class_type_id = 2 AND student_id = '".$param['student_id']."' ) fiqh,
				( SELECT CONCAT(comment_good, '<br />', comment_bad) FROM ".TEACHER_COMMENT." WHERE class_type_id = 3 AND student_id = '".$param['student_id']."' ) akhlaq,
				( SELECT CONCAT(comment_good, '<br />', comment_bad) FROM ".TEACHER_COMMENT." WHERE class_type_id = 4 AND student_id = '".$param['student_id']."' ) tareekh,
				( SELECT CONCAT(comment_good, '<br />', comment_bad) FROM ".TEACHER_COMMENT." WHERE class_type_id = 5 AND student_id = '".$param['student_id']."' ) aqaid
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array = $row;
		}
		
		// sync
		foreach ($array as $key => $value) {
			$string_check = trim(strip_tags($value));
			if (empty($string_check)) {
				$array[$key] = '-';
			}
		}
		
        return $array;
    }
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".TEACHER_COMMENT." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row);
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
}