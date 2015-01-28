<?php
	$task = $this->task_model->get_by_id(array( 'id' => $_POST['id'] ));
	$array_student = $this->task_class_model->get_array(array( 'task_id' => $task['id'], 'limit' => 250 ));
?>

<h4 class="center-title"><?php echo $task['task_type_name'].' - '.$task['title']; ?></h4>
<h4 class="center-title"><?php echo get_format_date($task['due_date'], array( 'date_format' => 'j F Y' )); ?></h4>

<div style="padding: 0 0 10px 0;">
	<input type="button" value="Add Student" class="btn btn-task-class-add">
</div>

<table class="table table-bordered">
	<thead>
		<tr>
			<th style="width: 50%;">Student Name</th>
			<th class="center" style="width: 40%;">Grade (Percent)</th>
			<th class="center hide" style="width: 10%;">Control</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($array_student as $row) { ?>
		<tr>
			<td><?php echo $row['s_name']; ?></td>
			<td class="center"><input type="text" class="span2 center task-class-value" data-task_class_id="<?php echo $row['id']; ?>" value="<?php echo $row['grade']; ?>" /></td>
			<td class="center hide">
				<span class="cursor-font-awesome icon-trash btn-delete"></span>
				<span class="hide"><?php echo json_encode($row); ?></span>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

