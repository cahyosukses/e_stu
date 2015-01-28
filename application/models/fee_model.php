<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class fee_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'parent_id', 'discount', 'subtotal', 'is_paid', 'invoice_no', 'invoice_date', 'json_meta', 'payment_method', 'payment_no' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, FEE);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, FEE);
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
            $select_query  = "
				SELECT fee.*,
					parents.p_father_name, parents.p_father_email, parents.p_mother_name, parents.p_mother_email, parents.p_address
				FROM ".FEE." fee
				LEFT JOIN ".PARENT." parents ON parents.p_id = fee.parent_id
				WHERE fee.id = '".$param['id']."'
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
		
		$param['field_replace']['total'] = '';
		
		$string_namelike = (!empty($param['namelike'])) ? "AND parents.p_father_name LIKE '%".$param['namelike']."%'" : '';
		$string_paid = (isset($param['is_paid'])) ? "AND fee.is_paid = '".$param['is_paid']."'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'start_date ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS fee.*, parents.p_father_name, parents.p_mother_name
			FROM ".FEE." fee
			LEFT JOIN ".PARENT." parents ON parents.p_id = fee.parent_id
			WHERE 1 $string_namelike $string_paid $string_filter
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
	
	function get_invoice_no() {
		$result = '0000001';
		$select_query = "SELECT invoice_no FROM ".FEE." fee ORDER BY invoice_no DESC LIMIT 1";
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$invoice_no = intval(trim($row['invoice_no']));
			$result = (empty($invoice_no)) ? $result : str_pad($invoice_no + 1, 7, "0", STR_PAD_LEFT);
		}
		
        return $result;
	}
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".FEE." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( 'start_date', 'end_date' ));
		
		// meta
		if (!empty($row['json_meta'])) {
			$array = object_to_array(json_decode($row['json_meta']));
			$row = array_merge($row, $array);
		}
		
		// calculate total
		if (isset($row['discount']) && isset($row['subtotal'])) {
			$row['total'] = $row['subtotal'] - $row['discount'];
		}
		
		// link
		if (!empty($row['invoice_no'])) {
			$row['link_nvoice'] = base_url('static/temp/'.$row['invoice_no'].'.pdf');
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
}