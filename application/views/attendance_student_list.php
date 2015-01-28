<?php
	$array_student = $this->attendance_student_model->get_array(array( 'sort' => '[{"property":"s_name","direction":"ASC"}]', 'attendance_id' => $_POST['attendance_id'], 'limit' => 250 ));
?>

<table class="table table-bordered">
	<thead>
		<tr>
			<th style="width: 75%;">Student Name</th>
			<th class="center" style="width: 25%;">Control</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($array_student as $row) { ?>
		<tr>
			<td><?php echo $row['s_name']; ?></td>
			<td class="center">
				<div style="margin: 0px auto; width: 80px;">
					<div class="toggle-adminflare">
						<input type="hidden" class="award" data-attendance_student_id="<?php echo $row['id']; ?>" value="<?php echo $row['award']; ?>" />
						<div class="normal-toggle-button toggle"></div>
						<span class="hide"><?php echo json_encode($row); ?></span>
					</div>
				</div>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>