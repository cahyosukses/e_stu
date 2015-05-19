<?php
	// array student
	$array_student = $this->student_model->get_class_rank($_POST);
	
	// aqaid class
	$aqaid_exist = false;
	foreach ($array_student as $key => $row) {
		if (isset($row['aqaid_summary'])) {
			$aqaid_exist = true;
			break;
		}
	}
?>

<table class="table table-bordered dataTable">
	<thead>
		<tr>
			<th class="center">Rank</th>
			<th class="center">Name</th>
			<th class="center">Fiqh</th>
			<th class="center">Akhlaq</th>
			<th class="center">Tareekh</th>
			<?php if ($aqaid_exist) { ?>
			<th class="center">Aqaid</th>
			<?php } ?>
			<th class="center">Attendance</th>
			<th class="center">Average</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($array_student as $row) { ?>
		<tr>
			<td class="center"><?php echo $row['rank_no']; ?></td>
			<td><?php echo $row['name']; ?></td>
			<td class="center"><?php echo $row['figh_summary']; ?></td>
			<td class="center"><?php echo $row['akhlaq_summary']; ?></td>
			<td class="center"><?php echo $row['tareekh_summary']; ?></td>
			<?php if ($aqaid_exist) { ?>
			<td class="center"><?php echo $row['aqaid_summary']; ?></td>
			<?php } ?>
			<td class="center"><?php echo $row['attendance_summary']; ?></td>
			<td class="center"><?php echo $row['rank_average']; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>