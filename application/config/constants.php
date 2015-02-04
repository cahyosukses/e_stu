<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*   WEBSITE   */
define('SITE_TITLE',							'Jafaria School');
define('SITE_DOMAIN',							'jafariaschool.org');

/*   MAIL   */
if ($_SERVER['SERVER_NAME'] == 'localhost') {
	define('SENT_MAIL',							0);
} else {
	define('SENT_MAIL',							1);
}

/*   USER TYPE   */
define('USER_TYPE_ADMINISTRATOR',				1);
define('USER_TYPE_PRINCIPAL',					2);
define('USER_TYPE_TEACHER',						3);
define('USER_TYPE_PARENT',						4);

/*   TASK TYPE   */
define('CLASS_TYPE_QURAN',						1);
define('CLASS_TYPE_FIQH',						2);
define('CLASS_TYPE_AKHLAG',						3);
define('CLASS_TYPE_TAREEKH',					4);
define('CLASS_TYPE_AQAID',						5);

/*   TASK TYPE   */
define('TASK_TYPE_HOMEWORK',					1);
define('TASK_TYPE_PROJECT',						2);
define('TASK_TYPE_TEST',						3);
define('TASK_TYPE_QUIZ',						4);
define('TASK_TYPE_ATTENDANCE',					5);

/*   TABLE   */
define('ATTENDANCE',							'attendance');
define('ATTENDANCE_ABSENCE',					'attendance_absence');
define('ATTENDANCE_STUDENT',					'attendance_student');
define('CALENDAR',								'calendar');
define('CLASS_LEVEL',							'class_level');
define('CLASS_NOTE',							'class_note');
define('CLASS_TYPE',							'class_type');
define('CONFIG',								'config');
define('CRON_LOG',								'cron_log');
define('FEE',									'fee');
define('HANDBOOK',								'handbook');
define('MAIL',									'mail');
define('PARENT',								'parents');
define('PARENTS',								'parents');
define('QURAN_LEVEL',							'quran_level');
define('SMS',									'sms');
define('STUDENT',								'students');
define('TASK',									'task');
define('TASK_TYPE',								'task_type');
define('TASK_CLASS',							'task_class');
define('TARDY',									'tardy');
define('TEACHER_CLASS',							'teacher_class');
define('TEACHER_COMMENT',						'teacher_comment');
define('USER',									'users');
define('USER_TYPE',								'user_type');
define('WEEKLY_CHECKLIST',						'weekly_checklist');
