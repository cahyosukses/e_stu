<?php
	// get master
	$user = $this->user_model->get_session();
	$parent = $this->parents_model->get_by_id(array( 'p_id' => $user['p_id'] ));
	$student = $this->student_model->get_by_id(array( 's_id' => $user['student_id'] ));
	
	// array dashboard
	$param_weekly = array(
		'student_id' => $user['student_id'],
		'start_date' => $this->uri->segments[3]
	);
	$array_weekly = $this->weekly_checklist_model->get_dashboard($param_weekly);
?>

<style>
.center { text-align: center; }

table { font-family: sans-serif; border: 1px solid #000000; border-collapse: collapse; }
th, td { border: 1px solid #000000; vertical-align: middle; }
</style>

<h3>Weekly Checklist - <?php echo $student['s_name']; ?></h3>

<table class="table" style="width: 100%;">
	<thead>
		<tr>
			<th style="width: 20%;" class="center">Date</th>
			<th style="width: 20%;" class="center">Days</th>
			<th style="width: 15%;" class="center">Reading Duration</th>
			<th style="width: 45%;">Comment</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($array_weekly as $row) { ?>
			<tr>
				<td class="center"><?php echo $row['date_check_swap']; ?></td>
				<td class="center"><?php echo $row['day_info']; ?></td>
				<td class="center"><?php echo $row['duration']; ?></td>
				<td><?php echo $row['content']; ?></td>
			</tr>
		<?php } ?>
	</tbody>
</table>

<div style="width: 600px; padding: 50px 0 0 400px; text-align: center;">
	<div><?php echo date("l, m-d-Y"); ?></div>
	<div>
		<?php if (empty($parent['p_sign_image'])) { ?>
			&nbsp;
		<?php } else { ?>
			<img src="<?php echo $parent['p_sign_image_link']; ?>" style="width: 200px;" />
		<?php } ?>
	</div>
	<div><?php echo $parent['p_father_name']; ?></div>
</div>