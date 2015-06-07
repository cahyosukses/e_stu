2015-06-07 :
CREATE TABLE IF NOT EXISTS `register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `is_paid` int(11) NOT NULL,
  `handbook` varchar(50) NOT NULL,
  `due_date` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

INSERT INTO `config` (`config_id`, `config_key`, `config_value`, `config_desc`, `config_group`, `is_hidden`) VALUES
(NULL, 'register-notification-email', 'Dear Parents, \nSalam Alaikum\n\nWe have opened Registration for the 2015-2016 School year and giving our current students an opportunity to re-register their child.\n\nIf you wish to re-register, please do so before 23rd of Ramadhan, which is when we will be opening up registration for all new students. \n\nPlease visit your dashboard at http://jafariaschool.org/student</a> to register your child.\n\n', 'Register Notification Email', 0, 0);

INSERT INTO `config` (`config_id`, `config_key`, `config_value`, `config_desc`, `config_group`, `is_hidden`) VALUES
(NULL, 'register-success-email', 'Salam,\r\n\r\nYou have successfully registered -name_of_student- for the 2015-2016 school year at Jafaria education center. Please take a minute to complete a survey <a href="http://www.jafariaschool.org/survey.html">here</a> to tell us about last year.\r\n\r\nAs always please don''t hesitate to let us know if you have any questions, comments, or concerns.\r\n\r\nPlease keep us in your duas in the Holy Months of Shabaan and Ramadhan.\r\n\r\nIltemas-e-Dua\r\n\r\nReagards,\r\nJafaria School System', 'Register Success Email', 0, 0);