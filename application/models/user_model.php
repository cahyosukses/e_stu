<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class user_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array(
			'user_id', 'user_type_id', 'user_uname', 'user_pword', 'user_status', 'user_level', 'user_since',
			'user_lastlogin', 'user_logins', 'user_email', 'teacher_subject', 'user_display', 'phone', 'json_meta'
		);
    }

    function update($param) {
        $result = array();
       
        if (empty($param['user_id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, USER);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, USER);
            $update_result = mysql_query($update_query) or die(mysql_error());
           
            $result['id'] = $param['user_id'];
            $result['status'] = '1';
            $result['message'] = 'Data successfully updated.';
        }
       
        return $result;
    }
	
	function update_meta($param) {
		$row = $this->get_by_id(array( 'user_id' => $param['user_id'] ));
		
		// selector
		if (empty($row['json_meta'])) {
			$param_update['user_id'] = $param['user_id'];
			$param_update['json_meta'] = json_encode($param['json_meta']);
			$result = $this->update($param_update);
		} else {
			$json_meta = object_to_array(json_decode($row['json_meta']));
			
			// override old data
			foreach ($param['json_meta'] as $key => $value) {
				$json_meta[$key] = $value;
			}
			
			// update
			$param_update['user_id'] = $param['user_id'];
			$param_update['json_meta'] = json_encode($json_meta);
			$result = $this->update($param_update);
		}
		
		return $result;
	}
	
    function get_by_id($param) {
        $array = array();
		
        if (isset($param['user_id'])) {
            $select_query  = "SELECT * FROM ".USER." WHERE user_id = '".$param['user_id']."' LIMIT 1";
        } else if (isset($param['user_uname'])) {
            $select_query  = "SELECT user.* FROM ".USER." user WHERE user.user_uname = '".$param['user_uname']."' LIMIT 1";
        } else if (isset($param['user_email'])) {
            $select_query  = "SELECT user.* FROM ".USER." user WHERE user.user_email = '".$param['user_email']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
		$array = array();
		$param['with_subject'] = (isset($param['with_subject'])) ? $param['with_subject'] : 0;
		
		$param['field_replace']['user_type_title'] = 'user_type.title';
		
		$string_namelike = (!empty($param['namelike'])) ? "AND user.email LIKE '%".$param['namelike']."%'" : '';
		$string_user_type = (!empty($param['user_type_id'])) ? "AND user.user_type_id = '".$param['user_type_id']."'" : '';
		$string_user_id_in = (isset($param['user_id_in'])) ? "AND user.user_id IN (".$param['user_id_in'].")" : '';
		$string_user_type_in = (isset($param['user_type_id_in'])) ? "AND user.user_type_id IN (".$param['user_type_id_in'].")" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'user_display ASC');
		$string_limit = GetStringLimit($param);
		
		// teacher class
		$string_teacher_class = '';
		if (isset($param['class_level_id']) || isset($param['quran_level_id'])) {
			$string_teacher_class = '';
			
			// quran
			if (!empty($param['quran_level_id'])) {
				$string_teacher_class = "
					user.user_id IN (
						SELECT DISTINCT (user_id)
						FROM ".TEACHER_CLASS."
						WHERE quran_level_id = '".$param['quran_level_id']."'
					)
				";
			}
			
			// class
			if (!empty($param['class_level_id'])) {
				$string_teacher_class .= (empty($string_teacher_class)) ? "" : "OR ";
				$string_teacher_class .= "
					user.user_id IN (
						SELECT DISTINCT (user_id)
						FROM ".TEACHER_CLASS."
						WHERE class_level_id = '".$param['class_level_id']."'
					)
				";
			}
			
			// prefix
			if (!empty($string_teacher_class)) {
				$string_teacher_class = "AND ($string_teacher_class)";
			}
		}
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS user.*, user_type.title user_type_title
			FROM ".USER." user
			LEFT JOIN ".USER_TYPE." user_type ON user_type.id = user.user_type_id
			WHERE 1 $string_namelike $string_user_type $string_user_id_in $string_user_type_in $string_teacher_class $string_filter
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			if ($param['with_subject']) {
				$subject_param = array(
					'user_id' => $row['user_id'],
					'user_display' => $row['user_display'],
					'class_level_id' => $param['class_level_id'],
					'quran_level_id' => $param['quran_level_id']
				);
				$subject = $this->teacher_class_model->get_subject($subject_param);
				$row['user_display'] = $subject;
			}
			
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
		$delete_query  = "DELETE FROM ".USER." WHERE user_id = '".$param['user_id']."' LIMIT 1";
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
	
	/*	Region Session */
	
	function is_login($param = array()) {
		$user = $this->get_session();
		$result = (count($user) > 0 && @$user['is_login']) ? true : false;
		
		if ($result && !empty($param['user_type_id'])) {
			if ($user['user_type_id'] != $param['user_type_id']) {
				$result = false;
			}
		}
		
		return $result;
	}
	
	// required_login(array( 'user_type_id' => 1 ))
	function required_login($param = array()) {
		$is_login = $this->is_login($param);
		if (!$is_login) {
			header("Location: ".base_url());
			exit;
		}
	}
	
	function set_session($user) {
		$user['is_login'] = true;
		
		// set session
		$_SESSION['user_login'] = $user;
		
		// set cookie
		$cookie_value = mcrypt_encode(json_encode($user));
		setcookie("user_login", $cookie_value, time() + (60 * 60 * 5), '/');
	}
	
	function get_session() {
		$user = (isset($_SESSION['user_login'])) ? $_SESSION['user_login'] : array();
		if (! is_array($user)) {
			$user = array();
		}
		
		// check from cookie
		if (count($user) == 0) {
			$user = $this->get_cookies();
		}
		
		// renew session if user already login
		if (count($user) > 0 && isset($user['is_login']) && $user['is_login']) {
			// set session
			$_SESSION['user_login'] = $user;
			
			// set cookie
			$cookie_value = mcrypt_encode(json_encode($user));
			setcookie("user_login", $cookie_value, time() + (60 * 60 * 5), '/');
		}
		
		return $user;
	}
	
	function get_cookies() {
		$user = array( 'is_login' => false );
		if (isset($_COOKIE["user_login"])) {
			$user = json_decode(mcrypt_decode($_COOKIE["user_login"]));
			$user = object_to_array($user);
			$user['is_login'] = true;
		}
		
		return $user;
	}
	
	function del_session() {
		// delete session
		if (isset($_SESSION['user_login'])) {
			unset($_SESSION['user_login']);
		}
		
		// delete cookie
		setcookie("user_login", '', time() + 0, '/');
	}
	
	/*	End Region Session */
}