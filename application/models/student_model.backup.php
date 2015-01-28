<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class student_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array(
			's_id', 's_parent_id', 's_status', 's_gender', 's_previous', 's_age', 's_last_level', 's_quran_level', 's_dob', 's_name',
			'quran_level_id', 'class_level_id'
		);
    }

    function update($param) {
        $result = array();
       
        if (empty($param['s_id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, STUDENT);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, STUDENT);
            $update_result = mysql_query($update_query) or die(mysql_error());
           
            $result['id'] = $param['s_id'];
            $result['status'] = '1';
            $result['message'] = 'Data successfully updated.';
        }
       
        return $result;
    }

    function get_by_id($param) {
        $array = array();
		
        if (isset($param['s_id'])) {
            $select_query  = "
				SELECT
					student.*,
					parent.p_father_email parent_email,
					parent.p_mother_name mother_name, parent.p_mother_email mother_email,
					class_level.name class_level_name, quran_level.name quran_level_name
				FROM ".STUDENT." student
				LEFT JOIN ".PARENT." parent ON student.s_parent_id = parent.p_id
				LEFT JOIN ".CLASS_LEVEL." class_level ON class_level.id = student.class_level_id
				LEFT JOIN ".QURAN_LEVEL." quran_level ON quran_level.id = student.quran_level_id
				WHERE
					student.s_id = '".$param['s_id']."'
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
		
		$param['field_replace']['father_name'] = 'parent.p_father_name';
		$param['field_replace']['father_cell'] = 'parent.p_father_cell';
		$param['field_replace']['mother_name'] = 'parent.p_mother_name';
		$param['field_replace']['quran_level_name'] = 'quran_level.name';
		$param['field_replace']['class_level_name'] = 'class_level.name';
		
		$string_parent = (isset($param['s_parent_id'])) ? "AND student.s_parent_id = '".$param['s_parent_id']."'" : '';
		$string_quran_level = (isset($param['quran_level_id'])) ? "AND student.quran_level_id = '".$param['quran_level_id']."'" : '';
		$string_class_level = (isset($param['class_level_id'])) ? "AND student.class_level_id = '".$param['class_level_id']."'" : '';
		$string_namelike = (!empty($param['namelike'])) ? "AND student.s_name LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column']);
		$string_sorting = GetStringSorting($param, @$param['column'], 's_name ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS student.*,
				quran_level.name quran_level_name, class_level.name class_level_name,
				parent.p_father_name father_name, parent.p_father_cell father_cell, parent.p_father_email parent_email,
				parent.p_mother_name mother_name, parent.p_mother_email mother_email
			FROM ".STUDENT." student
			LEFT JOIN ".PARENT." parent ON student.s_parent_id = parent.p_id
			LEFT JOIN ".QURAN_LEVEL." quran_level ON quran_level.id = student.quran_level_id
			LEFT JOIN ".CLASS_LEVEL." class_level ON class_level.id = student.class_level_id
			WHERE 1 $string_namelike $string_parent $string_quran_level $string_class_level $string_filter
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
	
    function get_grade($param = array()) {
		$string_student = (isset($param['student_id'])) ? "AND _student.s_id = '".$param['student_id']."'" : '';
		
		// get task grade
		$task_weight = $this->task_type_model->get_weight();
		
		// get student grade
		$select_query = "
			SELECT
				_student.s_id id, _student.s_name name, _student.quran_level_id, _student.class_level_id,
				
				-- quran select
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 1 AND quran_level_id = _student.quran_level_id AND task.task_type_id = ".TASK_TYPE_HOMEWORK." ) quran_homework,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 1 AND quran_level_id = _student.quran_level_id AND task.task_type_id = ".TASK_TYPE_PROJECT." ) quran_project,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 1 AND quran_level_id = _student.quran_level_id AND task.task_type_id = ".TASK_TYPE_TEST." ) quran_test,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 1 AND quran_level_id = _student.quran_level_id AND task.task_type_id = ".TASK_TYPE_QUIZ." ) quran_quiz,
				(	SELECT AVG(award) FROM attendance_student LEFT JOIN attendance ON attendance.id = attendance_student.attendance_id WHERE attendance_student.student_id = _student.s_id AND class_type_id = 1 AND quran_level_id = _student.quran_level_id ) quran_attendance,
				
				-- figh select
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 2 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_HOMEWORK." ) figh_homework,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 2 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_PROJECT." ) figh_project,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 2 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_TEST." ) figh_test,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 2 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_QUIZ." ) figh_quiz,
				(	SELECT AVG(award) FROM attendance_student LEFT JOIN attendance ON attendance.id = attendance_student.attendance_id WHERE attendance_student.student_id = _student.s_id AND class_type_id = 2 AND class_level_id = _student.class_level_id ) figh_attendance,
				
				-- akhlaq select
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 3 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_HOMEWORK." ) akhlaq_homework,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 3 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_PROJECT." ) akhlaq_project,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 3 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_TEST." ) akhlaq_test,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 3 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_QUIZ." ) akhlaq_quiz,
				(	SELECT AVG(award) FROM attendance_student LEFT JOIN attendance ON attendance.id = attendance_student.attendance_id WHERE attendance_student.student_id = _student.s_id AND class_type_id = 3 AND class_level_id = _student.class_level_id ) akhlaq_attendance,
				
				-- tareekh select
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 4 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_HOMEWORK." ) tareekh_homework,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 4 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_PROJECT." ) tareekh_project,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 4 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_TEST." ) tareekh_test,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 4 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_QUIZ." ) tareekh_quiz,
				(	SELECT AVG(award) FROM attendance_student LEFT JOIN attendance ON attendance.id = attendance_student.attendance_id WHERE attendance_student.student_id = _student.s_id AND class_type_id = 4 AND class_level_id = _student.class_level_id ) tareekh_attendance,
				
				-- aqaid select
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 5 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_HOMEWORK." ) aqaid_homework,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 5 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_PROJECT." ) aqaid_project,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 5 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_TEST." ) aqaid_test,
				(	SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 5 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_QUIZ." ) aqaid_quiz,
				(	SELECT AVG(award) FROM attendance_student LEFT JOIN attendance ON attendance.id = attendance_student.attendance_id WHERE attendance_student.student_id = _student.s_id AND class_type_id = 5 AND class_level_id = _student.class_level_id ) aqaid_attendance
			FROM ".STUDENT." _student
			WHERE
				1
				-- AND class_level_id != 0													-- => cause issue
				-- AND quran_level_id != 0												 	-- => cause issue
				$string_student
			LIMIT 250
		";
		
		$array_student = array();
		$select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array_student[] = $this->sync_grade($row, $task_weight);
		}
		
		// set 100 if no task avaliable
		if (count($array_student) == 0) {
			$temp['quran_label'] = 'No task avaliable';
			$temp['quran_summary'] = 100;
			$temp['figh_label'] = 'No task avaliable';
			$temp['figh_summary'] = 100;
			$temp['akhlaq_label'] = 'No task avaliable';
			$temp['akhlaq_summary'] = 100;
			$temp['tareekh_label'] = 'No task avaliable';
			$temp['tareekh_summary'] = 100;
			$temp['attendance_label'] = 'No task avaliable';
			$temp['attendance_summary'] = 100;
			$array_student[] = $temp;
		}
		
		return $array_student;
    }
	
	function get_class_average($param = array()) {
		// set default array
		$array_temp = array(
			'quran_homework' => array(),
			'quran_project' => array(),
			'quran_test' => array(),
			'quran_quiz' => array(),
			'quran_attendance' => array(),
			'figh_homework' => array(),
			'figh_project' => array(),
			'figh_test' => array(),
			'figh_quiz' => array(),
			'figh_attendance' => array(),
			'akhlaq_homework' => array(),
			'akhlaq_project' => array(),
			'akhlaq_test' => array(),
			'akhlaq_quiz' => array(),
			'akhlaq_attendance' => array(),
			'tareekh_homework' => array(),
			'tareekh_project' => array(),
			'tareekh_test' => array(),
			'tareekh_quiz' => array(),
			'tareekh_attendance' => array(),
			'aqaid_homework' => array(),
			'aqaid_project' => array(),
			'aqaid_test' => array(),
			'aqaid_quiz' => array(),
			'aqaid_attendance' => array()
		);
		
		// get task grade
		$task_weight = $this->task_type_model->get_weight();
		
		// set data to array
		$array_student = $this->student_model->get_grade();
		foreach ($array_student as $student_grade) {
			foreach ($array_temp as $key => $row) {
				if (isset($student_grade[$key])) {
					$array_temp[$key][] = $student_grade[$key];
				}
			}
		}
		
		// array data
		$array_data = array( 'class_level_id' => 20 );
		foreach ($array_temp as $key => $array) {
			$array_data[$key] = (count($array) == 0) ? 100 : array_sum($array) / count($array);
		}
		
		// fix and sync
		$array_data['quran_attendance'] = $array_data['quran_attendance'] / 100;
		$array_data['figh_attendance'] = $array_data['figh_attendance'] / 100;
		$array_data['akhlaq_attendance'] = $array_data['akhlaq_attendance'] / 100;
		$array_data['tareekh_attendance'] = $array_data['tareekh_attendance'] / 100;
		$array_data['aqaid_attendance'] = $array_data['aqaid_attendance'] / 100;
		$result = $this->sync_grade($array_data, $task_weight);
		
		return $result;
	}
	
	function get_teacher_average($param = array()) {
		// set default array
		$array_temp = array(
			'quran_homework' => array(),
			'quran_project' => array(),
			'quran_test' => array(),
			'quran_quiz' => array(),
			'quran_attendance' => array(),
			'figh_homework' => array(),
			'figh_project' => array(),
			'figh_test' => array(),
			'figh_quiz' => array(),
			'figh_attendance' => array(),
			'akhlaq_homework' => array(),
			'akhlaq_project' => array(),
			'akhlaq_test' => array(),
			'akhlaq_quiz' => array(),
			'akhlaq_attendance' => array(),
			'tareekh_homework' => array(),
			'tareekh_project' => array(),
			'tareekh_test' => array(),
			'tareekh_quiz' => array(),
			'tareekh_attendance' => array(),
			'aqaid_homework' => array(),
			'aqaid_project' => array(),
			'aqaid_test' => array(),
			'aqaid_quiz' => array(),
			'aqaid_attendance' => array()
		);
		
		// get task grade
		$task_weight = $this->task_type_model->get_weight();
		
		// get quran grade
		$select_query = "
			SELECT
				_student.s_name name, _student.quran_level_id, _student.class_level_id,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 1 AND quran_level_id = _student.quran_level_id AND task.task_type_id = ".TASK_TYPE_HOMEWORK.") quran_homework,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 1 AND quran_level_id = _student.quran_level_id AND task.task_type_id = ".TASK_TYPE_PROJECT.") quran_project,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 1 AND quran_level_id = _student.quran_level_id AND task.task_type_id = ".TASK_TYPE_TEST.") quran_test,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 1 AND quran_level_id = _student.quran_level_id AND task.task_type_id = ".TASK_TYPE_QUIZ.") quran_quiz,
				(SELECT AVG(award) FROM attendance_student LEFT JOIN attendance ON attendance.id = attendance_student.attendance_id WHERE attendance_student.student_id = _student.s_id AND class_type_id = 1 AND quran_level_id = _student.quran_level_id) quran_attendance
			FROM ".STUDENT." _student
			WHERE
				quran_level_id IN (SELECT quran_level_id FROM teacher_class WHERE user_id = '".$param['user_id']."' AND quran_level_id != 0)
			LIMIT 250
		";
		$select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			foreach ($row as $key => $value) {
				if (isset($array_temp[$key])) {
					if (is_null($value)) {
						if (in_array($key, array('quran_attendance'))) {
							$value = 1;
						} else {
							$value = 100;
						}
					}
					
					$array_temp[$key][] = $value;
				}
			}
		}
		
		// get class grade
		$select_query = "
			SELECT
				_student.s_name name, _student.quran_level_id, _student.class_level_id,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 2 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_HOMEWORK.") figh_homework,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 2 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_PROJECT.") figh_project,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 2 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_TEST.") figh_test,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 2 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_QUIZ.") figh_quiz,
				(SELECT AVG(award) FROM attendance_student LEFT JOIN attendance ON attendance.id = attendance_student.attendance_id WHERE attendance_student.student_id = _student.s_id AND class_type_id = 2 AND class_level_id = _student.class_level_id) figh_attendance,
				
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 3 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_HOMEWORK.") akhlaq_homework,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 3 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_PROJECT.") akhlaq_project,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 3 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_TEST.") akhlaq_test,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 3 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_QUIZ.") akhlaq_quiz,
				(SELECT AVG(award) FROM attendance_student LEFT JOIN attendance ON attendance.id = attendance_student.attendance_id WHERE attendance_student.student_id = _student.s_id AND class_type_id = 3 AND class_level_id = _student.class_level_id) akhlaq_attendance,
				
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 4 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_HOMEWORK.") tareekh_homework,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 4 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_PROJECT.") tareekh_project,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 4 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_TEST.") tareekh_test,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 4 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_QUIZ.") tareekh_quiz,
				(SELECT AVG(award) FROM attendance_student LEFT JOIN attendance ON attendance.id = attendance_student.attendance_id WHERE attendance_student.student_id = _student.s_id AND class_type_id = 4 AND class_level_id = _student.class_level_id) tareekh_attendance,
				
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 5 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_HOMEWORK.") aqaid_homework,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 5 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_PROJECT.") aqaid_project,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 5 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_TEST.") aqaid_test,
				(SELECT AVG(grade) FROM task_class LEFT JOIN task ON task.id = task_class.task_id WHERE student_id = _student.s_id AND class_type_id = 5 AND class_level_id = _student.class_level_id AND task.task_type_id = ".TASK_TYPE_QUIZ.") aqaid_quiz,
				(SELECT AVG(award) FROM attendance_student LEFT JOIN attendance ON attendance.id = attendance_student.attendance_id WHERE attendance_student.student_id = _student.s_id AND class_type_id = 5 AND class_level_id = _student.class_level_id) aqaid_attendance
			FROM ".STUDENT." _student
			WHERE
				class_level_id IN (SELECT class_level_id FROM teacher_class WHERE user_id = '".$param['user_id']."' AND class_level_id != 0)
			LIMIT 250
		";
		
		$select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			foreach ($row as $key => $value) {
				if (isset($array_temp[$key])) {
					if (is_null($value)) {
						if (in_array($key, array('figh_attendance', 'akhlaq_attendance', 'tareekh_attendance', 'aqaid_attendance'))) {
							$value = 1;
						} else {
							$value = 100;
						}
					}
					
					$array_temp[$key][] = $value;
				}
			}
		}
		
		// array data
		$array_data = array( 'class_level_id' => 20 );
		foreach ($array_temp as $key => $array) {
			// create default value
			if (in_array($key, array( 'quran_attendance' ))) {
				$default_value = 1;
			} else {
				$default_value = 100;
			}
			
			$array_data[$key] = (count($array) == 0) ? $default_value : array_sum($array) / count($array);
		}
		
		// sync
		$result = $this->sync_grade($array_data, $task_weight);
		
		return $result;
	}
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".STUDENT." WHERE s_id = '".$param['s_id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row);
		
		// quran title
		if (!empty($row['quran_level_name'])) {
			$row['quran_level_title'] = 'Quran '.$row['quran_level_name'];
		}
		
		// get 2 alphabest name
		$row['name_abbreviation'] = '';
		if (!empty($row['s_name'])) {
			$row['s_name'] = preg_replace('/ +/i', ' ', $row['s_name']);
			$array_name = explode(' ', $row['s_name'], 2);
			
			// first word
			$word[] = $array_name[0][0];
			
			// second word
			if (isset($array_name[1])) {
				$word[] = @$array_name[1][0];
			} else {
				$word[] = @$array_name[0][1];
			}
			
			$row['name_abbreviation'] = strtoupper(implode('', $word));
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
	
	function sync_grade($row, $task_weight) {
		$row['quran_homework'] = (is_null($row['quran_homework'])) ? 100 : intval($row['quran_homework']);
		$row['quran_project'] = (is_null($row['quran_project'])) ? 100 : intval($row['quran_project']);
		$row['quran_test'] = (is_null($row['quran_test'])) ? 100 : intval($row['quran_test']);
		$row['quran_quiz'] = (is_null($row['quran_quiz'])) ? 100 : intval($row['quran_quiz']);
		$row['quran_attendance'] = (is_null($row['quran_attendance'])) ? 100 : intval($row['quran_attendance'] * 100);
		$row['figh_homework'] = (is_null($row['figh_homework'])) ? 100 : intval($row['figh_homework']);
		$row['figh_project'] = (is_null($row['figh_project'])) ? 100 : intval($row['figh_project']);
		$row['figh_test'] = (is_null($row['figh_test'])) ? 100 : intval($row['figh_test']);
		$row['figh_quiz'] = (is_null($row['figh_quiz'])) ? 100 : intval($row['figh_quiz']);
		$row['figh_attendance'] = (is_null($row['figh_attendance'])) ? 100 : intval($row['figh_attendance'] * 100);
		$row['akhlaq_homework'] = (is_null($row['akhlaq_homework'])) ? 100 : intval($row['akhlaq_homework']);
		$row['akhlaq_project'] = (is_null($row['akhlaq_project'])) ? 100 : intval($row['akhlaq_project']);
		$row['akhlaq_test'] = (is_null($row['akhlaq_test'])) ? 100 : intval($row['akhlaq_test']);
		$row['akhlaq_quiz'] = (is_null($row['akhlaq_quiz'])) ? 100 : intval($row['akhlaq_quiz']);
		$row['akhlaq_attendance'] = (is_null($row['akhlaq_attendance'])) ? 100 : intval($row['akhlaq_attendance'] * 100);
		$row['tareekh_homework'] = (is_null($row['tareekh_homework'])) ? 100 : intval($row['tareekh_homework']);
		$row['tareekh_project'] = (is_null($row['tareekh_project'])) ? 100 : intval($row['tareekh_project']);
		$row['tareekh_test'] = (is_null($row['tareekh_test'])) ? 100 : intval($row['tareekh_test']);
		$row['tareekh_quiz'] = (is_null($row['tareekh_quiz'])) ? 100 : intval($row['tareekh_quiz']);
		$row['tareekh_attendance'] = (is_null($row['tareekh_attendance'])) ? 100 : intval($row['tareekh_attendance'] * 100);
		$row['aqaid_homework'] = (is_null($row['aqaid_homework'])) ? 100 : intval($row['aqaid_homework']);
		$row['aqaid_project'] = (is_null($row['aqaid_project'])) ? 100 : intval($row['aqaid_project']);
		$row['aqaid_test'] = (is_null($row['aqaid_test'])) ? 100 : intval($row['aqaid_test']);
		$row['aqaid_quiz'] = (is_null($row['aqaid_quiz'])) ? 100 : intval($row['aqaid_quiz']);
		$row['aqaid_attendance'] = (is_null($row['aqaid_attendance'])) ? 100 : intval($row['aqaid_attendance'] * 100);
		
		// get task weight spesific teacher
		$teacher_task_weight = $this->teacher_class_model->get_task_weight(array( 'student_id' => @$row['id'], 'task_weight' => $task_weight ));
		
		// summary & label
		$row['quran_summary']
			= ($row['quran_homework'] * $teacher_task_weight['quran']['homework'] * 0.01)
			+ ($row['quran_project'] * $teacher_task_weight['quran']['project'] * 0.01)
			+ ($row['quran_test'] * $teacher_task_weight['quran']['test'] * 0.01)
			+ ($row['quran_quiz'] * $teacher_task_weight['quran']['quiz'] * 0.01)
			+ ($row['quran_attendance'] * $teacher_task_weight['quran']['attendance'] * 0.01);
		$row['quran_label']
			= "Homework - ".$row['quran_homework']."%\n"
			. " Project - ".$row['quran_project']."%\n"
			. " Test - ".$row['quran_test']."%\n"
			. " Quiz - ".$row['quran_quiz']."%\n"
			. " Attendance - ".$row['quran_attendance']."%";
		$row['figh_summary']
			= ($row['figh_homework'] * $teacher_task_weight['fiqh']['homework'] * 0.01)
			+ ($row['figh_project'] * $teacher_task_weight['fiqh']['project'] * 0.01)
			+ ($row['figh_test'] * $teacher_task_weight['fiqh']['test'] * 0.01)
			+ ($row['figh_quiz'] * $teacher_task_weight['fiqh']['quiz'] * 0.01)
			+ ($row['figh_attendance'] * $teacher_task_weight['fiqh']['attendance'] * 0.01);
		$row['figh_label']
			= "Homework - ".$row['figh_homework']."%\n"
			. " Project - ".$row['figh_project']."%\n"
			. " Test - ".$row['figh_test']."%\n"
			. " Quiz - ".$row['figh_quiz']."%\n"
			. " Attendance - ".$row['figh_attendance']."%\n";
		$row['akhlaq_summary']
			= ($row['akhlaq_homework'] * $teacher_task_weight['akhlaq']['homework'] * 0.01)
			+ ($row['akhlaq_project'] * $teacher_task_weight['akhlaq']['project'] * 0.01)
			+ ($row['akhlaq_test'] * $teacher_task_weight['akhlaq']['test'] * 0.01)
			+ ($row['akhlaq_quiz'] * $teacher_task_weight['akhlaq']['quiz'] * 0.01)
			+ ($row['akhlaq_attendance'] * $teacher_task_weight['akhlaq']['attendance'] * 0.01);
		$row['akhlaq_label']
			= "Homework - ".$row['akhlaq_homework']."%\n"
			. " Project - ".$row['akhlaq_project']."%\n"
			. " Test - ".$row['akhlaq_test']."%\n"
			. " Quiz - ".$row['akhlaq_quiz']."%\n"
			. " Attendance - ".$row['akhlaq_attendance']."%";
		$row['tareekh_summary']
			= ($row['tareekh_homework'] * $teacher_task_weight['tareekh']['homework'] * 0.01)
			+ ($row['tareekh_project'] * $teacher_task_weight['tareekh']['project'] * 0.01)
			+ ($row['tareekh_test'] * $teacher_task_weight['tareekh']['test'] * 0.01)
			+ ($row['tareekh_quiz'] * $teacher_task_weight['tareekh']['quiz'] * 0.01)
			+ ($row['tareekh_attendance'] * $teacher_task_weight['tareekh']['attendance'] * 0.01);
		$row['tareekh_label']
			= "Homework - ".$row['tareekh_homework']."%\n"
			. " Project - ".$row['tareekh_project']."%\n"
			. " Test - ".$row['tareekh_test']."%\n"
			. " Quiz - ".$row['tareekh_quiz']."%\n"
			. " Attendance - ".$row['tareekh_attendance']."%";
		
		// aqaid only level 5 or greater
		if ($row['class_level_id'] >= 5) {
			$row['aqaid_summary']
				= ($row['aqaid_homework'] * $teacher_task_weight['aqaid']['homework'] * 0.01)
				+ ($row['aqaid_project'] * $teacher_task_weight['aqaid']['project'] * 0.01)
				+ ($row['aqaid_test'] * $teacher_task_weight['aqaid']['test'] * 0.01)
				+ ($row['aqaid_quiz'] * $teacher_task_weight['aqaid']['quiz'] * 0.01)
				+ ($row['aqaid_attendance'] * $teacher_task_weight['aqaid']['attendance'] * 0.01);
			$row['aqaid_label']
				= "Homework - ".$row['aqaid_homework']."%\n"
				. " Project - ".$row['aqaid_project']."%\n"
				. " Test - ".$row['aqaid_test']."%\n"
				. " Quiz - ".$row['aqaid_quiz']."%\n"
				. " Attendance - ".$row['aqaid_attendance']."%";
		} else {
			unset($row['aqaid_homework']);
			unset($row['aqaid_project']);
			unset($row['aqaid_test']);
			unset($row['aqaid_quiz']);
			unset($row['aqaid_attendance']);
		}
		
		// get attendance summary
		$array_attendance = array();
		$array_attendance[] = $row['quran_attendance'];
		$array_attendance[] = $row['figh_attendance'];
		$array_attendance[] = $row['akhlaq_attendance'];
		$array_attendance[] = $row['tareekh_attendance'];
		if ($row['class_level_id'] >= 5) {
			$array_attendance[] = $row['aqaid_attendance'];
		}
		$row['attendance_summary'] = array_sum($array_attendance) / count($array_attendance);
		$row['attendance_label']
			= 'Quran '.$row['quran_attendance']."%\n"
			. 'Figh '.$row['figh_attendance']."%\n"
			. 'Akhlaq '.$row['akhlaq_attendance']."%\n"
			. 'Tareekh '.$row['tareekh_attendance']."%";
		if ($row['class_level_id'] >= 5) {
			$row['attendance_label'] .= "\nAqaid ".$row['aqaid_attendance'].'%';
		}
		
		return $row;
	}
}