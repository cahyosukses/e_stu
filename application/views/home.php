<?php
	// get user
	$user = $this->user_model->get_session();
	$class_level = $this->class_level_model->get_array();
	$quran_level = $this->quran_level_model->get_array();
	
	// get teacher class type
	$user_class_type = array( 1, 2, 3, 4, 5 );
	if ($user['user_type_id'] == USER_TYPE_TEACHER) {
		$user_class_type = $this->teacher_class_model->get_class_teacher(array( 'user_id' => $user['user_id'], 'return_list_id' => true ));
	}
	
	// master
	$tareekh = $this->class_type_model->get_by_id(array( 'id' => CLASS_TYPE_TAREEKH ));
	
	// get student from uri or session
	$student = array();
	$array_uri = $this->uri->segments;
	$student_id = (!empty($array_uri[count($array_uri)])) ? $array_uri[count($array_uri)] : 0;
	$student_id = intval($student_id);
	if (!empty($student_id)) {
		$student = $this->student_model->get_by_id(array( 's_id' => $student_id ));
	} else if ($user['user_type_id'] == USER_TYPE_PARENT) {
		$student = $this->student_model->get_by_id(array( 's_id' => $user['student_id'] ));
		$array_handbook = $this->handbook_model->get_array(array( 'parent_id' => $user['p_id'] ));
	}
	
	// get grade
	$student_grade = array();
	if (count($student) > 0) {
		$array_student = $this->student_model->get_grade(array( 'student_id' => $student['s_id'] ));
		$student_grade = @$array_student[0];
	} else if ($user['user_type_id'] == USER_TYPE_TEACHER) {
		$param_grade['user_id'] = $user['user_id'];
		$student_grade = $this->student_model->get_teacher_average($param_grade);
	} else {
		$student_grade = $this->student_model->get_class_average();
	}
	
	// homework
	if (count($student) > 0) {
		$param_task = array(
			'student_id' => $student['s_id'],
			'limit' => 5
		);
		$array_task = $this->task_class_model->get_array($param_task);
	} else {
		$param_task = array(
			'limit' => 5
		);
		if ($user['user_type_id'] == USER_TYPE_TEACHER) {
			$param_task['assign_by'] = $user['user_id'];
		}
		$array_task = $this->task_model->get_array($param_task);
	}
	
	// mail
	$param_mail = array(
		'user_id' => $user['user_id'],
		'user_type_id' => $user['user_type_id'],
		'sort' => '[{"property":"due_date","direction":"DESC"}]',
		'limit' => 5
	);
	$array_mail = $this->mail_model->get_array($param_mail);
	
	// calendar
	$param_calendar = array(
		'sort' => '[{"property":"id","direction":"DESC"}]',
		'limit' => 5
	);
	$array_calendar = $this->calendar_model->get_array($param_calendar);
	
	// parent only
	if ($user['user_type_id'] == USER_TYPE_PARENT) {
		// registration
		$student_registration = $this->register_model->get_non_register(array( 'parent_id' => $user['p_id'] ));
		
		// document
		$array_document = $this->task_model->get_array_document(array( 'student_id' => $user['student_id'] ));
		
		// ask a question
		$array_mail_info = get_array_mail_info();
	}
	
	// get latest attendance
	if ($user['user_type_id'] == USER_TYPE_ADMINISTRATOR) {
		$param_attendance = array( 'limit' => 1 );
		$array_attendance = $this->attendance_model->get_array($param_attendance);
		if (count($array_attendance) > 0) {
			$latest_attendance = $array_attendance[0];
		}
	}
	
	// page
	$array_page['user'] = $user;
	$array_page['character_length'] = 140;
	$array_page['current_date'] = $this->config->item('current_date');
	$array_page['USER_TYPE_PARENT'] = USER_TYPE_PARENT;
	$array_page['USER_TYPE_TEACHER'] = USER_TYPE_TEACHER;
	$array_page['USER_TYPE_ADMINISTRATOR'] = USER_TYPE_ADMINISTRATOR;
	if (isset($latest_attendance) && is_array($latest_attendance)) {
		$array_page['latest_attendance'] = $latest_attendance;
	}
?>

<?php echo $this->load->view( 'common/meta', array( 'parklet' => true ) ); ?>

<style type="text/css">
	.well, .box { padding-bottom: 20px; }
	.dataTables_processing { display: none; }
	
	/* ======================================================================= */
	/* Server Statistics */
	.well.widget-pie-charts .box {
		margin-bottom: -20px;
	}

	/* ======================================================================= */
	/* Why AdminFlare */
	#why-adminflare ul { position: relative; padding: 0 10px; margin: 0 -10px; }
	#why-adminflare ul:nth-child(2n) { background: rgba(0, 0, 0, 0.02); }
	#why-adminflare li { padding: 8px 10px; font-size: 14px; list-style-type: circle; margin: 0 0 0 14px; padding: 0 0 0 5px; }
	#why-adminflare li i { color: #666; font-size: 14px; margin: 3px 0 0 -23px; position: absolute; }
	#why-adminflare .span4 { width: 50%; }
	#why-adminflare .span8 { width: 75%; }
	
	/* table */
	.dataTables_length, .dataTables_filter { top: -35px !important; }
	.dataTables_filter input { height: 29px; }
	
	/* modal */
	.modal-header .close { width: auto; line-height: inherit; }
	.controls input, .controls span.add-on { height: 30px; }
	#modal-update-password .modal-footer input, #modal-update-contact .modal-footer input { margin-top: 0px; float: inherit; }
	
	/* modal weekly checklisk */
	#modal-weekly-check .control-label { width: 150px; }
	#modal-weekly-check .controls { margin-left: 170px; }
</style>

