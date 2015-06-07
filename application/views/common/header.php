<?php
	// view_type
	$view_type = (isset($view_type)) ? $view_type : 'normal';
	
	$user = $this->user_model->get_session();
	$user_type = $this->user_type_model->get_by_id(array( 'id' => $user['user_type_id'] ));
	$mail_unread_count = $this->mail_model->get_count(array( 'count_type' => 'unread_count', 'user_id' => $user['user_id'], 'user_type_id' => $user['user_type_id'] ));
?>
<style>
	@media screen and (max-width: 1000px) {
		.nav-collapse ul.nav:first-child { display: none; }
	}
</style>
<script type="text/javascript">demoSetBodyLayout();</script>

<header class="navbar navbar-fixed-top" id="main-navbar">
	<div class="navbar-inner">
		<div class="container">
			<a class="logo" href="#" style="padding: 0 25px;">&nbsp;</a>
			<a class="btn nav-button collapsed" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-reorder"></span>
			</a>
			
			<div class="nav-collapse collapse">
				<ul class="nav">
					<?php if ($view_type == 'stand-alone') { ?>
					<li class="active"><a href="<?php echo base_url(); ?>">Home</a></li>
					<li class="divider-vertical"></li>
					<?php } else { ?>
					<li class="active"><a href="<?php echo base_url(); ?>">Home</a></li>
					<li><a>Jafaria Education Center - Student Dashboard</a></li>
					<li class="divider-vertical"></li>
					<?php } ?>
				</ul>
				
				<ul class="nav pull-right">
					<?php if ($view_type == 'stand-alone') { ?>
					<li class="active"><a href="<?php echo base_url('home/logout'); ?>">Sign Out</a></li>
					<?php } else { ?>
					<li><a target="_blank" href="http://www.jafariaschool.org">Jafaria Education Center</a></li>
					<li class="separator"></li>
					<li><a>Anaheim, CA</a></li>
					<li class="separator"></li>
					<li>
						<ul class="messages">
							<li><a href="<?php echo base_url('email'); ?>"><i class="icon-envelope"></i> <?php echo $mail_unread_count; ?><span class="responsive-text"> new messages</span></a></li>
						</ul>
					</li>
					<li class="separator"></li>
					
					<!-- theme issue : view for width less than 768px -->
					<?php if ($user['user_type_id'] == USER_TYPE_PARENT) { ?>
						<?php foreach ($user['array_student'] as $row) { ?>
						<?php $class_active = ($user['student_id'] == $row['s_id']) ? 'active' : ''; ?>
						<li class="visible-column-small"><a class="cursor change-student <?php echo $class_active; ?>" data-student_id="<?php echo $row['s_id']; ?>">- <?php echo $row['s_name']; ?></a></li>
						<?php } ?>
					<?php } ?>
					<li class="visible-column-small"><a href="<?php echo base_url('home/logout'); ?>">Sign Out</a></li>
					
					<!-- theme issue : view for width more than 768px -->
					<li class="dropdown">
						<a href="#" class="dropdown-toggle usermenu" data-toggle="dropdown">
							<img alt="Avatar" src="<?php echo base_url('static/images/avatar.png'); ?>">
							<span>&nbsp; <?php echo $user_type['title']; ?></span>
						</a>
						<ul class="dropdown-menu">
							<?php if ($user['user_type_id'] == USER_TYPE_PARENT) { ?>
								<?php foreach ($user['array_student'] as $row) { ?>
								<?php $class_active = ($user['student_id'] == $row['s_id']) ? 'active' : ''; ?>
								<li><a class="cursor change-student <?php echo $class_active; ?>" data-student_id="<?php echo $row['s_id']; ?>"><?php echo $row['s_name']; ?></a></li>
								<?php } ?>
							<?php } else { ?>
								<li><a><?php echo $user['user_uname']; ?></a></li>
							<?php } ?>
							
							<?php if ($user['user_type_id'] == USER_TYPE_ADMINISTRATOR) { ?>
							<li class="divider"></li>
							<!-- <li><a class="cursor btn-reset-task">Reset Task</a></li> -->
							<!-- <li><a class="cursor btn-reset-attendance">Reset Attendance</a></li> -->
							<li><a href="<?php echo base_url('setup_account'); ?>">Setup Account</a></li>
							<?php } ?>
							
							<li class="divider"></li>
							<?php if ($user['user_type_id'] == USER_TYPE_PARENT) { ?>
							<li><a class="cursor btn-contact-detail">Contact Details</a></li>
							<?php } ?>
							<li><a class="cursor btn-update-password">Change Password</a></li>
							<li><a href="<?php echo base_url('home/logout'); ?>">Sign Out</a></li>
						</ul>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</header>