<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class task_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'task_type_id', 'class_type_id', 'quran_level_id', 'class_level_id', 'assign_by', 'title', 'content', 'due_date', 'is_complete', 'attachment' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, TASK);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, TASK);
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
				SELECT task.*,
					task_type.name task_type_name, user.user_display, user.teacher_subject
				FROM ".TASK." task
				LEFT JOIN ".USER." user ON user.user_id = task.assign_by
				LEFT JOIN ".TASK_TYPE." task_type ON task_type.id = task.task_type_id
				WHERE task.id = '".$param['id']."'
				LIMIT 1
			";
		} else if (isset($param['title'])) {
            $select_query  = "
				SELECT task.*,
					task_type.name task_type_name, user.user_display, user.teacher_subject
				FROM ".TASK." task
				LEFT JOIN ".USER." user ON user.user_id = task.assign_by
				LEFT JOIN ".TASK_TYPE." task_type ON task_type.id = task.task_type_id
				WHERE task.title = '".$param['title']."'
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
		
		$param['field_replace']['due_date_swap'] = 'task.due_date';
		$param['field_replace']['complete_title'] = '';
		$param['field_replace']['task_type_name'] = 'task_type.name';
		
		$string_assign = (isset($param['assign_by'])) ? "AND task.assign_by = '".$param['assign_by']."'" : '';
		$string_task_type = (isset($param['task_type_id'])) ? "AND task.task_type_id = '".$param['task_type_id']."'" : '';
		$string_class_type = (isset($param['class_type_id'])) ? "AND task.class_type_id = '".$param['class_type_id']."'" : '';
		$string_quran_level = (isset($param['quran_level_id'])) ? "AND task.quran_level_id = '".$param['quran_level_id']."'" : '';
		$string_class_level = (isset($param['class_level_id'])) ? "AND task.class_level_id = '".$param['class_level_id']."'" : '';
		$string_namelike = (!empty($param['namelike'])) ? "AND title LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 'due_date DESC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS task.*,
				task_type.name task_type_name, user.user_display, user.teacher_subject,
				class_type.name class_type_name
			FROM ".TASK." task
			LEFT JOIN ".USER." user ON user.user_id = task.assign_by
			LEFT JOIN ".TASK_TYPE." task_type ON task_type.id = task.task_type_id
			LEFT JOIN ".CLASS_TYPE." class_type ON class_type.id = task.class_type_id
			WHERE 1 $string_assign $string_task_type $string_class_type $string_quran_level $string_class_level $string_namelike $string_filter
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
	
    function get_array_document($param = array()) {
		$array = array();
		
		// student
		$student = $this->student_model->get_by_id(array( 's_id' => $param['student_id'] ));
		
		// task
		$select_query = "
			SELECT task.*
			FROM ".TASK." task
			WHERE
				(task.class_level_id = '".$student['class_level_id']."' AND '".$student['class_level_id']."' != 0)
				OR (task.quran_level_id = '".$student['quran_level_id']."' AND '".$student['quran_level_id']."' != 0)
			LIMIT 1000
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			if (!empty($row['attachment'])) {
				$array_attachment = object_to_array(json_decode($row['attachment']));
				foreach ($array_attachment as $key => $file) {
					$file['task_title'] = $row['title'];
					$file['file_link'] = base_url('static/upload/'.$file['file_name']);
					$array[] = $file;
				}
			}
		}
		
		// check config finalize
		$finalize = $this->config_model->get_by_id(array( 'config_key' => 'report-card-finalize' ));
		if (!empty($finalize['config_value'])) {
			// add report card
			$select_query = "
				SELECT parents.report_card
				FROM ".STUDENT." student
				LEFT JOIN ".PARENTS." parents ON student.s_parent_id = parents.p_id
				WHERE student.s_id = '".$param['student_id']."'
			";
			
			$select_result = mysql_query($select_query) or die(mysql_error());
			while ( $row = mysql_fetch_assoc( $select_result ) ) {
				if (!empty($row['report_card'])) {
					$report_card = array(
						'file_name' => $row['report_card'],
						'task_title' => 'Report Card',
						'file_only' => preg_replace('/\d+\//i', '', $row['report_card']),
						'file_link' => base_url('static/temp/'.$row['report_card'])
					);
					$array = array_merge($array, array( $report_card ));
				}
			}
		}
		
        return $array;
    }

    function delete($param) {
		$param['truncate'] = (isset($param['truncate'])) ? $param['truncate'] : false;
		
		if ($param['truncate']) {
			$delete_query  = "TRUNCATE TABLE ".TASK_CLASS;
			$delete_result = mysql_query($delete_query) or die(mysql_error());
			
			$delete_query  = "TRUNCATE TABLE ".TASK;
			$delete_result = mysql_query($delete_query) or die(mysql_error());
		} else {
			$delete_query  = "DELETE FROM ".TASK_CLASS." WHERE task_id = '".$param['id']."'";
			$delete_result = mysql_query($delete_query) or die(mysql_error());
			
			$delete_query  = "DELETE FROM ".TASK." WHERE id = '".$param['id']."' LIMIT 1";
			$delete_result = mysql_query($delete_query) or die(mysql_error());
		}
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row, array( ));
		
		// add more alias
		if (isset($row['due_date'])) {
			$row['task_due_date'] = $row['due_date'];
		}
		if (isset($row['title'])) {
			$row['task_title'] = $row['title'];
		}
		if (isset($row['content'])) {
			$row['task_content'] = $row['content'];
		}
		if (isset($row['is_complete'])) {
			$row['complete_title'] = ($row['is_complete'] == 1) ? 'Yes' : 'No';
		}
		
		// label alert
		$row['label_alert'] = '';
		if (isset($row['task_due_date'])) {
			$row['label_alert'] = $this->task_class_model->get_label_alert($row);
			$row['due_date_swap'] = ExchangeFormatDate($row['due_date']);
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
	
	function set_complete($param = array()) {
		$update_query  = "
			UPDATE ".TASK."
			SET is_complete = '1'
			WHERE
				task_type_id = '".$param['task_type_id']."'
				AND class_type_id = '".$param['class_type_id']."'
				AND due_date <= '".$param['due_date']."'
				AND title LIKE '%".$param['title']."%'
		";
        $update_result = mysql_query($update_query) or die(mysql_error());
	}
	
	function generate_quran_task() {
		// mark as complete old task
		$param_update = array(
			'task_type_id' => TASK_TYPE_HOMEWORK,
			'class_type_id' => CLASS_TYPE_QURAN,
			'due_date' => $this->config->item('current_date'),
			'title' => 'Weekly Quran Checklist Week'
		);
		$this->task_model->set_complete($param_update);
		
		// only generate until end juli 2015
		$limit_time = ConvertToUnixTime(date("2015-08-01"));
		$current_time = ConvertToUnixTime($this->config->item('current_datetime'));
		if ($limit_time <= $current_time) {
			return;
		}
		
		// generate task title
		for ($i = 0; $i <= 10; $i++) {
			$date_counter = date("Y-m-d l", strtotime("-$i days"));
			list($date_sunday, $date_name) = explode(' ', $date_counter);
			if ($date_name == 'Sunday') {
				break;
			}
		}
		$task_title = 'Weekly Quran Checklist Week of '.get_format_date($date_sunday, array( 'date_format' => 'm/d/y' ));
		
		// get task from existing record
		$record = $this->get_by_id(array( 'title' => $task_title ));
		if (count($record) > 0) {
			return;
		}
		
		// array quran class for task
		$array_quran_level = $this->quran_level_model->get_array(array( 'id_in' => '3, 4, 5' ));
		
		// user administrator
		$array_user = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_ADMINISTRATOR, 'limit' => 1 ));
		$user_admin = $array_user[0];
		
		// create task for existing class
		foreach ($array_quran_level as $key => $quran_level) {
			// get student in quran class
			$param_student = array(
				'quran_level_id' => $quran_level['id'],
				'limit' => 500
			);
			$array_student = $this->student_model->get_array($param_student);
			
			// create task
			$param_task = array(
				'task_type_id' => TASK_TYPE_HOMEWORK,
				'class_type_id' => CLASS_TYPE_QURAN,
				'quran_level_id' => $quran_level['id'],
				'assign_by' => $user_admin['user_id'],
				'title' => $task_title,
				'content' => '-',
				'due_date' => add_date($date_sunday, '1 weeks'),
				'is_complete' => 1
			);
			$result_task = $this->task_model->update($param_task);
			$task = $this->task_model->get_by_id(array( 'id' => $result_task['id'] ));
			
			// create task for student
			foreach ($array_student as $row) {
				$param_task_class = array(
					'task_id' => $task['id'],
					'student_id' => $row['s_id']
				);
				$this->task_class_model->update($param_task_class);
			}
		}
	}
}