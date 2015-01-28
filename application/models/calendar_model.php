<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class calendar_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'user_id', 'start_date', 'end_date', 'title', 'content' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, CALENDAR);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, CALENDAR);
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
            $select_query  = "SELECT * FROM ".CALENDAR." WHERE id = '".$param['id']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$param['field_replace']['start_date_swap'] = 'calendar.start_date';
		
		$string_namelike = (!empty($param['namelike'])) ? "AND title LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'start_date ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS calendar.*, user.user_display
			FROM ".CALENDAR." calendar
			LEFT JOIN ".USER." user ON user.user_id = calendar.user_id
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

    function get_count($param = array()) {
		$select_query = "SELECT FOUND_ROWS() total";
		$select_result = mysql_query($select_query) or die(mysql_error());
		$row = mysql_fetch_assoc($select_result);
		$total = $row['total'];
		
		return $total;
    }
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".CALENDAR." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( 'start_date', 'end_date' ));
		
		// date time
		if (isset($row['start_date'])) {
			$array_temp = explode(' ', $row['start_date']);
			$row['start_temp_date'] = $array_temp[0];
			$row['start_temp_time'] = $array_temp[1];
		}
		if (isset($row['end_date'])) {
			$array_temp = explode(' ', $row['end_date']);
			$row['end_temp_date'] = $array_temp[0];
			$row['end_temp_time'] = $array_temp[1];
		} else {
			$row['end_temp_date'] = '';
			$row['end_temp_time'] = '';
		}
		
		// swap data
		if (isset($row['start_date'])) {
			$row['start_date_swap'] = ExchangeFormatDate($row['start_date']);
		}
		
		// start end date
		$row['start_end_date'] = '';
		if (!empty($row['start_date']) && !empty($row['end_date'])) {
			$row['start_end_date'] =
				get_format_date($row['start_date'], array( 'date_format' => 'M j, Y'))
				. ' - ' .
				get_format_date($row['end_date'], array( 'date_format' => 'M j, Y'));
		} else if (!empty($row['start_date'])) {
			$row['start_end_date'] = get_format_date($row['start_date'], array( 'date_format' => 'M j, Y'));
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
}