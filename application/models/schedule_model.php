<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class schedule_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'user_id', 'parent_id', 'time_frame' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, SCHEDULE);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, SCHEDULE);
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
            $select_query  = "SELECT * FROM ".SCHEDULE." WHERE id = '".$param['id']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$param['field_replace']['father_name'] = 'parents.p_father_name';
		$param['field_replace']['mother_name'] = 'parents.p_mother_name';
		$param['field_replace']['student_name'] = '';
		$param['field_replace']['time_frame_title'] = 'schedule.time_frame';
		
		$string_user = (!empty($param['user_id'])) ? "AND schedule.user_id = '".$param['user_id']."'" : '';
		$string_parent = (isset($param['parent_id'])) ? "AND schedule.parent_id = '".$param['parent_id']."'" : '';
		$string_parent_not_in = (isset($param['parent_not_in'])) ? "AND schedule.parent_id NOT IN (".$param['parent_not_in'].")" : '';
		$string_time_frame = (isset($param['time_frame'])) ? "AND schedule.time_frame = '".$param['time_frame']."'" : '';
		$string_time_frame_min = (isset($param['time_frame_min'])) ? "AND schedule.time_frame >= '".$param['time_frame_min']."'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'time_frame DESC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS schedule.*, user.user_display, parents.p_father_name father_name, parents.p_mother_name mother_name
			FROM ".SCHEDULE." schedule
			LEFT JOIN ".USER." user ON schedule.user_id = user.user_id
			LEFT JOIN ".PARENTS." parents ON schedule.parent_id = parents.p_id
			WHERE 1 $string_user $string_parent $string_parent_not_in $string_time_frame $string_time_frame_min $string_filter
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			// get student name
			$array_temp = array();
			$student_name = '';
			$array_student = $this->student_model->get_by_teacher_parent(array( 'user_id' => $row['user_id'], 'parent_id' => $row['parent_id'] ));
			foreach ($array_student as $student) {
				$array_temp[$student['student_name']][] = $student['class_type_name'];
			}
			foreach ($array_temp as $key => $class_type) {
				$string_temp = $key.' ('.implode(', ', $class_type).')';
				$student_name .= (empty($student_name)) ? $string_temp : ', '.$string_temp;
			}
			$row['student_name'] = $student_name;
			
			// sync
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
    }

	function get_teacher_without_schedule($param = array()) {
		$result = array( 'array_teacher' => array(), 'datatable' => array() );
		$array_teacher = $this->teacher_class_model->get_teacher_by_parent(array( 'parent_id' => $param['parent_id'] ));
		$array_teacher_schedule = $this->schedule_model->get_array(array( 'parent_id' => $param['parent_id'] ));
		
		// remove teacher with schedule
		$array_teacher_id = array();
		foreach ($array_teacher as $teacher) {
			if (empty($teacher['user_display'])) {
				continue;
			}
			
			$schedule_exist = false;
			$count_schedule_left = $this->schedule_model->get_count(array( 'query_type' => 'schedule_left', 'user_id' => $teacher['user_id'] ));
			foreach ($array_teacher_schedule as $schedule) {
				if ($schedule['user_id'] == $teacher['user_id']) {
					$schedule_exist = true;
					break;
				}
			}
			
			// mark unavilable when teacher do not have schedule left
			if ($count_schedule_left == 0) {
				$teacher['user_display'] .= ' - No Available Time Slots';
			}
			
			if (!$schedule_exist) {
				$array_teacher_id[] = $teacher['user_id'];
				$result['array_teacher'][$teacher['user_id']] = $teacher;
			}
		}
		
		// get result
		if (count($array_teacher_id) > 0) {
			$param_result = $param;
			$param_result['user_id_in'] = implode(',', $array_teacher_id);
			$result['datatable'] = $this->user_model->get_array($param_result);
		}
		
		// override datatable
		foreach ($result['datatable'] as $key => $row) {
			preg_match('/\"user_id\":\"(\d+)\"/i', $row[count($row) - 1], $match);
			$user_id = (isset($match[1])) ? $match[1] : 0;
			
			if (isset($result['array_teacher'][$user_id]) && count($result['array_teacher'][$user_id]) > 0) {
				$result['datatable'][$key][0] = $result['array_teacher'][$user_id]['user_display'];
				$result['datatable'][$key][1] = $result['array_teacher'][$user_id]['student_name'];
			}
		}
		
		return $result;
	}
	
    function get_count($param = array()) {
		$param['query_type'] = (isset($param['query_type'])) ? $param['query_type'] : '';
		
		if ($param['query_type'] == 'schedule_left') {
			$select_query = "SELECT COUNT(*) total FROM ".SCHEDULE." WHERE user_id = '".$param['user_id']."' AND parent_id = '0'";
		} else {
			$param['field_replace']['father_name'] = 'parents.p_father_name';
			$param['field_replace']['mother_name'] = 'parents.p_mother_name';
			$param['field_replace']['student_name'] = '';
			$param['field_replace']['time_frame_title'] = 'schedule.time_frame';
			
			$string_user = (!empty($param['user_id'])) ? "AND schedule.user_id = '".$param['user_id']."'" : '';
			$string_parent = (isset($param['parent_id'])) ? "AND schedule.parent_id = '".$param['parent_id']."'" : '';
			$string_parent_not_in = (isset($param['parent_not_in'])) ? "AND schedule.parent_id NOT IN (".$param['parent_not_in'].")" : '';
			$string_time_frame = (isset($param['time_frame'])) ? "AND schedule.time_frame = '".$param['time_frame']."'" : '';
			$string_time_frame_min = (isset($param['time_frame_min'])) ? "AND schedule.time_frame >= '".$param['time_frame_min']."'" : '';
			$string_filter = GetStringFilter($param, @$param['column']);
			
			$select_query = "
				SELECT COUNT(*) total
				FROM ".SCHEDULE." schedule
				LEFT JOIN ".USER." user ON schedule.user_id = user.user_id
				LEFT JOIN ".PARENTS." parents ON schedule.parent_id = parents.p_id
				WHERE 1 $string_user $string_parent $string_parent_not_in $string_time_frame $string_time_frame_min $string_filter
			";
		}
		
		$select_result = mysql_query($select_query) or die(mysql_error());
		$row = mysql_fetch_assoc($select_result);
		$total = $row['total'];
		
		return $total;
    }
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".SCHEDULE." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( 'create_date' ));
		
		if (!empty($row['time_frame'])) {
			$row['time_frame_title'] = ExchangeFormatDate($row['time_frame']);
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
}