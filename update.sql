2015-06-04 :
- CREATE TABLE IF NOT EXISTS `register` ( `id` int(11) NOT NULL AUTO_INCREMENT, `student_id` int(11) NOT NULL, `is_paid` int(11) NOT NULL, `due_date` datetime NOT NULL, `status` varchar(50) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
- ALTER TABLE `register` ADD `handbook` VARCHAR( 50 ) NOT NULL AFTER `is_paid` ;
- INSERT INTO `config` ( `config_id` , `config_key` , `config_value` , `config_desc` , `config_group` , `is_hidden` ) VALUES ( '17', 'register-notification-email', 'Dear Parents, We have a new Register 2015 - 2016 School Year. Please visit your dashboard at http://jafariaschool.org/student</a> to submit your registration ', 'Register Notification Email', '0', '0' );
