<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$autoload['packages'] = array();
$autoload['libraries'] = array('database', 'session');
$autoload['helper'] = array( 'date', 'common', 'url', 'mcrypt', 'email', 'signature' );
$autoload['config'] = array();
$autoload['language'] = array();
$autoload['model'] = array(
	'user_model', 'student_model', 'mail_model', 'quran_level_model', 'class_level_model', 'teacher_class_model', 'calendar_model',
	'task_type_model', 'task_model', 'task_class_model', 'attendance_model', 'attendance_student_model', 'parents_model',
	'user_type_model', 'class_type_model', 'fee_model', 'sms_model', 'config_model', 'weekly_checklist_model', 'handbook_model',
	'attendance_absence_model', 'cron_log_model', 'tardy_model', 'teacher_comment_model', 'class_note_model', 'schedule_model'
);
