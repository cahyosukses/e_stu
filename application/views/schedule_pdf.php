<?php
	// get schedule
	$user_id = (isset($this->uri->segments[3])) ? $this->uri->segments[3] : 0;
	$param_schedule = array(
		'parent_not_in' => 0,
		'sort' => '[{"property":"user.user_display","direction":"ASC"},{"property":"schedule.time_frame","direction":"ASC"}]',
		'limit' => 100
	);
	if (!empty($user_id)) {
		$param_schedule['user_id'] = $user_id;
	}
	$array_schedule = $this->schedule_model->get_array($param_schedule);
	
	// make teacher as key
	$array_teacher = array();
	foreach ($array_schedule as $row) {
		$array_teacher[$row['user_display']][] = $row;
	}
?>

<style>
.center { text-align: center; }

table { font-family: sans-serif; border: 1px solid #000000; border-collapse: collapse; }
th, td { border: 1px solid #000000; vertical-align: middle; }
</style>

<?php if (count($array_teacher) > 0) { ?>
	<?php $counter = 0; ?>
	<?php foreach ($array_teacher as $teacher_name => $array) { ?>
		<?php if ($counter > 0) { ?>
		<pagebreak />
		<?php } ?>
		
		<h3 style="text-align: center;">Schedule - <?php echo $teacher_name; ?></h3>
		
		<table class="table" style="width: 100%; font-size: 12px;">
			<thead>
				<tr>
					<th style="width: 20%;" class="center">Time Frame</th>
					<th style="width: 20%;" class="center">Father Name</th>
					<th style="width: 20%;" class="center">Mother Name</th>
					<th style="width: 40%;">Student</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($array as $row) { ?>
					<tr>
						<td class="center"><?php echo $row['time_frame_title']; ?></td>
						<td class="center"><?php echo $row['father_name']; ?></td>
						<td class="center"><?php echo $row['mother_name']; ?></td>
						<td><?php echo $row['student_name']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		
		<?php $counter++; ?>
	<?php } ?>
<?php } else { ?>
	<h3 style="text-align: center;">Sorry, there is no schedule available</h3>
<?php } ?>