<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	<div class="hide">
		<div class="cnt-page"><?php echo json_encode($array_page); ?></div>
		<iframe name="iframe_attachment" src="<?php echo base_url('upload?callback_name=add_attachment&file_rename=0'); ?>"></iframe>
	</div>
	
	<div id="modal-email" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-emailLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-emailLabel">Email Detail</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">From</label>
					<div class="controls">
						<input type="text" name="from" class="span6" placeholder="From" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Subject</label>
					<div class="controls"><input type="text" name="subject" class="span6" placeholder="Subject" /></div>
				</div>
				<div class="control-group">
					<label class="control-label">Message</label>
					<div class="controls"><textarea name="content" class="span6" style="height: 100px;" placeholder="Message"></textarea></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<div id="modal-task-detail" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-task-detailLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-task-detailLabel">Task Detail</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Class Subject</label>
					<div class="controls">
						<input type="text" name="class_type_name" class="span6" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Assign By</label>
					<div class="controls">
						<input type="text" name="user_display" class="span6" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Task Type</label>
					<div class="controls">
						<input type="text" name="task_type_name" class="span6" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Task Title</label>
					<div class="controls">
						<input type="text" name="task_title" class="span6" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Task Content</label>
					<div class="controls">
						<textarea name="task_content" class="span6"></textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Due Date</label>
					<div class="controls">
						<input type="text" name="task_due_date_swap" />
					</div>
				</div>
				<div class="control-group cnt-grade">
					<label class="control-label">Grade</label>
					<div class="controls">
						<input type="text" name="grade" />
					</div>
				</div>
			</div>
		</form>
	</div>
	
	<div id="modal-calendar" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-calendarLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-calendarLabel">Calendar Detail</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label" style="padding-top: 9px;">Start Date</label>
					<div class="controls">
						<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>">
							<input type="text" name="start_temp_date" class="input-small input-datepicker" size="16" />
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
						<div class="input-append bootstrap-timepicker">
							<input name="start_temp_time" class="timepicker input-small" type="text" />
							<span class="add-on"><i class="icon-time"></i></span>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" style="padding-top: 9px;">End Date</label>
					<div class="controls">
						<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>">
							<input type="text" name="end_temp_date" class="input-small input-datepicker" size="16" />
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
						<div class="input-append bootstrap-timepicker">
							<input name="end_temp_time" class="timepicker input-small" type="text" />
							<span class="add-on"><i class="icon-time"></i></span>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Title</label>
					<div class="controls"><input type="text" name="title" class="span6" placeholder="Title" /></div>
				</div>
				<div class="control-group">
					<label class="control-label">Content</label>
					<div class="controls"><textarea name="content" class="span6" style="height: 100px;" placeholder="Content"></textarea></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<div id="modal-weekly-check" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-weekly-check" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="id" />
			<input type="hidden" name="action" value="update_weekly_checklist" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Weekly Checklist</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Reading Duration<br /><sup>(minute)</sup></label>
					<div class="controls">
						<input type="text" name="duration" class="span6" placeholder="Reading Duration" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Comment</label>
					<div class="controls"><textarea name="content" class="span6" style="height: 100px;" placeholder="Comment"></textarea></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" style="float: right;" />
				<input type="submit" class="btn btn-primary" value="Update" style="margin-top: 7px; margin-right: 10px;" />
			</div>
		</form>
	</div>
	
	<div id="modal-signature" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-signatureLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="action" value="update_signature" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Update Signature</h3>
			</div>
			<div class="modal-body">
				<div class="sigPad signature-weekly">
					<ul class="sigNav">
						<li class="drawIt"><a href="#draw-it">Draw Signature</a></li>
						<li class="clearButton"><a href="#clear">Clear</a></li>
					</ul>
					<div class="sig sigWrapper" style="height: 57px;">
						<div class="typed"></div>
						<canvas class="pad" width="198" height="55"></canvas>
						<input type="hidden" name="output" class="output" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" style="float: right;" />
				<input type="submit" class="btn btn-primary" value="Update" style="margin-top: 7px; margin-right: 10px;" />
			</div>
		</form>
	</div>
	
	<div id="modal-handbook-submit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog"><div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary">Yes</button>
				<button type="button" class="btn btn-close btn-default" data-dismiss="modal" aria-hidden="true">No</button>
			</div>
		</div></div>
	</div>
	
	<div id="attendance-modal" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-weekly-check" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Attendance Tracker Detail</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Student Name</label>
					<div class="controls"><input type="text" name="student_name" class="span6" /></div>
				</div>
				<div class="control-group">
					<label class="control-label">Notification</label>
					<div class="controls"><input type="text" name="notification" class="span6" /></div>
				</div>
				<div class="control-group">
					<label class="control-label">Detail</label>
					<div class="controls"><textarea name="content" class="span6" style="height: 100px;"></textarea></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" style="float: right;" />
			</div>
		</form>
	</div>
	
	<div id="modal-tardy" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-weekly-check" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="action" value="update_tardy_tracker" />
			<input type="hidden" name="id" value="0" />
			<input type="hidden" name="parent_id" value="0" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Tardy Tracker Detail</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Parent Name</label>
					<div class="controls cnt-typeahead">
						<input type="text" name="parent_subject" class="span6 typeahead-parent" placeholder="Select a Parent"/>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Date</label>
					<div class="controls">
						<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>">
							<input type="text" name="due_date" class="input-small input-datepicker" size="16" />
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Student</label>
					<div class="controls">
						<select name="student_id" style="margin-bottom: 0px;">
							<option value="">-</option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Minutes Late</label>
					<div class="controls"><input type="text" name="minute_late" class="span6" placeholder="Amount of Minutes Late"/></div>
				</div>
				<div class="control-group">
					<label class="control-label">Reason</label>
					<div class="controls"><textarea name="reason" class="span6" placeholder="Reason"></textarea></div>
				</div>
				<div class="control-group cnt-input-sms">
					<label class="control-label">Send SMS</label>
					<div class="controls"><input type="checkbox" name="send_sms" checked="checked" value="1" /></div>
				</div>
				<div class="control-group">
					<label class="control-label">Total Tardies</label>
					<div class="controls"><input type="text" name="total_tardy" class="span6" placeholder="Total Tardies" readonly="readonly" /></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" style="float: right;" />
				<input type="submit" class="btn btn-primary" value="Save" style="margin-top: 7px; margin-right: 10px;" />
			</div>
		</form>
	</div>
	
	<?php if (isset($student_registration) && count($student_registration) > 0) { ?>
	<div id="modal-registration" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-registration" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="action" value="update_register" />
			<input type="hidden" name="parent_id" value="<?php echo $user['p_id']; ?>" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>2015-2016 Registration</h3>
			</div>
			<div class="modal-body">
				<div>Jafaria Sunday School has completed its first successful year. Please pick an option below to indicate if you intend to register you child for next year.</div>
				<div style="padding: 8px 0 0 0;">
					<ul>
						<li style="padding: 1px 0;"><label><input type="radio" name="opt_value" value="1" style="margin: 0 0 4px 0;" /> I do wish to register my child for next year</label></li>
						<li style="padding: 1px 0;"><label><input type="radio" name="opt_value" value="2" style="margin: 0 0 4px 0;" /> I do not wish to register my child next year</label></li>
						<li style="padding: 1px 0;"><label><input type="radio" name="opt_value" value="3" style="margin: 0 0 4px 0;" /> Remind me Later</label></li>
					</ul>
				</div>
				<div class="control-group input-child hide">
					<label class="control-label">Student</label>
					<div class="controls">
						<select name="student_id" style="margin-bottom: 0px;">
							<?php echo ShowOption(array( 'Array' => $student_registration, 'ArrayID' => 's_id','ArrayTitle' => 's_name' )); ?>
							<?php if (count($student_registration) > 1) { ?>
							<option value="all">All my children</option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="input-child hide" style="padding: 8px 0;">
					<label><input type="checkbox" name="contact" value="1" style="margin: 0 0 4px 0;" /> I have correct <a class="cursor btn-contact-detail">contact details</a>.</label>
					<label><input type="checkbox" name="agree" value="1" style="margin: 0 0 4px 0;" /> I have read and Agree to the terms and conditions specified in <a href="<?php echo base_url('static/document/handbook.pdf'); ?>" target="_blank">the student handbook</a>.</label>
				</div>
				<div>
					<hr />
					<h3>2014-2015 Survey</h3>
					<div>Thank you for all your help in making the 2014-2015 school year a success, please take a few minutes to let us know how we did by clicking <a href="http://www.jafariaschool.org/survey.html" target="_blank">here</a></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" style="float: right;" />
				<input type="submit" class="btn btn-primary" value="Submit" style="margin-top: 7px; margin-right: 10px;" />
			</div>
		</form>
	</div>
	<?php } ?>
	
	<section class="container">
		<?php if (count($student) > 0) { ?>
		<?php $student_label = $student['s_name']; ?>
		<?php if (!empty($student['quran_level_title'])) { ?>
			<?php $student_label .= ' - '.$student['quran_level_title']; ?>
		<?php } ?>
		<?php if (!empty($student['class_level_name'])) { ?>
			<?php $student_label .= ' - '.$student['class_level_name']; ?>
		<?php } ?>
		
		<div id="banner-region"><div class="banner"><div class="cover-wrapper">
			<div class="cover fill green"></div>
			<div class="container"><div class="row"><div class="col-md-12">
				<div class="unit-wrapper">
					<div class="unit xlarge employee fancy photo-viewable" title="<?php echo $student_label; ?>" style="background-image: none;"><?php echo $student['name_abbreviation']; ?></div>
					<h2><?php echo $student_label; ?></h2>
					<a href="#" class="delete fill red right"><span class="icon icon-times"></span></a>
				</div>
			</div></div></div>
		</div></div></div>
		<?php } ?>
		
		<?php if (count($student_grade) > 0) { ?>
		<section class="row-fluid">
			<div class="well widget-pie-charts">
				<h3 class="box-header">Grades</h3>
				<div class="box no-border non-collapsible">
					<?php if (in_array(CLASS_TYPE_QURAN, $user_class_type)) { ?>
					<div class="span2 pie-chart" title="<?php echo $student_grade['quran_label']; ?>">
						<div id="easy-pie-chart-1" data-percent="<?php echo my_number_format($student_grade['quran_summary']); ?>"><?php echo my_number_format($student_grade['quran_summary']); ?>%</div><br />
						<div class="caption">Quran</div>
					</div>
					<?php } ?>
					
					<?php if (in_array(CLASS_TYPE_FIQH, $user_class_type)) { ?>
					<div class="span2 pie-chart" title="<?php echo $student_grade['figh_label']; ?>">
						<div id="easy-pie-chart-2" data-percent="<?php echo my_number_format($student_grade['figh_summary']); ?>"><?php echo my_number_format($student_grade['figh_summary']); ?>%</div><br />
						<div class="caption">Fiqh</div>
					</div>
					<?php } ?>
					
					<?php if (in_array(CLASS_TYPE_AKHLAG, $user_class_type)) { ?>
					<div class="span2 pie-chart" title="<?php echo $student_grade['akhlaq_label']; ?>">
						<div id="easy-pie-chart-3" data-percent="<?php echo my_number_format($student_grade['akhlaq_summary']); ?>"><?php echo my_number_format($student_grade['akhlaq_summary']); ?>%</div><br />
						<div class="caption">Akhlaq</div>
					</div>
					<?php } ?>
					
					<?php if (in_array(CLASS_TYPE_TAREEKH, $user_class_type)) { ?>
					<div class="span2 pie-chart" title="<?php echo $student_grade['tareekh_label']; ?>">
						<div id="easy-pie-chart-4" data-percent="<?php echo my_number_format($student_grade['tareekh_summary']); ?>"><?php echo my_number_format($student_grade['tareekh_summary']); ?>%</div><br />
						<div class="caption"><?php echo $tareekh['name']; ?></div>
					</div>
					<?php } ?>
					
					<?php if (in_array(CLASS_TYPE_AQAID, $user_class_type) && isset($student_grade['aqaid_summary'])) { ?>
					<div class="span2 pie-chart" title="<?php echo $student_grade['aqaid_label']; ?>">
						<div id="easy-pie-chart-5" data-percent="<?php echo my_number_format($student_grade['aqaid_summary']); ?>"><?php echo my_number_format($student_grade['aqaid_summary']); ?>%</div><br />
						<div class="caption">Aqaid</div>
					</div>
					<?php } ?>
					
					<?php if ($user['user_type_id'] != USER_TYPE_TEACHER) { ?>
					<div class="span2 pie-chart" title="<?php echo $student_grade['attendance_label']; ?>">
						<div id="easy-pie-chart-6" data-percent="<?php echo my_number_format($student_grade['attendance_summary']); ?>"><?php echo my_number_format($student_grade['attendance_summary']); ?>%</div><br />
						<div class="caption">Attendance</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</section>
		<?php } ?>
		
		<?php if ($user['user_type_id'] == USER_TYPE_PARENT) { ?>
			<?php if (count($array_handbook) == 0) { ?>
		<section class="row-fluid" id="form-handbook">
			<h3 class="box-header"><i class="icon-envelope" style="color: #cd522c"></i> Parent & Student Handbook Agreement</h3>
			<div class="box">
				<form><fieldset style="margin: 0px auto; width: 50%;">
					<input type="hidden" name="action" value="handbook_agreement" />
					
					<div>
						Please read <a href="<?php echo base_url('static/document/handbook.pdf'); ?>" target="_blank">The Parent & Student Handbook</a> and complete the signature form below<br /><br />
						I acknowledge that I have read, understood and agreed to the terms outlined in the Parent & Student Handbook linked above<br />
					</div>
					
					<div style="padding: 15px 0;">
						<label>Full Name</label>
						<input type="text" name="full_name" style="margin-bottom: 0px;" class="span12" value="<?php echo $user['user_display']; ?>" />
					</div>
					<div style="padding: 0 0 15px 0;">
						<label>Student Name</label>
						<input type="text" name="student_name" style="margin-bottom: 0px;" class="span12" value="<?php echo $user['student_name']; ?>" readonly="readonly" />
					</div>
					<div style="padding: 0 0 15px 0;">
						<label>Date</label>
						<input type="text" name="current_date" style="margin-bottom: 0px;" class="span12" value="<?php echo date("m-d-Y"); ?>" readonly="readonly" />
					</div>
					<div style="padding: 0 0 15px 0;">
						<div class="span12">
							<div class="sigPad signature-handbook">
								<ul class="sigNav">
									<li class="drawIt"><a href="#draw-it">Draw Signature</a></li>
									<li class="clearButton"><a href="#clear">Clear</a></li>
								</ul>
								<div class="sig sigWrapper" style="height: 57px;">
									<div class="typed"></div>
									<canvas class="pad" width="198" height="55"></canvas>
									<input type="hidden" name="output" class="output" />
								</div>
							</div>
							<div style="clear: both;"></div>
							<div style="padding: 0 0 15px 0;">
								<label style="text-align: center;">or</label>
								<input type="text" name="text_signature" style="margin-bottom: 0px; text-align: center;" class="span12" placeholder="Text Signature" />
							</div>
						</div>
					</div>
					<div style="text-align: center;">
						* By clicking Submit, I agree that the signature and initials will be the electronic representation of my signature and initials for all purposes
						when I (or my agent) use them on documents, including legally binding contracts - just the same as a pen-and-paper signature or initial.
					</div>
					<div class="center">
						<button type="submit" class="btn">Submit</button>
					</div>
				</fieldset></form>
			</div>
		</section>
			<?php } ?>
		<?php } ?>
		
		<section class="row-fluid">
			<?php if ($user['user_type_id'] == USER_TYPE_ADMINISTRATOR) { ?>
			<div class="span12">
				<h3 class="box-header"><i class="icon-bullhorn"></i> Recent News</h3>
				<div class="box">
					<div class="tabbable">
						<ul class="nav nav-tabs box-wide">
							<li class="active"><a href="#tab-comments" data-toggle="tab">Emails</a></li>
							<li><a href="#tab-threads" data-toggle="tab">Calendar</a></li>
							<li><a href="#tab-attendance" data-toggle="tab">Attendance Tracker</a></li>
							<li><a href="#tab-tardy-tracker" data-toggle="tab">Tardy Tracker</a></li>
							<li><a href="#tab-class-ranking" data-toggle="tab">Class Ranking</a></li>
							<li><a href="#tab-quran-ranking" data-toggle="tab">Quran Ranking</a></li>
						</ul>
						<div class="tab-content box-wide box-no-bottom-padding">
							<div class="tab-pane fade in widget-comments active" id="tab-comments">
								<?php if (count($array_mail) > 0) { ?>
									<?php foreach ($array_mail as $row) { ?>
									<div class="thread">
										<div class="content">
											<span class="commented-by">
												<a class="open-email"><?php echo $row['from_title']; ?></a>
												Sent <a class="open-email"><?php echo $row['subject']; ?></a>
											</span>
											<?php echo GetLengthChar($row['content'], 100, ' ...'); ?>
											<span class="actions">
												<a href="#">&nbsp;</a>
												<span class="pull-right"><?php echo $row['time_diff']; ?></span>
											</span>
											<span class="hide raw-record"><?php echo json_encode($row); ?></span>
										</div>
									</div>
									<?php } ?>
								<?php } else { ?>
									<div class="thread">
										<div class="content">
											<span class="commented-by">
												No email found.<br /><br />
											</span>
										</div>
									</div>
								<?php } ?>
								
								<div class="widget-actions">
									<a href="<?php echo base_url('email'); ?>" class="btn btn-mini">Show more</a>
								</div>
							</div>
							<div class="tab-pane fade widget-threads" id="tab-threads">
								<?php if (count($array_calendar) > 0) { ?>
									<?php foreach ($array_calendar as $row) { ?>
									<div class="thread">
										<div class="content">
											<span><?php echo $row['start_end_date']; ?></span>
											<div>
												<a class="open-calendar"><?php echo $row['title']; ?></a><br>
											</div>
											<div class="hide"><?php echo json_encode($row); ?>
												<span class="raw-record"><?php echo json_encode($row); ?></span>
											</div>
										</div>
									</div>
									<?php } ?>
								<?php } else { ?>
									<div class="thread">
										<div class="content">
											<span class="commented-by">
												No calender found.<br /><br />
											</span>
										</div>
									</div>
								<?php } ?>
								
								<div class="widget-actions">
									<a href="<?php echo base_url('calendar'); ?>" class="btn btn-mini">Show more</a>
								</div>
							</div>
							<div class="tab-pane fade widget-threads" id="tab-attendance" style="padding: 10px 0 10px 0;">
								<div class="cnt-filter">
									<div class="row-fluid">
										<div style="padding: 6px 0 0 0;" class="span3 filter-title">Date :</div>
										<div class="span2">
											<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>">
												<input type="text" name="due_date" class="input-small input-datepicker" size="16" style="height: 28px; width: 125px;" />
												<span class="add-on" style="height: 28px;"><i class="icon-calendar"></i></span>
											</div>
										</div>
										<div class="span3 hide" style="padding: 3px 0 0 0; text-align: right;">Day :</div>
										<div class="span2 hide">
											<input type="text" name="due_date_title" style="height: 28px;" readonly="readonly" />
										</div>
									</div>
								</div>
								<div class="cnt-table hide">
									<table class="table table-bordered" id="tab-attendance-table">
										<thead>
											<tr>
												<th style="width: 40%;">Name</th>
												<th style="width: 35%;">Prior Notification</th>
												<th style="width: 15%;">Total Absences</th>
												<th style="width: 10%;">Control</th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
									
									<div class="dataTables_paginate paging_full_numbers more-buttons">
										<button style="margin: 0px;" class="btn btn-attendance-tracker">Send Notifications</button>
									</div>
								</div>
								<div class="cnt-attendance-message" style="margin-top: -30px; padding-bottom: 10px;">&nbsp;</div>
							</div>
							<div class="tab-pane fade widget-threads" id="tab-tardy-tracker" style="padding: 10px 0 10px 0;">
								<div class="cnt-filter">
									<div class="row-fluid">
										<div style="padding: 6px 0 0 0;" class="span3 filter-title">Date :</div>
										<div class="span2">
											<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>">
												<input type="text" name="due_date" class="input-small input-datepicker" size="16" style="height: 28px; width: 125px;" />
												<span class="add-on" style="height: 28px;"><i class="icon-calendar"></i></span>
											</div>
										</div>
										<div class="visible-column-small">
											<div class="span2">
												<input type="button" value="Add Tardy" class="btn btn-tardy-add">
											</div>
										</div>
									</div>
								</div>
								
								<table class="table table-bordered" id="tab-tardy-table">
									<thead>
										<tr>
											<th>Date</th>
											<th>Student</th>
											<th>Reason</th>
											<th>Total Tardies</th>
											<th>Control</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<div class="tab-pane fade widget-threads" id="tab-class-ranking" style="padding: 10px 0 10px 0;">
								<div class="cnt-filter">
									<div class="row-fluid">
										<div style="padding: 6px 0 0 0;" class="span3 filter-title">Class Level :</div>
										<div class="span2">
											<select name="class_level_id" style="width: 100%;">
												<?php echo ShowOption(array( 'Array' => $class_level, 'ArrayTitle' => 'name' )); ?>
											</select>
										</div>
									</div>
								</div>
								
								<div class="cnt-class-ranking"></div>
							</div>
							<div class="tab-pane fade widget-threads" id="tab-quran-ranking" style="padding: 10px 0 10px 0;">
								<div class="cnt-filter">
									<div class="row-fluid">
										<div style="padding: 6px 0 0 0;" class="span3 filter-title">Quran Level :</div>
										<div class="span2">
											<select name="quran_level_id" style="width: 100%;">
												<?php echo ShowOption(array( 'Array' => $quran_level, 'ArrayTitle' => 'name' )); ?>
											</select>
										</div>
									</div>
								</div>
								
								<div class="cnt-quran-ranking"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } else { ?>
			<div class="span6">
				<h3 class="box-header"><i class="icon-book"></i> Homework</h3>
				<div class="box widget-support-tickets">
					<?php if (count($array_task) > 0) { ?>
						<?php foreach ($array_task as $row) { ?>
						<div class="ticket">
							<?php if ($user['user_type_id'] == USER_TYPE_PARENT) { ?>
							<?php echo $row['label_alert']; ?>
							<?php } ?>
							
							<a class="cursor open-task"><?php echo $row['task_title']; ?> <span><?php echo $row['task_content']; ?></span></a>
							<span class="opened-by">
								Assigned by <?php echo $row['class_type_name']; ?> Teacher - <a class="cursor open-task"><?php echo $row['user_display']; ?></a><br />
								<?php echo get_format_date($row['task_due_date'], array( 'date_format' => 'M j, Y H:i')); ?>
							</span>
							<span class="hide raw-record"><?php echo json_encode($row); ?></span>
						</div>
						<?php } ?>
					<?php } else { ?>
						<div class="ticket">
							<a>No Homework assigned.</a>
						</div>
					<?php } ?>
					
					<?php if (in_array($user['user_type_id'], array(USER_TYPE_TEACHER, USER_TYPE_PARENT))) { ?>
					<div class="widget-actions">
						<a href="<?php echo base_url('task'); ?>" class="btn btn-mini">Show more</a>
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="span6">
				<h3 class="box-header"><i class="icon-bullhorn"></i> Recent News</h3>
				<div class="box">
					<div class="tabbable">
						<ul class="nav nav-tabs box-wide">
							<li class="active"><a href="#tab-comments" data-toggle="tab">Emails</a></li>
							<li><a href="#tab-threads" data-toggle="tab">Calendar</a></li>
						</ul>
						<div class="tab-content box-wide box-no-bottom-padding">
							<div class="tab-pane fade in widget-comments active" id="tab-comments">
								<?php if (count($array_mail) > 0) { ?>
									<?php foreach ($array_mail as $row) { ?>
									<div class="thread">
										<div class="content">
											<span class="commented-by">
												<a class="open-email"><?php echo $row['from_title']; ?></a>
												Sent <a class="open-email"><?php echo $row['subject']; ?></a>
											</span>
											<?php echo GetLengthChar($row['content'], 100, ' ...'); ?>
											<span class="actions">
												<a href="#">&nbsp;</a>
												<span class="pull-right"><?php echo $row['time_diff']; ?></span>
											</span>
											<span class="hide raw-record"><?php echo json_encode($row); ?></span>
										</div>
									</div>
									<?php } ?>
								<?php } else { ?>
									<div class="thread">
										<div class="content">
											<span class="commented-by">
												No email found.<br /><br />
											</span>
										</div>
									</div>
								<?php } ?>
								
								<div class="widget-actions">
									<a href="<?php echo base_url('email'); ?>" class="btn btn-mini">Show more</a>
								</div>
							</div>
							<div class="tab-pane fade widget-threads" id="tab-threads">
								<?php if (count($array_calendar) > 0) { ?>
									<?php foreach ($array_calendar as $row) { ?>
									<div class="thread">
										<div class="content">
											<span><?php echo $row['start_end_date']; ?></span>
											<div>
												<a class="open-calendar"><?php echo $row['title']; ?></a><br>
											</div>
											<div class="hide"><?php echo json_encode($row); ?>
												<span class="raw-record"><?php echo json_encode($row); ?></span>
											</div>
										</div>
									</div>
									<?php } ?>
								<?php } else { ?>
									<div class="thread">
										<div class="content">
											<span class="commented-by">
												No calender found.<br /><br />
											</span>
										</div>
									</div>
								<?php } ?>
								
								<div class="widget-actions">
									<a href="<?php echo base_url('calendar'); ?>" class="btn btn-mini">Show more</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</section>
		
		<?php if (in_array($user['user_type_id'], array(USER_TYPE_PRINCIPAL, USER_TYPE_ADMINISTRATOR))) { ?>
		<section class="row-fluid" id="grid-handbook">
			<h3 class="box-header"><i class="icon-envelope" style="color: #cd522c"></i> 2015-2016 Registration</h3>
			<div class="box">
				<div class="tabbable">
					<ul class="nav nav-tabs box-wide">
						<li class="active"><a href="#tab-handbook-done" data-toggle="tab">Complete</a></li>
						<li><a href="#tab-handbook-undone" data-toggle="tab">Not Completed</a></li>
					</ul>
					<div class="tab-content box-wide box-no-bottom-padding">
						<div class="tab-pane fade in widget-comments active" id="tab-handbook-done"><div style="padding: 20px; min-height: 200px;">
							<div class="dataTables_wrapper"><div class="cnt-table" style="background: #FFFFFF;">
								<table class="table table-bordered" id="grid-register">
									<thead>
										<tr>
											<th style="width: 20%;">Student Name</th>
											<th style="width: 15%;">Father Name</th>
											<th style="width: 15%;">Mother Name</th>
											<th style="width: 10%;">Fees Paid</th>
											<th style="width: 15%;">Date Registered</th>
											<th style="width: 10%;">Control</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div></div>
						</div></div>
						<div class="tab-pane fade widget-threads" id="tab-handbook-undone"><div style="padding: 20px; min-height: 200px;">
							<div class="dataTables_wrapper"><div class="cnt-table" style="background: #FFFFFF;">
								<table class="table table-bordered" id="grid-unregister">
									<thead>
										<tr>
											<th style="width: 20%;">Student Name</th>
											<th style="width: 20%;">Father Name</th>
											<th style="width: 20%;">Father Email</th>
											<th style="width: 20%;">Mother Name</th>
											<th style="width: 20%;">Control</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div></div>
						</div></div>
					</div>
				</div>
			</div>
		</section>
		<?php } else if ($user['user_type_id'] == USER_TYPE_TEACHER) { ?>
		<div class="center">
			<img src="<?php echo base_url('static/images/surat-taha.png'); ?>" />
		</div>
		<?php } if ($user['user_type_id'] == USER_TYPE_PARENT) { ?>
		<section class="row-fluid" id="weekly-checklist">
			<h3 class="box-header"><i class="icon-envelope" style="color: #cd522c"></i> Weekly Checklist</h3>
			<div class="box well">
				<div class="dataTables_wrapper">
					<div class="cnt-table" style="background: #FFFFFF;"></div>
					
					<div class="dataTables_paginate paging_full_numbers" style="position: static; width: 275px; margin: 0 auto;">
						<a class="previous paginate_button btn-previous" style="display: block;">Previous</a>
						<a class="paginate_button btn-signature">Signature</a>
						<!-- <a class="paginate_button btn-print">Print</a>  -->
						<a class="last paginate_button btn-next" style="display: block;">Next</a>
					</div>
				</div>
			</div>
		</section>
		
		<section class="row-fluid">
			<div class="span12">
				<h3 class="box-header"><i class="icon-envelope" style="color: #cd522c"></i> Parents Form</h3>
				<div class="box">
					<div class="tabbable">
						<ul class="nav nav-tabs box-wide">
							<li class="active"><a href="#tab-ask-question" data-toggle="tab">Ask a Question</a></li>
							<li><a href="#tab-absence-form" data-toggle="tab">Absence Form</a></li>
							<li><a href="#tab-tardy-form" data-toggle="tab">Tardy Form</a></li>
						</ul>
						<div class="tab-content box-wide box-no-bottom-padding" style="padding: 0 20px 10px;">
							<div class="tab-pane fade in widget-comments active" id="tab-ask-question">
								<section class="row-fluid" id="why-adminflare">
									<form><fieldset>
										<input type="hidden" name="action" value="sent_mail" />
										<input type="hidden" name="subject" value="Ask a Question" />
										
										<div style="padding: 0 0 15px 0;">
											<label>Enter Your Name</label>
											<input type="text" name="temp_name" placeholder="Name" style="margin-bottom: 0px;" class="span4" />
										</div>
										<div style="padding: 0 0 15px 0;">
											<label>Enter Your Email Address</label>
											<input type="text" name="temp_email" placeholder="Email Address" style="margin-bottom: 0px;" class="span4" />
										</div>
										<div style="padding: 0 0 15px 0;">
											<label>Select Who to Ask the Question</label>
											<select name="mail_info" style="margin-bottom: 0px;">
												<?php echo ShowOption(array( 'Array' => $array_mail_info, 'ArrayID' => 'value', 'ArrayTitle' => 'title' )); ?>
											</select>
										</div>
										<div class="cnt-textarea" style="padding: 0 0 15px 0;">
											<div class="hide pull-right"><span>140</span> characters left</div>
											<div><textarea name="temp_content" id="temp_content" rows="3" style="margin: 0px; width: 100%; height: 164px;"></textarea></div>
										</div>
										<div class="cnt-attachment" style="padding: 0 0 15px 0; display: none;">
											Attachemnt
											<ul></ul>
										</div>
										<button type="button" class="btn btn-attachment">Add Attachment</button>
										<button type="submit" class="btn">Submit</button>
									</fieldset></form>
								</section>
							</div>
							<div class="tab-pane fade widget-threads" id="tab-absence-form">
								<section class="row-fluid" id="form-absence">
									<form><fieldset>
										<input type="hidden" name="action" value="sent_mail" />
										<input type="hidden" name="mail_info" value="sent_absence" />
										<input type="hidden" name="subject" value="Absence Form Notification" />
										
										<div style="padding: 0 0 15px 0;">
											<label>Student</label>
											<select name="student_id" style="margin-bottom: 0px;">
												<?php echo ShowOption(array( 'Array' => $user['array_student'], 'ArrayID' => 's_id', 'ArrayTitle' => 's_name' )); ?>
												<?php if (count($user['array_student']) > 1) { ?>
												<option value="all">All Students</option>
												<?php } ?>
											</select>
										</div>
										<div class="control-group">
											<label class="control-label" style="padding-top: 9px;">Date of Expected Absence</label>
											<div class="controls">
												<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>" style="padding-left: 0px;">
													<input type="text" name="absence_date" class="input-small input-datepicker" size="16" />
													<span class="add-on"><i class="icon-calendar"></i></span>
												</div>
											</div>
										</div>
										<div style="padding: 0 0 15px 0;">
											<label>Reason for Absence</label>
											<input type="text" name="reason" placeholder="Reason for Absence" style="margin-bottom: 0px;" class="span8" />
										</div>
										<div class="cnt-textarea" style="padding: 0 0 15px 0;">
											<div class="hide pull-right"><span>140</span> characters left</div>
											<div><textarea name="more_detail" rows="3" style="margin: 0px; width: 100%; height: 164px;"></textarea></div>
										</div>
										
										<button type="submit" class="btn">Submit</button>
									</fieldset></form>
								</section>
							</div>
							<div class="tab-pane fade widget-threads" id="tab-tardy-form">
								<form><fieldset>
									<input type="hidden" name="action" value="update_tardy_tracker" />
									<input type="hidden" name="parent_id" value="<?php echo $user['p_id']; ?>" />
									
									<div style="padding: 0 0 15px 0;">
										<label>Student</label>
										<select name="student_id" style="margin-bottom: 0px;">
											<?php echo ShowOption(array( 'Array' => $user['array_student'], 'ArrayID' => 's_id', 'ArrayTitle' => 's_name' )); ?>
											<?php if (count($user['array_student']) > 1) { ?>
											<option value="all">All Students</option>
											<?php } ?>
										</select>
									</div>
									<div style="padding: 0 0 15px 0;">
										<label>Parent</label>
										<select name="parent_subject" style="margin-bottom: 0px;">
											<option value="">-</option>
											<option value="<?php echo $student['father_name']; ?>"><?php echo $student['father_name']; ?></option>
											<option value="<?php echo $student['mother_name']; ?>"><?php echo $student['mother_name']; ?></option>
										</select>
									</div>
									<div class="control-group">
										<label class="control-label" style="padding-top: 9px;">Date</label>
										<div class="controls">
											<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>" style="padding-left: 0px;">
												<input type="text" name="due_date" class="input-small input-datepicker" size="16" value="<?php echo date("m-d-Y"); ?>" />
												<span class="add-on"><i class="icon-calendar"></i></span>
											</div>
										</div>
									</div>
									<div style="padding: 0 0 15px 0;">
										<label>Amount of Minutes Late</label>
										<input type="text" name="minute_late" placeholder="Amount of Minutes Late" style="margin-bottom: 0px;" class="span8" />
									</div>
									<div style="padding: 0 0 15px 0;">
										<textarea name="reason" rows="3" style="margin: 0px; width: 100%; height: 164px;" placeholder="Reason"></textarea>
									</div>
									<div style="padding: 0 0 15px 0;">
										<label>Total number of tardies</label>
										<input type="text" name="total_tardy" placeholder="Total number of tardies" style="margin-bottom: 0px;" class="span8" readonly="readonly" />
									</div>
									
									<button type="submit" class="btn">Submit</button>
								</fieldset></form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		
		<section class="row-fluid" id="grid-document">
			<h3 class="box-header"><i class="icon-file" style="color: #cd522c"></i> Documents</h3>
			<div class="box well">
				<table class="table table-striped" id="table-document">
					<thead>
						<tr>
							<th style="width: 30%;">Task Title</th>
							<th class="column-small">File</th>
							<th class="center" style="width: 20%;">Control</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($array_document as $row) { ?>
						<tr>
							<td><?php echo $row['task_title']; ?></td>
							<td class="column-small"><?php echo $row['file_only']; ?></td>
							<td class="center">
								<span class="cursor-font-awesome icon-link btn-links" data-original-title="Download"></span>
								<span class="hide"><?php echo json_encode($row); ?></span>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</section>
		<?php } ?>
		
		<?php echo $this->load->view( 'common/footer' ); ?>
	</section>
</body>

<script type="text/javascript">
$(document).ready(function () {
	var page = {
		init: function() {
			var raw = $('.cnt-page').html();
			eval('var data = ' + raw);
			page.data = data;
		}
	}
	page.init();
	
	// upload
	$('#why-adminflare .btn-attachment').click(function() { window.iframe_attachment.browse() });
	add_attachment = function(p) {
		// show container
		$('#why-adminflare .cnt-attachment').show();
		
		// append
		var raw_html = '<li>' + p.file_only + ' <a class="cursor btn-remove">(remove)</a> <input type="hidden" name="array_attachment[]" value=\'' + Func.object_to_json(p) + '\' /></li>';
		$('.cnt-attachment ul').append(raw_html);
		
		// init
		$('#why-adminflare .btn-remove').last().click(function() {
			$(this).parent('li').remove();
		});
	}
	
	// chart
	var easyPieChartDefaults = { animate: 2000, scaleColor: false, lineWidth: 12, lineCap: 'square', size: 100, trackColor: '#e5e5e5' }
	$('#easy-pie-chart-1').easyPieChart($.extend({}, easyPieChartDefaults, { barColor: Func.get_color($('#easy-pie-chart-1').data('percent')) }));
	$('#easy-pie-chart-2').easyPieChart($.extend({}, easyPieChartDefaults, { barColor: Func.get_color($('#easy-pie-chart-2').data('percent')) }));
	$('#easy-pie-chart-3').easyPieChart($.extend({}, easyPieChartDefaults, { barColor: Func.get_color($('#easy-pie-chart-3').data('percent')) }));
	$('#easy-pie-chart-4').easyPieChart($.extend({}, easyPieChartDefaults, { barColor: Func.get_color($('#easy-pie-chart-4').data('percent')) }));
	$('#easy-pie-chart-5').easyPieChart($.extend({}, easyPieChartDefaults, { barColor: Func.get_color($('#easy-pie-chart-5').data('percent')) }));
	$('#easy-pie-chart-6').easyPieChart($.extend({}, easyPieChartDefaults, { barColor: Func.get_color($('#easy-pie-chart-6').data('percent')) }));
	
	// grid document
	$('#table-document').DataTable();
	$('#table-document .cursor-font-awesome').tooltip({ placement: 'top' });
	$('.btn-links').click(function() {
		var raw_record = $(this).parent('td').find('span.hide').text();
		eval('var record = ' + raw_record);
		window.open(record.file_link);
	});
	
	// task
	$('.open-task').click(function() {
		var raw_record = $(this).parents('.ticket').find('.raw-record').text();
		eval('var record = ' + raw_record);
		
		// set record
		if (record.due_date_swap != null) {
			record.task_due_date_swap = record.due_date_swap;
		}
		
		// set view
		if (page.data.user.user_type_id == page.data.USER_TYPE_PARENT) {
			$('#modal-task-detail .cnt-grade').show();
		} else {
			$('#modal-task-detail .cnt-grade').hide();
		}
		
		// show modal
		Func.populate({ cnt: '#modal-task-detail', record: record });
		$('#modal-task-detail').modal();
	});
	
	// email
	$('.open-email').click(function() {
		var raw_record = $(this).parents('.thread').find('.raw-record').text();
		eval('var record = ' + raw_record);
		
		// show modal
		Func.populate({ cnt: '#modal-email', record: record });
		$('#modal-email [name="from"]').val(record.from_title + '<' + record.from_email + '>');
		$('#modal-email').modal();
	});
	
	// calendar
	$('.open-calendar').click(function() {
		var raw_record = $(this).parents('.thread').find('.raw-record').text();
		eval('var record = ' + raw_record);
		
		// show modal
		Func.populate({ cnt: '#modal-calendar', record: record });
		$('#modal-calendar').modal();
	});
	
	// ask a question
	$('#why-adminflare [name="temp_content"]').keyup(function() {
		var value = $(this).val();
		var char_left = page.data.character_length - value.length;
		var el_parent = $(this).parents('.cnt-textarea');
		
		if (char_left < 0) {
			el_parent.find('.pull-right').addClass('red');
		} else {
			el_parent.find('.pull-right').removeClass('red');
		}
		el_parent.find('.pull-right span').html(char_left);
	});
	$('#why-adminflare form').validate({
		rules: {
			temp_name: { required: true },
			temp_email: { required: true, email: true },
			mail_info: { required: true },
			temp_content: { required: true }
		}
	});
	$('#why-adminflare form').submit(function(e) {
		e.preventDefault();
		if (! $('#why-adminflare form').valid()) {
			return false;
		}
		
		// param
		var param = Func.form.get_value('why-adminflare');
		
		// build content
		param.content  = "Dear,<br />\n";
		param.content += "Name : " + param.temp_name + "<br />\n";
		param.content += "Email : " + param.temp_email + "<br />\n";
		param.content += "Ask Message<br />\n";
		param.content += param.temp_content;
		
		// ajax request
		Func.form.submit({
			url: web.base + 'email/action',
			param: param,
			callback: function(result) {
				$('#why-adminflare form')[0].reset();
				$('#why-adminflare .cnt-attachment').hide();
				$('#why-adminflare .cnt-attachment ul').html('');
			}
		});
	});
	
	// form absence
	$('#form-absence [name="more_detail"]').keyup(function() {
		var value = $(this).val();
		var char_left = page.data.character_length - value.length;
		var el_parent = $(this).parents('.cnt-textarea');
		
		if (char_left < 0) {
			el_parent.find('.pull-right').addClass('red');
		} else {
			el_parent.find('.pull-right').removeClass('red');
		}
		el_parent.find('.pull-right span').html(char_left);
	});
	$('#form-absence form').validate({
		rules: {
			student_id: { required: true },
			absence_date: { required: true },
			reason: { required: true }
		}
	});
	$('#form-absence form').submit(function(e) {
		e.preventDefault();
		if (! $('#form-absence form').valid()) {
			return false;
		}
		
		// ajax request
		Func.form.submit({
			url: web.base + 'email/action',
			param: Func.form.get_value('form-absence'),
			callback: function(result) {
				$('#form-absence form')[0].reset();
			}
		});
	});
	
	// tardy tracker parent
	$('#tab-tardy-form [name="student_id"]').change(function() {
		Func.ajax({
			param: { action: 'get_tardy_count', student_id: $('#tab-tardy-form [name="student_id"]').val() },
			url: web.base + 'home/action',
			callback: function(result) {
				$('#tab-tardy-form [name="total_tardy"]').val(result.tardy_next);
			}
		});
	});
	$('#tab-tardy-form form').validate({
		rules: {
			student_id: { required: true },
			parent_subject: { required: true },
			due_date: { required: true },
			minute_late: { required: true },
			reason: { required: true }
		}
	});
	$('#tab-tardy-form form').submit(function(e) {
		e.preventDefault();
		if (! $('#tab-tardy-form form').valid()) {
			return false;
		}
		
		// ajax request
		Func.form.submit({
			url: web.base + 'home/action',
			param: Func.form.get_value('tab-tardy-form'),
			callback: function(result) {
				$('#tab-tardy-form form')[0].reset();
			}
		});
	});
	
	// form handbook
	if ($('#grid-handbook').length > 0) {
		// complete
		var complete_param = {
			id: 'grid-register',
			source: 'home/grid', aaSorting: [[ 0, "ASC" ]],
			column: [ { }, { sClass: 'column-small' }, { sClass: 'column-small' }, { bSortable: false, sClass: 'center column-small' }, { sClass: 'center column-small' }, { bSortable: false, sClass: 'center' } ],
			fnServerParams: function(aoData) {
				aoData.push(
					{ name: 'grid_type', value: 'register_student' }
				);
			},
			callback: function() {
				$('#grid-register .btn-document').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					window.open(record.handbook_link);
				});
				
				$('#grid-register .btn-paid').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					
					Func.form.submit({
						url: web.base + 'home/action',
						param: { action: 'update_register_row', id: record.id, is_paid: 1 },
						callback: function(result) {
							complete_dt.reload();
						}
					});
				});
				
				$('#grid-register .btn-unpaid').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					
					Func.form.submit({
						url: web.base + 'home/action',
						param: { action: 'update_register_row', id: record.id, is_paid: 0 },
						callback: function(result) {
							complete_dt.reload();
						}
					});
				});
			}
		}
		var complete_dt = Func.datatable(complete_param);
		
		// uncomplete
		var uncomplete_param = {
			id: 'grid-unregister',
			source: 'home/grid', aaSorting: [[ 0, "ASC" ]],
			column: [ { }, { sClass: 'column-small' }, { sClass: 'column-small' }, { sClass: 'column-small' }, { bSortable: false, sClass: 'center' } ],
			init: function() {
				$('#grid-unregister_length').prepend(
					'<div style="float: left; padding: 0 5px 0 0;">' +
						'<div class="btn-group open">' +
							'<button data-toggle="dropdown" class="btn btn-notification dropdown-toggle" style="margin: 0px;">Send Notification <span class="caret"></span></button>' +
							'<ul class="dropdown-menu">' +
								'<li><a class="cursor btn-request-handbook">All Parents</a></li>' +
								'<li><a class="cursor btn-request-handbook" data-limit="10">First 10 Parents</a></li>' +
							'</ul>' +
						'</div>' +
					'</div>'
				);
			},
			fnServerParams: function(aoData) {
				aoData.push(
					{ name: 'grid_type', value: 'unregister_student' }
				);
			},
			callback: function() {
				
				$('#grid-unregister .btn-mail').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					
					Func.form.submit({
						url: web.base + 'home/action',
						param: { action: 'register_notification', parent_id: record.parent_id }
					});
				});
			}
		}
		var uncomplete_dt = Func.datatable(uncomplete_param);
		$('#tab-handbook-undone .btn-request-handbook').click(function() {
			var limit = $(this).data('limit');
			var param = { action: 'register_notification' };
			if (typeof(limit) == 'number') {
				param.limit = limit;
			}
			
			$('#grid-handbook .btn-notification').attr('disabled', true);
			Func.form.submit({
				url: web.base + 'home/action',
				param: param,
				callback: function(result) {
					$('#grid-handbook .btn-notification').attr('disabled', false);
				}
			});
		});
	}
	if ($('#form-handbook').length > 0) {
		// signature
		$('.signature-handbook').signaturePad({ drawOnly: true, validateFields: false });
		
		// form handbook
		$('#form-handbook form').validate({
			rules: {
				full_name: { required: true }
			}
		});
		$('#form-handbook form').submit(function(e) {
			e.preventDefault();
			if (! $('#form-handbook form').valid()) {
				return false;
			}
			
			// param
			var param = Func.form.get_value('form-handbook');
			if (param.output == '' && param.text_signature == '') {
				$.notify('Please enter signature.', "error");
				return;
			}
			
			// show modal
			$('#modal-handbook-submit').modal();
		});
		
		// modal handbook
		$('#modal-handbook-submit .btn-primary').click(function() {
			$('#modal-handbook-submit .modal-body p').html("Please wait ... it's takes 1 - 5 minutes");
			$('#modal-handbook-submit .modal-footer .btn').attr('disabled', true);
			Func.form.submit({
				url: web.base + 'home/handbook_agreement',
				param: Func.form.get_value('form-handbook'),
				callback: function(result) {
					$('#form-handbook').hide();
					$('#modal-handbook-submit .modal-footer .btn').attr('disabled', false);
					$('#modal-handbook-submit .btn-close').click();
				}
			});
		});
	}
	
	// weekly checklist
	if ($('#weekly-checklist').length > 0) {
		var weekly = {
			init_table: function() {
				$('#weekly-checklist .btn-edit').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					
					// make sure signature exist
					Func.ajax({
						param: { action: 'get_user' },
						url: web.base + 'home/action',
						callback: function(result) {
							// show modal
							$('#modal-weekly-check form')[0].reset();
							Func.populate({ cnt: '#modal-weekly-check', record: record });
							$('#modal-weekly-check').modal();
						}
					});
				});
			},
			load_grid: function(p) {
				p.param = (p.param == null) ? {} : p.param;
				p.param.start_date = (p.param.start_date == null) ? '' : p.param.start_date;
				
				Func.ajax({
					url: web.base + 'home/view',
					param: p.param,
					is_json: false,
					callback: function(result) {
						$('#weekly-checklist .cnt-table').html(result);
						weekly.init_table();
					}
				});
			}
		}
		
		// autoload
		weekly.load_grid({ param: { action: 'weekly_checklist' } });
		
		// signature
		$('.signature-weekly').signaturePad({ drawOnly: true, validateFields: false });
		
		// button
		$('#weekly-checklist .btn-previous').click(function() {
			var param_temp = Func.form.get_value('weekly-checklist');
			var start_date = new Date(param_temp.start_date);
			start_date.setDate(start_date.getDate() - 7);
			
			// load grid
			var year = start_date.getFullYear();
			var month = str_pad(start_date.getMonth() + 1, 2, '0', 'STR_PAD_LEFT');
			var day = str_pad(start_date.getDate(), 2, '0', 'STR_PAD_LEFT');
			var start_date_string = year + '-' + month + '-' + day;
			weekly.load_grid({ param: { action: 'weekly_checklist', start_date: start_date_string } });
		});
		$('#weekly-checklist .btn-next').click(function() {
			var param_temp = Func.form.get_value('weekly-checklist');
			var start_date = new Date(param_temp.start_date);
			start_date.setDate(start_date.getDate() + 7);
			
			// load grid
			var year = start_date.getFullYear();
			var month = str_pad(start_date.getMonth() + 1, 2, '0', 'STR_PAD_LEFT');
			var day = str_pad(start_date.getDate(), 2, '0', 'STR_PAD_LEFT');
			var start_date_string = year + '-' + month + '-' + day;
			weekly.load_grid({ param: { action: 'weekly_checklist', start_date: start_date_string } });
		});
		$('#weekly-checklist .btn-print').click(function() {
			var param = Func.form.get_value('weekly-checklist');
			Func.ajax({
				param: { action: 'get_user' },
				url: web.base + 'home/action',
				callback: function(result) {
					if (typeof(result.p_sign_image_link) != 'undefined' && result.p_sign_image_link != '') {
						window.open(web.base + 'home/print_weekly/' + param.start_date);
					} else {
						$.notify("Please enter signature before continue.", "error");
					}
				}
			});
		});
		$('#weekly-checklist .btn-signature').click(function() {
			$('#modal-signature').modal();
		});
		if (page.data.user.p_sign_image != '') {
			$('#weekly-checklist .btn-signature').hide();
		}
		
		// form weekly check
		$('#modal-weekly-check form').validate({
			rules: {
				duration: { required: true, number: true }
			}
		});
		$('#modal-weekly-check form').submit(function(e) {
			e.preventDefault();
			if (! $('#modal-weekly-check form').valid()) {
				return false;
			}
			
			// param
			var param = Func.form.get_value('modal-weekly-check');
			
			// ajax request
			Func.form.submit({
				url: web.base + 'home/action',
				param: param,
				callback: function(result) {
					$('#modal-weekly-check form')[0].reset();
					$('#modal-weekly-check').modal('hide');
					
					// load grid
					var param_temp = Func.form.get_value('weekly-checklist');
					weekly.load_grid({ param: { action: 'weekly_checklist', start_date: param_temp.start_date } });
				}
			});
		});
		
		// form signature
		$('#modal-signature form').submit(function(e) {
			e.preventDefault();
			
			// param
			var param = Func.form.get_value('modal-signature');
			if (param.output == '') {
				$.notify('Signature is empty.', "error");
				return;
			}
			
			// ajax request
			Func.form.submit({
				url: web.base + 'home/action',
				param: param,
				callback: function(result) {
					$('#modal-signature form')[0].reset();
					$('#modal-signature .signature-weekly .clearButton').click();
					$('#modal-signature').modal('hide');
				}
			});
		});
	}
	
	// attendance tracker
	$('#tab-attendance [name="due_date"]').change(function() {
		var param = Func.form.get_value('#tab-attendance .row-fluid');
		
		// grid
		dt_attendance_tracker.reload();
		$('#tab-attendance .cnt-table').show();
		
		// message
		Func.ajax({
			param: { action: 'get_attendance_message', due_date: param.due_date },
			url: web.base + 'home/action',
			callback: function(result) {
				$('#tab-attendance .cnt-attendance-message').text(result.message);
			}
		});
		
	});
	var param_attendance_tracker = {
		id: 'tab-attendance-table',
		source: web.base + 'home/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { bSortable: false, sClass: 'column-small' }, { bSortable: false, sClass: 'center column-small' }, { bSortable: false, sClass: 'center' } ],
		fnServerParams: function(aoData) {
			var param = Func.form.get_value('#tab-attendance .row-fluid');
			aoData.push(
				{ name: 'grid_type', value: 'attendance_tracker' },
				{ name: 'due_date', value: param.due_date }
			);
		},
		callback: function() {
			$('#tab-attendance .btn-excuse').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// form
				var param = Func.form.get_value('#tab-attendance .row-fluid');
				
				Func.ajax({
					param: { action: 'attendance_excuse', student_id: record.student_id, due_date: param.due_date },
					url: web.base + 'home/action',
					callback: function(result) {
						$.notify(result.message, 'success');
						$('#tab-attendance [name="due_date"]').change();
					}
				});
			});
			
			$('#tab-attendance .btn-message').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				Func.populate({ cnt: '#attendance-modal', record: record });
				$('#attendance-modal').modal();
			});
		}
	}
	var dt_attendance_tracker = Func.datatable(param_attendance_tracker);
	$('#tab-attendance .btn-attendance-tracker').click(function() {
		var param = Func.form.get_value('#tab-attendance .cnt-filter');
		if (param.due_date == '') {
			$.notify('Please enter Due Date', "error");
			return;
		}
		Func.ajax({
			param: param,
			url: web.base + 'service/attendance_tracker',
			callback: function(result) {
				$.notify(result.message, 'success');
			}
		});
	});
	if (typeof(page.data.latest_attendance) != 'undefined') {
		Func.populate({ cnt: '#tab-attendance .cnt-filter', record: page.data.latest_attendance });
		$('#tab-attendance [name="due_date"]').change();
	}
	
	// tardy tracker administrator datatable
	$('#tab-tardy-tracker [name="due_date"]').change(function() {
		dt_tardy_tracker.reload();
	});
	var param_tardy_tracker = {
		id: 'tab-tardy-table',
		source: web.base + 'home/grid', aaSorting: [[ 0, "DESC" ]],
		column: [ { sClass: 'center column-small' }, { }, { sClass: 'column-small' }, { bSortable: false, sClass: 'center column-small' }, { bSortable: false, sClass: 'center' } ],
		fnServerParams: function(aoData) {
			aoData.push({ name: 'grid_type', value: 'tardy_tracker' });
			
			// due date
			var param = Func.form.get_value('#tab-tardy-tracker .cnt-filter');
			if (param.due_date != '') {
				aoData.push({ name: 'due_date', value: param.due_date });
			}
		},
		init: function() {
			$('#tab-tardy-table_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-tardy-add" value="Add Tardy" style="margin: 0px;" /></div>');
		},
		callback: function() {
			$('#tab-tardy-table .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// populate
				Func.populate({ cnt: '#modal-tardy', record: record });
				combo.student({ s_parent_id: record.parent_id, target: $('#modal-tardy [name="student_id"]'), value: record.student_id });
				$('#modal-tardy [name="parent_subject"]').html('<option value="' + record.parent_subject + '">' + record.parent_subject + '</option>');
				$('#modal-tardy').modal();
			});
			
			$('#tab-tardy-table .btn-excuse').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// form
				var param = Func.form.get_value('#tab-tardy-table .row-fluid');
				Func.form.submit({
					url: web.base + 'home/action',
					param: { action: 'update_tardy_excuse', id: record.id },
					callback: function(result) {
						dt_tardy_tracker.reload();
					}
				});
			});
			
			$('#tab-tardy-table .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete_tardy_tracker', id: record.id },
					url: web.base + 'home/action', callback: function() { dt_tardy_tracker.reload(); }
				});
			});
		}
	}
	var dt_tardy_tracker = Func.datatable(param_tardy_tracker);
	
	// tardy tracker administrator autocomplete
	var parent_store = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('p_father_name'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		prefetch: web.base + 'typeahead?action=parent',
		remote: web.base + 'typeahead?action=parent&namelike=%QUERY'
	});
	parent_store.initialize();
	var parent_combo = $('.typeahead-parent').typeahead(null, {
		name: 'parent',
		displayKey: 'p_father_name',
		source: parent_store.ttAdapter(),
		templates: { empty: [ '<div class="empty-message">', 'no result found.', '</div>' ].join('\n'), suggestion: Handlebars.compile('<p><strong>{{p_father_name}}</strong></p>') }
	});
	parent_combo.on('typeahead:selected', function(evt, data) {
		// set parent id
		$('#modal-tardy [name="parent_id"]').val(data.p_id);
		
		// set student option
		combo.student({ s_parent_id: data.p_id, target: $('#modal-tardy [name="student_id"]'), callback: function() {
			if ($('#modal-tardy [name="student_id"] option').length > 2) {
				$('#modal-tardy [name="student_id"]').append('<option value="all">All Students</option>');
			}
		} });
	});
	
	// tardy tracker administrator form
	$('.btn-tardy-add').click(function() {
		$('#modal-tardy form')[0].reset();
		$('#modal-tardy').modal();
		Func.populate({ cnt: '#modal-tardy', record: { id: 0, parent_id: 0, due_date: page.data.current_date } });
	});
	$('#modal-tardy [name="student_id"]').change(function() {
		Func.ajax({
			param: { action: 'get_tardy_count', student_id: $('#modal-tardy [name="student_id"]').val() },
			url: web.base + 'home/action',
			callback: function(result) {
				$('#modal-tardy [name="total_tardy"]').val(result.tardy_next);
			}
		});
	});
	$('#modal-tardy form').validate({
		rules: {
			parent_subject: { required: true },
			due_date: { required: true },
			student_id: { required: true },
			minute_late: { required: true },
			reason: { required: true }
		}
	});
	$('#modal-tardy form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-tardy form').valid()) {
			return false;
		}
		
		// ajax request
		Func.form.submit({
			url: web.base + 'home/action',
			param: Func.form.get_value('modal-tardy'),
			callback: function(result) {
				dt_tardy_tracker.reload();
				$('#modal-tardy').modal('hide') 
				$('#modal-tardy form')[0].reset();
			}
		});
	});
	
	// registration
	if ($('#modal-registration').length > 0) {
		$('#modal-registration').modal();
		
		// form
		$('#modal-registration [name="opt_value"]').click(function() {
			var param = Func.form.get_value('modal-registration');
			if (param.opt_value == 1) {
				$('#modal-registration .input-child').show();
			} else {
				$('#modal-registration .input-child').hide();
			}
		});
		$('#modal-registration .btn-contact-detail').click(function() {
			$('#modal-registration').modal('hide');
			
			// set callback
			Func.callback = function() {
				$('#modal-registration').modal();
				delete Func.callback;
			}
		});
		$('#modal-registration form').submit(function(e) {
			e.preventDefault();
			var param = Func.form.get_value('modal-registration');
			if (param.opt_value == null) {
				$.notify("Please select your option", "error");
				return false;
			} else if (param.opt_value == 1) {
				if (param.student_id == '') {
					$.notify("Please select children", "error");
					return false;
				} else if (param.contact == 0) {
					$.notify("Please check contact details", "error");
					return false;
				} else if (param.agree == 0) {
					$.notify("Please check our aggrement", "error");
					return false;
				}
			} else if (param.opt_value == 3) {
				$('#modal-registration').modal('hide');
				return false;
			}
			
			// ajax request
			$('#modal-registration [type="submit"]').attr('disabled', true);
			Func.form.submit({
				url: web.base + 'home/action',
				param: param,
				callback: function(result) {
					$('#modal-registration').modal('hide');
					$('#modal-registration [type="submit"]').attr('disabled', false);
				}
			});
		});
	}
	
	// class ranking
	$('#tab-class-ranking [name="class_level_id"]').change(function() {
		$('#tab-class-ranking .cnt-class-ranking').html('<div style="text-align: center;"><img src="' + web.base + 'static/images/loading.gif" style="width: 25px;" /></div>');
		var class_level_id = $('#tab-class-ranking [name="class_level_id"]').val();
		if (class_level_id != '') {
			Func.ajax({
				param: { action: 'class_ranking', class_level_id: class_level_id },
				url: web.base + 'home/view', is_json: false,
				callback: function(result) {
					$('#tab-class-ranking .cnt-class-ranking').html(result);
					$('#tab-class-ranking table').dataTable();
				}
			});
		} else {
			$('#tab-class-ranking .cnt-class-ranking').html('');
		}
	});
	
	// quran ranking
	$('#tab-quran-ranking [name="quran_level_id"]').change(function() {
		$('#tab-quran-ranking .cnt-quran-ranking').html('<div style="text-align: center;"><img src="' + web.base + 'static/images/loading.gif" style="width: 25px;" /></div>');
		var quran_level_id = $('#tab-quran-ranking [name="quran_level_id"]').val();
		if (quran_level_id != '') {
			Func.ajax({
				param: { action: 'quran_ranking', quran_level_id: quran_level_id },
				url: web.base + 'home/view', is_json: false,
				callback: function(result) {
					$('#tab-quran-ranking .cnt-quran-ranking').html(result);
					$('#tab-quran-ranking table').dataTable();
				}
			});
		} else {
			$('#tab-quran-ranking .cnt-quran-ranking').html('');
		}
	});
	
	// tooltips
	$('.widget-pie-charts .pie-chart').tooltip({ placement: 'top' });
});
</script>

</html>
