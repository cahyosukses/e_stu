<?php
	// user
	$user = $this->user_model->get_session();
	
	// get teacher class type
	$user_class_type = array( 1, 2, 3, 4, 5 );
	if ($user['user_type_id'] == USER_TYPE_TEACHER) {
		$user_class_type = $this->teacher_class_model->get_class_teacher(array( 'user_id' => $user['user_id'], 'return_list_id' => true ));
	}
	
	// master
	$tareekh = $this->class_type_model->get_by_id(array( 'id' => CLASS_TYPE_TAREEKH ));
	
	// get average grade
	$student_grade = $this->student_model->get_class_average();
?>

<div class="well widget-pie-charts">
	<h3 class="box-header">Average Grades</h3>
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
	</div>
</div>