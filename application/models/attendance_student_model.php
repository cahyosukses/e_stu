<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class attendance_student_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'attendance_id', 'student_id', 'award', 'update_time' );
    }
	
    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, ATTENDANCE_STUDENT);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, ATTENDANCE_STUDENT);
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
            $select_query  = "SELECT * FROM ".ATTENDANCE_STUDENT." WHERE id = '".$param['id']."' LIMIT 1";
        } else if (isset($param['student_id']) && isset($param['due_date'])) {
            $select_query  = "
				SELECT attendance_student.*
				FROM ".ATTENDANCE_STUDENT." attendance_student
				LEFT JOIN ".ATTENDANCE." attendance ON attendance.id = attendance_student.attendance_id
				WHERE
					attendance.due_date = '".$param['due_date']."'
					AND attendance_student.student_id = '".$param['student_id']."'
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
		
		$param['field_replace']['class_type_name'] = 'class_type.name';
		$param['field_replace']['due_date_swap'] = 'attendance.due_date';
		$param['field_replace']['attendance_title'] = 'attendance.title';
		$param['field_replace']['award_title'] = 'attendance_student.award';
		
		$string_namelike = (!empty($param['namelike'])) ? "AND student.s_name LIKE '%".$param['namelike']."%'" : '';
		$string_student = (isset($param['student_id'])) ? "AND attendance_student.student_id = '".$param['student_id']."'" : '';
		$string_attendance = (isset($param['attendance_id'])) ? "AND attendance_student.attendance_id = '".$param['attendance_id']."'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'student.s_name DESC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS attendance_student.*,
				attendance.due_date, attendance.title attendance_title, student.s_name, class_type.name class_type_name
			FROM ".ATTENDANCE_STUDENT." attendance_student
			LEFT JOIN ".ATTENDANCE." attendance ON attendance.id = attendance_student.attendance_id
			LEFT JOIN ".STUDENT." student ON student.s_id = attendance_student.student_id
			LEFT JOIN ".CLASS_TYPE." class_type ON class_type.id = attendance.class_type_id
			WHERE 1 $string_namelike $string_student $string_attendance $string_filter
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
	
	function get_array_notification($param = array()) {
        $array = array();
		
		$param['field_replace']['total_absence'] = '';
		$param['field_replace']['student_name'] = 'student.s_name';
		$param['field_replace']['notification'] = 'attendance_absence.reason';
		
		$string_student = (isset($param['due_date'])) ? "AND attendance.due_date = '".$param['due_date']."'" : '';
		$string_current_date = (isset($param['current_date'])) ? "AND DATE(attendance.due_date) < DATE('".$param['current_date']."')" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'student.s_name ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS student.s_id student_id, student.s_name student_name,
				attendance_absence.id attendance_absence_id, attendance_absence.reason notification, attendance_absence.content,
				parent.p_father_name father_name, parent.p_father_email father_email,
				parent.p_mother_name mother_name, parent.p_mother_email mother_email,
				(	SELECT COUNT(*)
					FROM ".ATTENDANCE_STUDENT." attendance_student_count
					WHERE
						attendance_student_count.award = 0
						AND attendance_student_count.student_id = student.s_id
				) total_absence
			FROM ".ATTENDANCE_STUDENT." attendance_student
			LEFT JOIN ".ATTENDANCE." attendance ON attendance.id = attendance_student.attendance_id
			LEFT JOIN ".STUDENT." student ON student.s_id = attendance_student.student_id
			LEFT JOIN ".PARENT." parent ON student.s_parent_id = parent.p_id
			LEFT JOIN ".ATTENDANCE_ABSENCE." attendance_absence
				ON attendance_absence.student_id = student.s_id AND attendance_absence.absence_date = attendance.due_date
			WHERE
				attendance_student.award = 0
				AND student.s_name IS NOT NULL
				$string_student $string_current_date $string_filter
			GROUP BY student.s_id, student.s_name
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
	}
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".ATTENDANCE_STUDENT." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( ));
		
		// due date swap
		if (isset($row['due_date'])) {
			$row['due_date_swap'] = ExchangeFormatDate($row['due_date']);
		}
		
		// award title
		if (isset($row['award'])) {
			$row['award_title'] = ($row['award'] == 0) ? 'Absent' : 'Present';
		}
		
		if (count(@$param['column']) > 0) {
			if (isset($param['grid_type']) && $param['grid_type'] == 'attendance_tracker') {
				$param['is_custom'] = '<span class="cursor-font-awesome icon-ok btn-excuse" title="Excuse"></span>';
				if (!empty($row['notification'])) {
					$param['is_custom'] .= '<span class="cursor-font-awesome icon-envelope btn-message" title="View Message"></span>';
				} else {
					$row['notification'] = '-';
				}
			}
			
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
	
	function sent_notification($param = array()) {
		$param['force_sent'] = (isset($param['force_sent'])) ? $param['force_sent'] : false;
		
		#region cron log
		
		/*
		// check log
		$param_cron = array( 'alias' => 'attendance-tracker', 'limit' => 1 );
		$array_cron = $this->cron_log_model->get_array($param_cron);
		$cron = (count($array_cron) == 1) ? $array_cron[0] : array();
		if (!empty($cron['log_time']) && !$param['force_sent']) {
			$cron_time = ConvertToUnixTime($cron['log_time']);
			$current_time = ConvertToUnixTime($this->config->item('current_datetime'));
			
			// offset time
			$offset_time = 5 * 24 * 60 * 60;
			$diff_time = $current_time - $cron_time;
			if ($diff_time < $offset_time) {
				$result = array( 'status' => false, 'message' => 'Time is below offset time.' );
				return $result;
			}
		}
		/*	*/
		
		// write log
		$param_update = array(
			'alias' => 'attendance-tracker',
			'log_time' => $this->config->item('current_datetime')
		);
		$this->cron_log_model->update($param_update);
		
		#endregion cron log
		
		#region collect data
		
		// get absence date
		$param_attendance = array( 'limit' => 1 );
		$array_attendance = $this->attendance_model->get_array($param_attendance);
		$latest_attendance = $array_attendance[0];
		
		// collect all parent
		$param_absence = array(
			'due_date' => (!empty($param['due_date'])) ? $param['due_date'] : $latest_attendance['due_date'],
			'current_date' => $this->config->item('current_date'),
			'limit' => 500
		);
		$array_absence = $this->attendance_student_model->get_array_notification($param_absence);
		
		#endregion collect data
		
		#region email
		
		// variable
		$array_to = $array_duplicate = $array_sub['-student_name-'] = array();
		
		// set student with one parent
		$array_temp = $array_absence;
		$array_absence = array();
		foreach ($array_temp as $row) {
			if (!empty($row['notification'])) {
				continue;
			}
			
			if (!in_array($row['father_name'], $array_duplicate)) {
				$array_absence[] = $row;
				$array_duplicate[] = $row['father_name'];
			} else {
				foreach ($array_absence as $key => $student) {
					if ($student['father_name'] == $row['father_name']) {
						$array_absence[$key]['student_name'] .= ', '.$row['student_name'];
					}
				}
			}
		}
		
		// email target
		foreach ($array_absence as $row) {
			if (!empty($row['father_email'])) {
				$array_duplicate[] = $row['father_email'];
				$array_to[] = array(
					'name' => $row['father_name'],
					'email' => $row['father_email']
				);
				$array_sub['-student_name-'][] = $row['student_name'];
			}
			if (!empty($row['mother_email'])) {
				$array_duplicate[] = $row['mother_email'];
				$array_to[] = array(
					'name' => $row['mother_name'],
					'email' => $row['mother_email']
				);
				$array_sub['-student_name-'][] = $row['student_name'];
			}
		}
		
		// email data
		$subject = 'Attendance Tracker Notification';
		$email_content = $this->config_model->get_by_id(array( 'config_key' => 'attendance-tracker' ));
		
		// sent mail
		$param_mail = array(
			'category' => array( 'title' => $subject ),
			'user_email' => 'school@jafaria.org',
			'user_display' => 'Nausheen Mithani',
			'array_to' => $array_to,
			'array_sub' => $array_sub,
			'subject' => $subject,
			'content' => $email_content['config_value'],
			'full_body_message' => true
		);
		$this->mail_model->sent_grid($param_mail);
		
		#endregion email
		
		// result
		$result = array( 'status' => true, 'message' => count($array_to).' email sent.' );
		return $result;
	}
	
	function get_recap_student($param = array()) {
		$result = array( 'is_attend' => 0, 'not_attend' => 0 );
		
		$select_query = "
			SELECT attendance_student.award, COUNT(*) total
			FROM ".ATTENDANCE_STUDENT." attendance_student
			LEFT JOIN ".ATTENDANCE." attendance ON attendance.id = attendance_student.attendance_id
			LEFT JOIN ".STUDENT." student ON student.s_id = attendance_student.student_id
			WHERE
				student.s_name IS NOT NULL
				AND attendance.due_date = '".$param['due_date']."'
			GROUP BY attendance_student.award
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			if (empty($row['award'])) {
				$result['not_attend'] = $row['total'];
			} else {
				$result['is_attend'] = $row['total'];
			}
		}
		
		$result['message'] = $result['is_attend'].' students attend and '.$result['not_attend'].' students do not attend.';
		return $result;
	}
}