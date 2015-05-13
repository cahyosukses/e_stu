<?php
	$array_student = $this->student_model->get_rank($_POST);
?>

<table class="table table-bordered dataTable">
	<thead>
		<tr>
			<th class="center">Rank</th>
			<th class="center">Name</th>
			<th class="center">Quran</th>
			<th class="center">Fiqh</th>
			<th class="center">Akhlaq</th>
			<th class="center">Tareekh</th>
			<th class="center">Aqaid</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($array_student as $row) { ?>
		<tr>
			<td class="center"><?php echo $row['rank_no']; ?></td>
			<td><?php echo $row['name']; ?></td>
			<td class="center"><?php echo $row['quran_summary']; ?></td>
			<td class="center"><?php echo $row['figh_summary']; ?></td>
			<td class="center"><?php echo $row['akhlaq_summary']; ?></td>
			<td class="center"><?php echo $row['tareekh_summary']; ?></td>
			<td class="center"><?php echo (isset($row['aqaid_summary'])) ? $row['aqaid_summary'] : '-'; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>