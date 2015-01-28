<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class task_class_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'task_id', 'student_id', 'grade' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, TASK_CLASS);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, TASK_CLASS);
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
            $select_query  = "SELECT * FROM ".TASK_CLASS." WHERE id = '".$param['id']."' LIMIT 1";
        } else if (isset($param['task_id']) && isset($param['student_id'])) {
            $select_query  = "
				SELECT *
				FROM ".TASK_CLASS."
				WHERE
					task_id = '".$param['task_id']."'
					AND student_id = '".$param['student_id']."'
				LIMIT 1
			";
        } else if (isset($param['task_title']) && isset($param['student_id'])) {
            $select_query  = "
				SELECT task_class.*
				FROM ".TASK_CLASS." task_class
				LEFT JOIN ".TASK." task ON task.id = task_class.task_id
				WHERE
					task.title = '".$param['task_title']."'
					AND task_class.student_id = '".$param['student_id']."'
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
		
		$param['field_replace']['task_title'] = 'task.title';
		$param['field_replace']['task_due_date'] = 'task.due_date';
		$param['field_replace']['task_type_name'] = 'task_type.name';
		$param['field_replace']['class_type_name'] = 'class_type.name';
		$param['field_replace']['task_due_date_swap'] = 'task.due_date';
		
		$string_task = (isset($param['task_id'])) ? "AND task_class.task_id = '".$param['task_id']."'" : '';
		$string_task_type = (isset($param['task_type_id'])) ? "AND task.task_type_id = '".$param['task_type_id']."'" : '';
		$string_student = (isset($param['student_id'])) ? "AND task_class.student_id = '".$param['student_id']."'" : '';
		$string_namelike = (!empty($param['namelike'])) ? "AND student.s_name LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'student.s_name ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS task_class.*,
				task.title task_title, task.content task_content, task.due_date task_due_date,
				student.s_name, user.user_display, user.teacher_subject, task_type.name task_type_name, class_type.name class_type_name
			FROM ".TASK_CLASS." task_class
			LEFT JOIN ".TASK." task ON task.id = task_class.task_id
			LEFT JOIN ".TASK_TYPE." task_type ON task_type.id = task.task_type_id
			LEFT JOIN ".USER." user ON user.user_id = task.assign_by
			LEFT JOIN ".STUDENT." student ON student.s_id = task_class.student_id
			LEFT JOIN ".CLASS_TYPE." class_type ON class_type.id = task.class_type_id
			WHERE 1 $string_task $string_task_type $string_student $string_namelike $string_filter
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
		$delete_query  = "DELETE FROM ".TASK_CLASS." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row);
		
		// label alert
		$row['label_alert'] = '';
		if (isset($row['task_due_date'])) {
			$row['label_alert'] = $this->get_label_alert($row);
			$row['task_due_date_swap'] = ExchangeFormatDate($row['task_due_date']);
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
	
	function get_label_alert($param = array()) {
		$unix_current_time = ConvertToUnixTime($this->config->item('current_datetime'));
		$unix_due_time = ConvertToUnixTime($param['task_due_date']);
		$unix_diff = $unix_due_time - $unix_current_time;
		
		if (!empty($param['grade'])) {
			$result = '<span class="label label-success">Completed</span>';
		} else if ($unix_diff > 1 && $unix_diff < (1 * 24 * 60 * 60)) {
			$result = '<span class="label label-info">Tomorrow</span>';
		} else if ($unix_diff > 1 && $unix_diff < (7 * 24 * 60 * 60)) {
			$result = '<span class="label label-info">This Week</span>';
		} else if ($unix_diff > 1) {
			$result = '<span class="label label-warning">Due Next Week</span>';
		} else {
			$result = '<span class="label label-important">Overdue</span>';
		}
		
		return $result;
	}
}