<?php
	// data
	$class_level_id = (isset($_GET['class_level_id'])) ? $_GET['class_level_id'] : 0;
	if (empty($class_level_id)) {
		exit;
	}
	
	// user
	$user = $this->user_model->get_session();
	
	// page
	$array_page['user'] = $user;
	$array_page['class_level_id'] = $class_level_id;
	$array_page['USER_TYPE_PARENT'] = USER_TYPE_PARENT;
	$array_page['USER_TYPE_TEACHER'] = USER_TYPE_TEACHER;
	$array_page['USER_TYPE_ADMINISTRATOR'] = USER_TYPE_ADMINISTRATOR;
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	<div class="hide">
		<div class="cnt-page"><?php echo json_encode($array_page); ?></div>
	</div>
	
	<!--
	<div id="modal-student" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-studentLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="task_id" value="" />
			<input type="hidden" name="student_id" value="" />
			<input type="hidden" name="action" value="task_class_add" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-studentLabel">Add Student</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Student Name</label>
					<div class="controls cnt-typeahead">
						<input type="text" name="parent_text" class="span6 typeahead-student" placeholder="Select a Student" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Add" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	-->
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Finalize</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Finalize</h4>
					<table class="table table-striped" id="grade-finalize-grid">
						<thead>
							<tr>
								<th style="width: 15%;">Task Type</th>
								<th style="width: 15%;">Assign By</th>
								<th>Title</th>
								<th style="width: 15%;">Due Date</th>
								<th style="width: 15%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</section>
		
		<?php echo $this->load->view( 'common/footer' ); ?>
	</section>
</body>

<script type="text/javascript">
$(document).ready(function() {
	var page = {
		init: function() {
			var raw = $('.cnt-page').html();
			eval('var data = ' + raw);
			page.data = data;
		}
	}
	page.init();
	
	/*
	
	// grid
	var param = {
		id: 'grade-finalize-grid',
		source: 'task/grid', aaSorting: [[ 2, "DESC" ]],
		column: [ { }, { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		fnServerParams: function(aoData) {
			var data = page.get_filter();
			aoData.push( { name: 'grid_type', value: 'teacher' } );
			
			// check
			data.quran_level_id = (data.quran_level_id == '') ? 0 : data.quran_level_id;
			data.class_level_id = (data.class_level_id == '') ? 0 : data.class_level_id;
			
			if (data.quran_level_id == 0 && data.class_level_id == 0) {
				aoData.push( { name: 'quran_level_id', value: 0 } );
				aoData.push( { name: 'class_level_id', value: 0 } );
			} else {
				aoData.push( { name: 'class_type_id', value: data.class_type } );
				
				if (data.quran_level_id != 0) {
					aoData.push( { name: 'quran_level_id', value: data.quran_level_id } );
				}
				if (data.class_level_id != 0) {
					aoData.push( { name: 'class_level_id', value: data.class_level_id } );
				}
			}
		},
		init: function() {
			$('#grade-finalize-grid_length').prepend('<div class="btn-group" style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-task-add" value="Add" /></div>');
		},
		callback: function() {
			$('#grade-finalize-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				console.log(record);
				// show modal
				Func.populate({ cnt: '#form-task', record: record });
				page.attachment.populate({ raw_attachment: record.attachment });
				page.show_form_task();
			});
			
			$('#grade-finalize-grid .btn-update-score').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				page.init_task_class(record);
			});
			
			$('#grade-finalize-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', id: record.id },
					url: web.base + 'task/action', callback: function() { dt.reload(); }
				});
			});
		}
	}
	var dt = Func.datatable(param);
	
	// form task
	$('.btn-task-add').click(function() {
		if (page.is_valid()) {
			page.show_form_task();
			
			// reset form
			$('#form-task')[0].reset();
			$('#form-task [name="id"]').val(0);
			$('#form-task .cnt-attachment ul').html('');
		} else {
			$.notify("Please select Quran or Class Level", "error");
		}
	});
	$('.btn-show-grid').click(function() {
		page.show_grid();
	});
	$('#form-task').validate({
		rules: {
			task_type_id: { required: true },
			class_level_id: { required: true },
			title: { required: true },
			content: { required: true },
			due_date: { required: true }
		}
	});
	$('#form-task').submit(function(e) {
		e.preventDefault();
		if (! $('#form-task').valid()) {
			return false;
		}
		
		// filter
		var filter = page.get_filter();
		
		// collect param
		var param = Func.form.get_value('form-task');
		param.class_type_id = filter.class_type;
		param.quran_level_id = filter.quran_level_id;
		param.class_level_id = filter.class_level_id;
		
		// ajax request
		$('#form-task [type="submit"]').attr('disabled', true);
		Func.form.submit({
			url: web.base + 'task/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-task')[0].reset();
				$('#form-task [type="submit"]').attr('disabled', false);
			}
		});
	});
	
	// form task grade
	$('.box-task-grade form').submit(function(e) {
		e.preventDefault();
		
		// ajax request
		var param = Func.form.get_value('.box-task-grade');
		
		// collect value
		param.array_grade = [];
		for (var i = 0; i < $('.box-task-grade .task-class-value').length; i++) {
			var value = $('.box-task-grade .task-class-value').eq(i).val();
			var task_class_id = $('.box-task-grade .task-class-value').eq(i).data('task_class_id');
			param.array_grade.push(task_class_id + ',' + value);
		}
		
		Func.form.submit({
			url: web.base + 'task/action',
			param: param,
			callback: function() {
				dt.reload();
			}
		});
	});
	
	// form modal student
	$('#modal-student form').validate({
		rules: {
			student_id: { required: true },
			parent_text: { required: true }
		}
	});
	$('#modal-student form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-student form').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('modal-student form');
		Func.form.submit({
			url: web.base + 'task/action',
			param: param,
			callback: function(result) {
				page.init_task_class({ id: param.task_id });
				$('#modal-student').modal('hide');
			}
		});
	});
	
	/*	*/
});
</script>

</html>