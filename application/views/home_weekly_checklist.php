<?php
	// get user
	$user = $this->user_model->get_session();
	
	// array dashboard
	$param_weekly['student_id'] = $user['student_id'];
	$param_weekly['start_date'] = (!empty($_POST['start_date'])) ? $_POST['start_date'] : date("Y-m-d");
	$array_weekly = $this->weekly_checklist_model->get_dashboard($param_weekly);
?>

<input type="hidden" name="start_date" value="<?php echo $param_weekly['start_date']; ?>" />

<table class="table table-bordered">
	<thead>
		<tr>
			<th style="width: 15%;" class="center">Date</th>
			<th style="width: 15%;" class="center column-small">Days</th>
			<th style="width: 15%;" class="center column-small">Reading Duration</th>
			<th style="width: 40%;" class="column-small">Comment</th>
			<th style="width: 15%;" class="center">Control</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($array_weekly as $row) { ?>
			<tr>
				<td class="center"><?php echo $row['date_check_swap']; ?></td>
				<td class="center column-small"><?php echo $row['day_info']; ?></td>
				<td class="center column-small"><?php echo $row['duration']; ?></td>
				<td class="column-small"><?php echo $row['content']; ?></td>
				<td class="center">
					<span class="cursor-font-awesome icon-pencil btn-edit" data-original-title="Edit"></span>
					<span class="hide"><?php echo json_encode($row); ?></span>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>