<?php
	// view_type
	$view_type = (isset($view_type)) ? $view_type : 'normal';
	
	// user
	$user = $this->user_model->get_session();
?>

<?php if ($view_type == 'stand-alone') { ?>
<nav id="left-panel">
	<div id="left-panel-content">
		<ul>
		</ul>
	</div>
</nav>
<?php } else { ?>
<nav id="left-panel">
	<div id="left-panel-content">
		<ul>
			<?php if ($user['user_type_id'] == USER_TYPE_ADMINISTRATOR) { ?>
			<li><a href="<?php echo base_url(); ?>"><span class="icon-dashboard"></span>Dashboard</a></li>
			<li><a href="<?php echo base_url('classes'); ?>"><span class="icon-inbox"></span>Class</a></li>
			<li><a href="<?php echo base_url('attendance'); ?>"><span class="icon-check"></span>Attendance</a></li>
			<li><a href="<?php echo base_url('grade'); ?>"><span class="icon-certificate"></span>Grade</a></li>
			<li><a href="<?php echo base_url('calendar'); ?>"><span class="icon-calendar"></span>Calendar</a></li>
			<!--   <li><a href="<?php echo base_url('fee'); ?>"><span class="icon-credit-card"></span>Fee</a></li>   -->
			
			
			<li><a href="<?php echo base_url('report_card'); ?>"><span class="icon-envelope"></span>Report Card</a></li>
			<li><a href="<?php echo base_url('schedule'); ?>"><span class="icon-time"></span>Schedule</a></li>
			<li class="lp-dropdown">
				<a href="#" class="lp-dropdown-toggle" id="contact-dropdown"><span class="icon-th-large"></span>Contact</a>
				<ul class="lp-dropdown-menu" data-dropdown-owner="contact-dropdown">
					<li><a tabindex="-1" href="<?php echo base_url('email'); ?>"><span class="icon-envelope"></span>Email</a></li>
					<li><a tabindex="-2" href="<?php echo base_url('sms'); ?>"><span class="icon-mobile-phone"></span>SMS</a></li>
				</ul>
			</li>
			<li class="lp-dropdown">
				<a href="#" class="lp-dropdown-toggle" id="extras-dropdown"><span class="icon-th-large"></span>Master</a>
				<ul class="lp-dropdown-menu" data-dropdown-owner="extras-dropdown">
					<li><a tabindex="-1" href="<?php echo base_url('master/student'); ?>"><span class="icon-group"></span>Student</a></li>
					<li><a tabindex="-2" href="<?php echo base_url('master/parents'); ?>"><span class="icon-group"></span>Parent</a></li>
					<li><a tabindex="-3" href="<?php echo base_url('master/user'); ?>"><span class="icon-group"></span>User</a></li>
					<li><a tabindex="-3" href="<?php echo base_url('master/config'); ?>"><span class="icon-cog"></span>Config</a></li>
				</ul>
			</li>
			<li class="lp-dropdown">
				<a href="#" class="lp-dropdown-toggle" id="level-dropdown"><span class="icon-th-large"></span>Level</a>
				<ul class="lp-dropdown-menu" data-dropdown-owner="level-dropdown">
					<li><a href="<?php echo base_url('level/quran_level'); ?>"><span class="icon-th-large"></span>Quran</a></li>
					<li><a href="<?php echo base_url('level/class_level'); ?>"><span class="icon-th-large"></span>Class</a></li>
				</ul>
			</li>
			<?php } else if ($user['user_type_id'] == USER_TYPE_TEACHER) { ?>
			<li><a href="<?php echo base_url(); ?>"><span class="icon-dashboard"></span>Dashboard</a></li>
			<li><a href="<?php echo base_url('classes'); ?>"><span class="icon-inbox"></span>Class</a></li>
			<li><a href="<?php echo base_url('task'); ?>"><span class="icon-book"></span>Task</a></li>
			<li><a href="<?php echo base_url('attendance'); ?>"><span class="icon-check"></span>Attendance</a></li>
			<li><a href="<?php echo base_url('email'); ?>"><span class="icon-envelope"></span>Email</a></li>
			<li><a href="<?php echo base_url('sms'); ?>"><span class="icon-mobile-phone"></span>SMS</a></li>
			<?php } else if ($user['user_type_id'] == USER_TYPE_PRINCIPAL) { ?>
			<li><a href="<?php echo base_url(); ?>"><span class="icon-dashboard"></span>Dashboard</a></li>
			<li><a href="<?php echo base_url('classes'); ?>"><span class="icon-inbox"></span>Class</a></li>
			<li><a href="<?php echo base_url('attendance'); ?>"><span class="icon-check"></span>Attendance</a></li>
			<li><a href="<?php echo base_url('email'); ?>"><span class="icon-envelope"></span>Email</a></li>
			<?php } else { ?>
			<li><a href="<?php echo base_url(); ?>"><span class="icon-dashboard"></span>Dashboard</a></li>
			<li><a href="<?php echo base_url('task'); ?>"><span class="icon-book"></span>Task</a></li>
			<li><a href="<?php echo base_url('attendance'); ?>"><span class="icon-check"></span>Attendance</a></li>
			<li><a href="<?php echo base_url('calendar'); ?>"><span class="icon-calendar"></span>Calendar</a></li>
			<li><a href="<?php echo base_url('email'); ?>"><span class="icon-envelope"></span>Email</a></li>
			<li><a href="<?php echo base_url('meeting'); ?>"><span class="icon-time"></span>Meeting</a></li>
			<li><a href="<?php echo base_url('teacher'); ?>"><span class="icon-group"></span>Teacher</a></li>
			<?php } ?>
			
			<!--
			<li>
				<a href="layout.html"><span class="icon-th-large"></span>Layout</a>
			</li>
			<li>
				<a href="typography.html"><span class="icon-font"></span>Typography</a>
			</li>
			<li>
				<a href="forms.html"><span class="icon-edit"></span>Forms</a>
			</li>
			<li class="active">
				<a href="tables.html"><span class="icon-table"></span>Tables</a>
			</li>
			
			<li>
				<a href="javascript.html"><span class="icon-cog"></span>JavaScript</a>
			</li>
			<li class="lp-dropdown">
				<a href="#" class="lp-dropdown-toggle" id="extras-dropdown"><span class="icon-reorder"></span>Extras</a>
				<ul class="lp-dropdown-menu" data-dropdown-owner="extras-dropdown">
					<li>
						<a tabindex="-1" href="extras-icons.html"><span class="icon-coffee"></span>Icons</a>
					</li>
					<li>
						<a tabindex="-1" href="extras-charts.html"><span class="icon-bar-chart"></span>Charts</a>
					</li>
					<li>
						<a tabindex="-1" href="extras-widgets.html"><span class="icon-star"></span>Widgets</a>
					</li>
				</ul>
			</li>
			<li class="lp-dropdown">
				<a href="#" class="lp-dropdown-toggle" id="pages-dropdown"><span class="icon-file-alt"></span>Pages</a>
				<ul class="lp-dropdown-menu simple" data-dropdown-owner="pages-dropdown">
					<li>
						<a tabindex="-1" href="index.html"><i class="icon-signin"></i>&nbsp;&nbsp;Sign In</a>
					</li>
					<li>
						<a tabindex="-1" href="pages-signup.html"><i class="icon-check"></i>&nbsp;&nbsp;Sign Up</a>
					</li>
					<li>
						<a tabindex="-1" href="pages-messages.html"><i class="icon-envelope-alt"></i>&nbsp;&nbsp;Messages</a>
					</li>
					<li>
						<a tabindex="-1" href="pages-stream.html"><i class="icon-leaf"></i>&nbsp;&nbsp;Stream</a>
					</li>
					<li>
						<a tabindex="-1" href="pages-pricing.html"><i class="icon-money"></i>&nbsp;&nbsp;Pricing</a>
					</li>
					<li>
						<a tabindex="-1" href="pages-invoice.html"><i class="icon-pencil"></i>&nbsp;&nbsp;Invoice</a>
					</li>
					<li>
						<a tabindex="-1" href="pages-map.html"><i class="icon-map-marker"></i>&nbsp;&nbsp;Full page map</a>
					</li>
					<li>
						<a tabindex="-1" href="pages-error-404.html"><i class="icon-unlink"></i>&nbsp;&nbsp;Error 404</a>
					</li>
					<li>
						<a tabindex="-1" href="pages-error-500.html"><i class="icon-bug"></i>&nbsp;&nbsp;Error 500</a>
					</li>
					<li>
						<a tabindex="-1" href="pages-blank.html"><i class="icon-bookmark-empty"></i>&nbsp;&nbsp;Blank page</a>
					</li>
				</ul>
			</li>
			-->
		</ul>
	</div>
	<div class="icon-caret-down"></div>
	<div class="icon-caret-up"></div>
</nav>
<?php } ?>