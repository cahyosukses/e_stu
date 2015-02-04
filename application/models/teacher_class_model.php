<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class teacher_class_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'user_id', 'class_level_id', 'quran_level_id', 'class_type_id' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, TEACHER_CLASS);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, TEACHER_CLASS);
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
            $select_query  = "SELECT * FROM ".TEACHER_CLASS." WHERE id = '".$param['id']."' LIMIT 1";
        } else if (isset($param['user_id']) && isset($param['class_type_id']) && isset($param['quran_level_id']) && isset($param['class_level_id'])) {
            $select_query  = "
				SELECT * FROM ".TEACHER_CLASS."
				WHERE
					user_id = '".$param['user_id']."'
					AND class_type_id = '".$param['class_type_id']."'
					AND quran_level_id = '".$param['quran_level_id']."'
					AND class_level_id = '".$param['class_level_id']."'
				LIMIT 1
			";
        } 
		
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$return_list_id = (isset($param['return_list_id'])) ? $param['return_list_id'] : false;
		$string_user = (isset($param['user_id'])) ? "AND teacher_class.user_id = '".$param['user_id']."'" : '';
		$string_class_type = (isset($param['class_type_id'])) ? "AND teacher_class.class_type_id = '".$param['class_type_id']."'" : '';
		$string_namelike = (!empty($param['namelike'])) ? "AND name LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'user.teacher_subject ASC');
		$string_limit = GetStringLimit($param);
		
		#region class filter
		$string_quran_level = '';
		if (isset($param['quran_level_id'])) {
			if ($param['quran_level_id'] == 'x') {
				// select all
			} else {
				$string_quran_level = "AND teacher_class.quran_level_id = '".$param['quran_level_id']."'";
			}
		}
		
		$string_class_level = '';
		if (isset($param['class_level_id'])) {
			if ($param['class_level_id'] == 'x') {
				// select all
			} else {
				$string_class_level = "AND teacher_class.class_level_id = '".$param['class_level_id']."'";
			}
		}
		#endregion class filter
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS teacher_class.*,
				user.user_type_id, user.teacher_subject, user.user_display, user.user_email,
				class_level.name class_level_name, quran_level.name quran_level_name
			FROM ".TEACHER_CLASS." teacher_class
			LEFT JOIN ".USER." user ON user.user_id = teacher_class.user_id
			LEFT JOIN ".CLASS_LEVEL." class_level ON class_level.id = teacher_class.class_level_id
			LEFT JOIN ".QURAN_LEVEL." quran_level ON quran_level.id = teacher_class.quran_level_id
			WHERE 1 $string_namelike $string_user $string_class_type $string_quran_level $string_class_level $string_filter
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
		// set return format
		if ($return_list_id) {
			$result = array();
			foreach ($array as $key => $row) {
				$result[] = $row['class_type_id'];
			}
		} else {
			$result = $array;
		}
		
        return $result;
    }
	
    function get_class_teacher($param = array()) {
        $array = array();
		
		$return_list_id = (isset($param['return_list_id'])) ? $param['return_list_id'] : false;
		$string_user = (isset($param['user_id'])) ? "AND teacher_class.user_id = '".$param['user_id']."'" : '';
		
		$select_query = "
			SELECT class_type.id, class_type.name, class_type.name title
			FROM ".TEACHER_CLASS." teacher_class
			LEFT JOIN ".CLASS_TYPE." class_type ON class_type.id = teacher_class.class_type_id
			WHERE 1 $string_user
			GROUP BY class_type.id, class_type.name
			ORDER BY class_type.id
			LIMIT 25
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
		// set return format
		if ($return_list_id) {
			$result = array();
			foreach ($array as $key => $row) {
				$result[] = $row['id'];
			}
		} else {
			$result = $array;
		}
		
        return $result;
    }

    function get_count($param = array()) {
		$select_query = "SELECT FOUND_ROWS() total";
		$select_result = mysql_query($select_query) or die(mysql_error());
		$row = mysql_fetch_assoc($select_result);
		$total = $row['total'];
		
		return $total;
    }
	
	function get_task_weight($param = array()) {
		$result = array();
		
		// if empty student id
		if (empty($param['student_id'])) {
			$result['quran'] = $param['task_weight'];
			$result['fiqh'] = $param['task_weight'];
			$result['akhlaq'] = $param['task_weight'];
			$result['tareekh'] = $param['task_weight'];
			$result['aqaid'] = $param['task_weight'];
			return $result;
		}
		
        $select_query  = "
			SELECT
				-- quran
				(	SELECT users.json_meta
					FROM teacher_class
					LEFT JOIN users ON users.user_id = teacher_class.user_id
					WHERE
						class_type_id = 1
						AND quran_level_id = (SELECT quran_level_id FROM students WHERE s_id = ".$param['student_id'].")
					LIMIT 1
				) quran,
				
				-- fiqh
				(	SELECT users.json_meta
					FROM teacher_class
					LEFT JOIN users ON users.user_id = teacher_class.user_id
					WHERE
						class_type_id = 2
						AND class_level_id = (SELECT class_level_id FROM students WHERE s_id = ".$param['student_id'].")
					LIMIT 1
				) fiqh,
				
				-- akhlaq
				(	SELECT users.json_meta
					FROM teacher_class
					LEFT JOIN users ON users.user_id = teacher_class.user_id
					WHERE
						class_type_id = 3
						AND class_level_id = (SELECT class_level_id FROM students WHERE s_id = ".$param['student_id'].")
					LIMIT 1
				) akhlaq,
				
				-- tareekh
				(	SELECT users.json_meta
					FROM teacher_class
					LEFT JOIN users ON users.user_id = teacher_class.user_id
					WHERE
						class_type_id = 4
						AND class_level_id = (SELECT class_level_id FROM students WHERE s_id = ".$param['student_id'].")
					LIMIT 1
				) tareekh,
				
				-- aqaid
				(	SELECT users.json_meta
					FROM teacher_class
					LEFT JOIN users ON users.user_id = teacher_class.user_id
					WHERE
						class_type_id = 5
						AND class_level_id = (SELECT class_level_id FROM students WHERE s_id = ".$param['student_id'].")
					LIMIT 1
				) aqaid
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
			foreach ($row as $key => $json) {
				// reset variable
				$json_data = array();
				if (!empty($json)) {
					$json_data = object_to_array(json_decode($json));
				}
				
				// update task weight
				if (isset($json_data['array_task_type'])) {
					foreach ($json_data['array_task_type'] as $task) {
						$result[$key][$task['alias']] = $task['weight'];
					}
				} else {
					$result[$key] = $param['task_weight'];
				}
			}
        }
		
        return $result;
	}
	
	function get_subject($param = array()) {
		$result = '';
		
		// quran level
		$select_query = "
			SELECT class_type.name
			FROM ".TEACHER_CLASS." teacher_class
			LEFT JOIN ".CLASS_TYPE." class_type ON class_type.id = teacher_class.class_type_id
			WHERE
				user_id = '".$param['user_id']."'
				AND quran_level_id = '".$param['quran_level_id']."'
			ORDER BY class_type.name ASC
		";
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$result .= (empty($result)) ? $row['name'] : ', '.$row['name'];
		}
		
		// class level
		$select_query = "
			SELECT class_type.name
			FROM ".TEACHER_CLASS." teacher_class
			LEFT JOIN ".CLASS_TYPE." class_type ON class_type.id = teacher_class.class_type_id
			WHERE
				user_id = '".$param['user_id']."'
				AND class_level_id = '".$param['class_level_id']."'
			ORDER BY class_type.name ASC
		";
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$result .= (empty($result)) ? $row['name'] : ', '.$row['name'];
		}
		
		// append user display
		$result = (empty($result)) ? $param['user_display'] : $result.' Teacher - '.$param['user_display'];
		
		return $result;
	}
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".TEACHER_CLASS." WHERE id = '".$param['id']."' LIMIT 1";
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