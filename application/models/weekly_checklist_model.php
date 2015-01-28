<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class weekly_checklist_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'student_id', 'date_check', 'duration', 'content' );
    }
	
    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, WEEKLY_CHECKLIST);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, WEEKLY_CHECKLIST);
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
            $select_query  = "SELECT * FROM ".WEEKLY_CHECKLIST." WHERE id = '".$param['id']."' LIMIT 1";
        } else if (isset($param['date_check']) && isset($param['student_id'])) {
			$select_query  = "SELECT * FROM ".WEEKLY_CHECKLIST." WHERE date_check = '".$param['date_check']."' AND student_id = '".$param['student_id']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$string_student = (isset($param['student_id'])) ? "AND weekly_checklist.student_id = '".$param['student_id']."'" : '';
		$string_start_date = (isset($param['start_date'])) ? "AND weekly_checklist.date_check >= '".$param['start_date']."'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'weekly_checklist.date_check ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS weekly_checklist.*
			FROM ".WEEKLY_CHECKLIST." weekly_checklist
			WHERE 1 $string_student $string_start_date $string_filter
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
    }
	
	function get_dashboard($param = array()) {
		// make sure start_date is sunday
		for ($i = 0; $i < 7; $i++) {
			$date_check = add_date($param['start_date'], '-'.$i.' days');
			$days = get_format_date($date_check, array( 'date_format' => 'l' ));
			if ($days == 'Sunday') {
				$param['start_date'] = $date_check;
			}
		}
		
		// check or force insert record
		for ($i = 0; $i < 7; $i++) {
			$date_insert = add_date($param['start_date'], $i.' days');
			
			// insert if record is not exist
			$record = $this->get_by_id(array( 'date_check' => $date_insert, 'student_id' => $param['student_id'] ));
			if (count($record) == 0) {
				$param_insert = array(
					'student_id' => $param['student_id'],
					'date_check' => $date_insert,
				);
				$this->update($param_insert);
			}
		}
		
		// get array dashboard
		$param_dashboard = $param;
		$param_dashboard['limit'] = 7;
		return $this->get_array($param_dashboard);
	}
	
    function get_count($param = array()) {
		$select_query = "SELECT FOUND_ROWS() total";
		$select_result = mysql_query($select_query) or die(mysql_error());
		$row = mysql_fetch_assoc($select_result);
		$total = $row['total'];
		
		return $total;
    }
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".WEEKLY_CHECKLIST." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( ));
		
		// due date swap
		if (isset($row['date_check'])) {
			$row['date_check_swap'] = ExchangeFormatDate($row['date_check']);
			$row['day_info'] = get_format_date($row['date_check'], array( 'date_format' => 'l' ));
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
	
	function get_summary_reading_time($param) {
		$param['limit'] = 7;
		$array_weekly_checklist = $this->get_array($param);
		
		// get total duration
		$duration = 0;
		foreach ($array_weekly_checklist as $row) {
			$duration += $row['duration'];
		}
		
		// set max duration
		// $duration = ($duration > 105) ? 105 : $duration;
		
		// calculate score
		$result = array(
			'status' => true,
			'score' => round(($duration / 105) * 100)
		);
		
		return $result;
	}
}