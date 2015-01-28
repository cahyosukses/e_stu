<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class config_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'config_id', 'config_key', 'config_value', 'config_desc', 'config_group', 'is_hidden' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['config_id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, CONFIG);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, CONFIG);
            $update_result = mysql_query($update_query) or die(mysql_error());
           
            $result['id'] = $param['config_id'];
            $result['status'] = '1';
            $result['message'] = 'Data successfully updated.';
        }
       
        return $result;
    }
	
	function update_by_key($param = array()) {
		$row = $this->get_by_id(array( 'config_key' => $param['config_key'] ));
		$param_update['config_id'] = $row['config_id'];
		$param_update['config_value'] = $param['config_value'];
		$result = $this->update($param_update);
		
		return $result;
	}

    function get_array($param = array()) {
        $array = array();
		
		$string_namelike = (!empty($param['namelike'])) ? "AND config.config_desc LIKE '%".$param['namelike']."%'" : '';
		$string_hidden = (isset($param['is_hidden'])) ? "AND config.is_hidden = '".$param['is_hidden']."'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'config_desc ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS config.*
			FROM ".CONFIG." config
			WHERE 1 $string_namelike $string_hidden $string_filter
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
	
    function get_by_id($param) {
        $array = array();
		
        if (isset($param['id'])) {
            $select_query  = "SELECT * FROM ".CONFIG." WHERE id = '".$param['id']."' LIMIT 1";
        } else if (isset($param['config_key'])) {
            $select_query  = "SELECT * FROM ".CONFIG." WHERE config_key = '".$param['config_key']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
	function get_row($param) {
		$record = $this->get_by_id(array( 'config_key' => $param['config_key'] ));
		if (count($record) == 0) {
			return array();
		}
		
		// decode data
		$value = object_to_array(json_decode($record['config_value']));
		
		return $value;
	}
	
	/*
    function get_array($param = array()) {
        $array = array();
		
		$param['field_replace']['due_date_title'] = 'due_date';
		
		$string_user = (!empty($param['user_id'])) ? "AND user_id = '".$param['user_id']."'" : '';
		$string_namelike = (!empty($param['namelike'])) ? "AND message LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'create_date DESC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS *
			FROM ".CONFIG."
			WHERE 1 $string_namelike $string_user $string_filter
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
	/*	*/
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".CONFIG." WHERE id = '".$param['id']."' LIMIT 1";
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