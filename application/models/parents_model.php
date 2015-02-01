<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class parents_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array(
			'p_id', 'p_status', 'p_father_name', 'p_father_email', 'p_father_cell', 'p_mother_name', 'p_mother_email',
			'p_mother_cell', 'p_phone', 'p_address', 'p_apt', 'p_city', 'p_state', 'p_zip', 'p_signature', 'p_passwd', 'p_pdf2014', 'p_sign_image'
		);
    }

    function update($param) {
        $result = array();
       
        if (empty($param['p_id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, PARENTS);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, PARENTS);
            $update_result = mysql_query($update_query) or die(mysql_error());
           
            $result['id'] = $param['p_id'];
            $result['status'] = '1';
            $result['message'] = 'Data successfully updated.';
        }
       
        return $result;
    }

    function get_by_id($param) {
        $array = array();
		
        if (isset($param['p_id'])) {
            $select_query  = "SELECT * FROM ".PARENTS." WHERE p_id = '".$param['p_id']."' LIMIT 1";
        } else if (isset($param['p_father_email'])) {
            $select_query  = "
				SELECT *
				FROM ".PARENTS."
				WHERE
					p_father_email = '".$param['p_father_email']."'
					OR p_mother_email = '".$param['p_father_email']."'
				LIMIT 1";
        }
		
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$string_namelike = (!empty($param['namelike'])) ? "AND parents.p_father_name LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'parents.p_father_name ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS parents.*
			FROM ".PARENTS." parents
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

    function get_array_child($param = array()) {
        $array = array();
		
		$string_namelike = (!empty($param['namelike'])) ? "AND parents.p_father_name LIKE '%".$param['namelike']."%'" : '';
		$string_parent = (isset($param['parent_id'])) ? "AND parents.p_id = '".$param['parent_id']."'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'parents.p_id ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT parents.p_id parent_id, p_father_name father_name, p_mother_name mother_name, COUNT(*) AS student_count
			FROM ".PARENTS." parents
			LEFT JOIN ".STUDENT." student ON student.s_parent_id = parents.p_id
			WHERE 1 $string_namelike $string_parent $string_filter
			GROUP BY parents.p_id
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
    }
	
	function get_array_autocomplete($param = array()) {
        $array = array();
		
		$string_sorting = GetStringSorting($param, @$param['column'], 'parents.p_father_name ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			(	SELECT p_id, p_father_name p_father_name, p_father_email
				FROM ".PARENTS."
				WHERE p_father_name LIKE '%".$param['namelike']."%'
			) UNION (
				SELECT p_id, p_mother_name p_father_name, p_father_email
				FROM ".PARENTS." parents
				WHERE p_mother_name LIKE '%".$param['namelike']."%'
			)
			ORDER BY p_father_name ASC
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
		$delete_query  = "DELETE FROM ".PARENTS." WHERE p_id = '".$param['p_id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( ));
		
		// sync with user table
		if (isset($row['p_id'])) {
			$row['user_id'] = $row['p_id'];
		}
		if (isset($row['p_father_name'])) {
			$row['user_display'] = $row['p_father_name'];
		}
		$row['user_type_id'] = USER_TYPE_PARENT;
		if (isset($row['p_passwd'])) {
			$row['user_pword'] = $row['p_passwd'];
		}
		if (isset($row['p_father_email'])) {
			$row['user_email'] = $row['p_father_email'];
		}
		
		// link
		if (!empty($row['p_sign_image'])) {
			$row['p_sign_image_link'] = base_url('static/upload/'.$row['p_sign_image']);
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
}