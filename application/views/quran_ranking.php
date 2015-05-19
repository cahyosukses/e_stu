<?php
	// array student
	$array_student = $this->student_model->get_quran_rank($_POST);
?>

<table class="table table-bordered dataTable">
	<thead>
		<tr>
			<th class="center">Rank</th>
			<th class="center">Name</th>
			<th class="center">Quran</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($array_student as $row) { ?>
		<tr>
			<td class="center"><?php echo $row['rank_no']; ?></td>
			<td><?php echo $row['name']; ?></td>
			<td class="center"><?php echo $row['quran_summary']; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>