<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class task_type_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'name', 'weight' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, TASK_TYPE);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, TASK_TYPE);
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
            $select_query  = "SELECT * FROM ".TASK_TYPE." WHERE id = '".$param['id']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$string_id_in = (isset($param['id_in'])) ? "AND task_type.id IN (".$param['id_in'].")" : '';
		$string_namelike = (!empty($param['namelike'])) ? "AND name LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'name ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS task_type.*
			FROM ".TASK_TYPE." task_type
			WHERE 1 $string_namelike $string_id_in $string_filter
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
	
    function get_weight($param = array()) {
		$update = false;
		$array_weight = array( 'homework' => 0, 'project' => 0, 'test' => 0, 'quiz' => 0, 'attendance' => 0 );
		
		// user
		$user = $this->user_model->get_session();
		
		// teacher
		if ($user['user_type_id'] == USER_TYPE_TEACHER) {
			$teacher = $this->user_model->get_by_id(array( 'user_id' => $user['user_id'] ));
			
			// set task weight
			if (!empty($teacher['json_meta'])) {
				$json_data = object_to_array(json_decode($teacher['json_meta']));
				if (isset($json_data['array_task_type'])) {
					$update = true;
					foreach ($json_data['array_task_type'] as $key => $row) {
						$array_weight[$row['alias']] = $row['weight'];
					}
				}
			}
		}
		
		// get default task weight
		if (!$update) {
			$select_query = "
				SELECT
					( SELECT task_type.weight FROM ".TASK_TYPE." task_type WHERE id = ".TASK_TYPE_HOMEWORK." ) homework,
					( SELECT task_type.weight FROM ".TASK_TYPE." task_type WHERE id = ".TASK_TYPE_PROJECT." ) project,
					( SELECT task_type.weight FROM ".TASK_TYPE." task_type WHERE id = ".TASK_TYPE_TEST." ) test,
					( SELECT task_type.weight FROM ".TASK_TYPE." task_type WHERE id = ".TASK_TYPE_QUIZ." ) quiz,
					( SELECT task_type.weight FROM ".TASK_TYPE." task_type WHERE id = ".TASK_TYPE_ATTENDANCE." ) attendance
			";
			$select_result = mysql_query($select_query) or die(mysql_error());
			while ( $row = mysql_fetch_assoc( $select_result ) ) {
				$array_weight = $row;
			}
		}
		
		return $array_weight;
    }
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".TASK_TYPE." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( ));
		
		// alias
		if (isset($row['name'])) {
			$row['alias'] = get_name($row['name']);
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
}