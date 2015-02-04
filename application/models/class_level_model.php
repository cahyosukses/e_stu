<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class class_level_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'name', 'fiqh', 'akhlaq', 'taareekh', 'aqaid', 'no_order' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, CLASS_LEVEL);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, CLASS_LEVEL);
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
            $select_query  = "SELECT * FROM ".CLASS_LEVEL." WHERE id = '".$param['id']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		$param['option_all'] = (isset($param['option_all'])) ? $param['option_all'] : 0;
		
		if ($param['option_all'] == 1) {
			$array[] = array( 'id' => 'x', 'name' => 'All');
		}
		
		$string_fiqh = (isset($param['fiqh'])) ? "AND class_level.fiqh = '".$param['fiqh']."'" : '';
		$string_akhlaq = (isset($param['akhlaq'])) ? "AND class_level.akhlaq = '".$param['akhlaq']."'" : '';
		$string_taareekh = (isset($param['taareekh'])) ? "AND class_level.taareekh = '".$param['taareekh']."'" : '';
		$string_aqaid = (isset($param['aqaid'])) ? "AND class_level.aqaid = '".$param['aqaid']."'" : '';
		
		$string_namelike = (!empty($param['namelike'])) ? "AND name LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'no_order ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS class_level.*
			FROM ".CLASS_LEVEL." class_level
			WHERE 1
				$string_namelike $string_filter
				$string_fiqh $string_akhlaq $string_taareekh $string_aqaid
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
    }

    function get_teacher_array($param = array()) {
        $array = array();
		
		$string_user = (isset($param['user_id'])) ? "AND teacher_class.user_id = '".$param['user_id']."'" : '';
		$string_fiqh = (isset($param['fiqh'])) ? "AND class_type_id = '".CLASS_TYPE_FIQH."' AND class_level.fiqh = '".$param['fiqh']."'" : '';
		$string_akhlaq = (isset($param['akhlaq'])) ? "AND class_type_id = '".CLASS_TYPE_AKHLAG."' AND class_level.akhlaq = '".$param['akhlaq']."'" : '';
		$string_taareekh = (isset($param['taareekh'])) ? "AND class_type_id = '".CLASS_TYPE_TAREEKH."' AND class_level.taareekh = '".$param['taareekh']."'" : '';
		$string_aqaid = (isset($param['aqaid'])) ? "AND class_type_id = '".CLASS_TYPE_AQAID."' AND class_level.aqaid = '".$param['aqaid']."'" : '';
		
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'class_level.no_order ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS class_level.*
			FROM ".CLASS_LEVEL." class_level
			LEFT JOIN ".TEACHER_CLASS." teacher_class ON teacher_class.class_level_id = class_level.id
			WHERE 1 $string_user $string_fiqh $string_akhlaq $string_taareekh $string_aqaid $string_filter
			GROUP BY class_level.id
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
    }
	
	function get_array_recap($param = array()) {
        $result = array( 'array' => array(), 'count' => 0 );
		
		// param
		$string_status_final = '';
		if (isset($param['status_finalize'])) {
			if ($param['status_finalize'] == 'complete') {
				$string_status_final = "AND finalize_date IS NOT NULL";
			} else if ($param['status_finalize'] == 'uncomplete') {
				$string_status_final = "AND finalize_date IS NULL";
			}
		}
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_limit = GetStringLimit($param);
		
		// get record
		$select_query = "
			SELECT *
			FROM (
				SELECT '1' class_type_id, 'Quran' class_type_name, quran_level.id quran_level_id, '0' class_level_id, quran_level.name class_level_name, quran_level.no_order no_order, class_note.finalize_date,
					( SELECT GROUP_CONCAT(user_display SEPARATOR ', ') FROM teacher_class LEFT JOIN users ON users.user_id = teacher_class.user_id WHERE teacher_class.class_type_id = 1 AND teacher_class.quran_level_id = quran_level.id ) teacher_name
				FROM quran_level
				LEFT JOIN class_note ON class_note.quran_level_id = quran_level.id AND class_note.quran_level_id = 1
				UNION
				SELECT '2' class_type_id, 'Fiqh' class_type_name, '0' quran_level_id, class_level.id class_level_id, class_level.name class_level_name, class_level.no_order no_order, class_note.finalize_date,
					( SELECT GROUP_CONCAT(user_display SEPARATOR ', ') FROM teacher_class LEFT JOIN users ON users.user_id = teacher_class.user_id WHERE teacher_class.class_type_id = 2 AND teacher_class.class_level_id = class_level.id ) teacher_name
				FROM class_level
				LEFT JOIN class_note ON class_note.class_level_id = class_level.id AND class_note.class_type_id = 2
				WHERE class_level.fiqh = 1
				UNION
				SELECT '3' class_type_id, 'Akhlaq' class_type_name, '0' quran_level_id, class_level.id class_level_id, class_level.name class_level_name, class_level.no_order no_order, class_note.finalize_date,
					( SELECT GROUP_CONCAT(user_display SEPARATOR ', ') FROM teacher_class LEFT JOIN users ON users.user_id = teacher_class.user_id WHERE teacher_class.class_type_id = 3 AND teacher_class.class_level_id = class_level.id ) teacher_name
				FROM class_level
				LEFT JOIN class_note ON class_note.class_level_id = class_level.id AND class_note.class_type_id = 3
				WHERE class_level.akhlaq = 1
				UNION
				SELECT '4' class_type_id, 'Tareekh' class_type_name, '0' quran_level_id, class_level.id class_level_id, class_level.name class_level_name, class_level.no_order no_order, class_note.finalize_date,
					( SELECT GROUP_CONCAT(user_display SEPARATOR ', ') FROM teacher_class LEFT JOIN users ON users.user_id = teacher_class.user_id WHERE teacher_class.class_type_id = 4 AND teacher_class.class_level_id = class_level.id ) teacher_name
				FROM class_level
				LEFT JOIN class_note ON class_note.class_level_id = class_level.id AND class_note.class_type_id = 4
				WHERE class_level.taareekh = 1
				UNION
				SELECT '5' class_type_id, 'Aqaid' class_type_name, '0' quran_level_id, class_level.id class_level_id, class_level.name class_level_name, class_level.no_order no_order, class_note.finalize_date,
					( SELECT GROUP_CONCAT(user_display SEPARATOR ', ') FROM teacher_class LEFT JOIN users ON users.user_id = teacher_class.user_id WHERE teacher_class.class_type_id = 5 AND teacher_class.class_level_id = class_level.id ) teacher_name
				FROM class_level
				LEFT JOIN class_note ON class_note.class_level_id = class_level.id AND class_note.class_type_id = 5
				WHERE class_level.aqaid = 1
			) table_temp
			WHERE 1 $string_status_final $string_filter
			ORDER BY class_type_name ASC, no_order ASC
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$result['array'][] = $this->sync($row, $param);
		}
		
		// get count
		$select_query = "
			SELECT COUNT(*) total
			FROM (
				SELECT '1' class_type_id, 'Quran' class_type_name, quran_level.id class_level_id, quran_level.name class_level_name, quran_level.no_order no_order, class_note.finalize_date
					
				FROM quran_level
				LEFT JOIN class_note ON class_note.quran_level_id = quran_level.id AND class_note.quran_level_id = 1
				UNION
				SELECT '2' class_type_id, 'Fiqh' class_type_name, class_level.id class_level_id, class_level.name class_level_name, class_level.no_order no_order, class_note.finalize_date
				FROM class_level
				LEFT JOIN class_note ON class_note.class_level_id = class_level.id AND class_note.class_type_id = 2
				WHERE class_level.fiqh = 1
				UNION
				SELECT '3' class_type_id, 'Akhlaq' class_type_name, class_level.id class_level_id, class_level.name class_level_name, class_level.no_order no_order, class_note.finalize_date
				FROM class_level
				LEFT JOIN class_note ON class_note.class_level_id = class_level.id AND class_note.class_type_id = 3
				WHERE class_level.akhlaq = 1
				UNION
				SELECT '4' class_type_id, 'Tareekh' class_type_name, class_level.id class_level_id, class_level.name class_level_name, class_level.no_order no_order, class_note.finalize_date
				FROM class_level
				LEFT JOIN class_note ON class_note.class_level_id = class_level.id AND class_note.class_type_id = 4
				WHERE class_level.taareekh = 1
				UNION
				SELECT '5' class_type_id, 'Aqaid' class_type_name, class_level.id class_level_id, class_level.name class_level_name, class_level.no_order no_order, class_note.finalize_date
				FROM class_level
				LEFT JOIN class_note ON class_note.class_level_id = class_level.id AND class_note.class_type_id = 5
				WHERE class_level.aqaid = 1
			) table_temp
			WHERE 1 $string_status_final $string_filter
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$result['count'] = $row['total'];
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
	
    function delete($param) {
		$record_count = 0;
        $select_query = array();
        if (isset($param['id'])) {
            $select_query[] = "SELECT COUNT(*) total FROM ".ATTENDANCE." WHERE class_level_id = '".$param['id']."'";
            $select_query[] = "SELECT COUNT(*) total FROM ".STUDENT." WHERE class_level_id = '".$param['id']."'";
            $select_query[] = "SELECT COUNT(*) total FROM ".TASK." WHERE class_level_id = '".$param['id']."'";
            $select_query[] = "SELECT COUNT(*) total FROM ".TEACHER_CLASS." WHERE class_level_id = '".$param['id']."'";
        }
        foreach ($select_query as $query) {
            $select_result = mysql_query($query) or die(mysql_error());
            if (false !== $row = mysql_fetch_assoc($select_result)) {
                $record_count += $row['total'];
            }
        }
		if ($record_count > 0) {
            $result['status'] = '0';
            $result['message'] = 'Data already used.';
			return $result;
		}
		
		$delete_query  = "DELETE FROM ".CLASS_LEVEL." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row);
		
		// title
		if (isset($row['name'])) {
			$row['title'] = $row['name'];
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
}