<?php
	// user
	$user = $this->user_model->get_session();
	
	// master
	$array_class_type = $this->teacher_class_model->get_class_teacher(array( 'user_id' => $user['user_id'] ));
	$array_task_type_all = $this->task_type_model->get_array();
	$array_task_type = $this->task_type_model->get_array(array( 'id_in' => '1,2,3,4' ));
	$array_quran_level = $this->quran_level_model->get_teacher_array(array( 'user_id' => $user['user_id'] ));
	$array_fiqh_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'fiqh' => 1 ));
	$array_akhlag_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'akhlaq' => 1 ));
	$array_taareekh_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'taareekh' => 1 ));
	$array_aqaid_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'aqaid' => 1 ));
	
	// flash message
	$message = get_flash_message();
	
	// page
	$array_page['user'] = $user;
	$array_page['USER_TYPE_PARENT'] = USER_TYPE_PARENT;
	$array_page['USER_TYPE_TEACHER'] = USER_TYPE_TEACHER;
	$array_page['USER_TYPE_ADMINISTRATOR'] = USER_TYPE_ADMINISTRATOR;
	if (!empty($_GET['class_type_id'])) {
		$array_page['class_type_id'] = $_GET['class_type_id'];
	}
	if (!empty($_GET['quran_level_id'])) {
		$array_page['quran_level_id'] = $_GET['quran_level_id'];
	}
	if (!empty($_GET['class_level_id'])) {
		$array_page['class_level_id'] = $_GET['class_level_id'];
	}
	if (!empty($message)) {
		$array_page['message'] = $message;
	}
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	<div class="hide">
		<div class="cnt-page"><?php echo json_encode($array_page); ?></div>
		<iframe name="iframe_attachment" src="<?php echo base_url('upload?callback_name=add_attachment&file_rename=0'); ?>"></iframe>
	</div>
	
	<div id="modal-weight" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-weightLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="action" value="task_type_update" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-weightLabel">Grade Weight</h3>
			</div>
			<div class="modal-body">
				<?php foreach ($array_task_type_all as $row) { ?>
				<div class="control-group">
					<label class="control-label"><?php echo $row['name']; ?></label>
					<div class="controls">
						<input type="text" class="span1 center weight-score" data-id="<?php echo $row['id']; ?>" value="<?php echo $row['weight']; ?>" /> %
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Save" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
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
	
	<div id="modal-task-detail" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-task-detailLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-task-detailLabel">Task Detail</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Class Subject</label>
					<div class="controls">
						<input type="text" name="class_type_name" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Assign By</label>
					<div class="controls">
						<input type="text" name="user_display" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Task Type</label>
					<div class="controls">
						<input type="text" name="task_type_name" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Task Title</label>
					<div class="controls">
						<input type="text" name="task_title" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Due Date</label>
					<div class="controls">
						<input type="text" name="task_due_date_swap" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Grade</label>
					<div class="controls">
						<input type="text" name="grade" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Task</h3>
			
			<?php if ($user['user_type_id'] == USER_TYPE_TEACHER || $user['user_type_id'] == USER_TYPE_PRINCIPAL) { ?>
			<div id="cnt-class-filter" class="box-grid box">
				<div class="row-fluid">
					<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Type :</div>
					<div class="span2">
						<select name="class_type" style="width: 100%;">
							<?php echo ShowOption(array( 'Array' => $array_class_type, 'WithEmptySelect' => 0 )); ?>
						</select>
					</div>
					<div class="span2">&nbsp;</div>
					<div class="span5 center btn-group">
						<input type="button" class="btn btn-task-add" value="Add" />
						<input type="button" class="btn btn-grade-weight" value="Grade Weights" />
						<input type="button" class="btn btn-finalize" value="Finalize" />
					</div>
				</div>
				<div class="row-fluid">
					<div class="class-level cnt-quran-level">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Quran Level :</div>
						<div class="span2">
							<select name="quran_level_id" style="width: 100%;">
								<?php echo ShowOption(array( 'Array' => $array_quran_level, 'ArrayTitle' => 'name' )); ?>
							</select>
						</div>
					</div>
					<div class="class-level cnt-fiqh-level hide">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Level :</div>
						<div class="span2">
							<select name="fiqh_level_id" style="width: 100%;">
								<?php echo ShowOption(array( 'Array' => $array_fiqh_level, 'ArrayTitle' => 'name' )); ?>
							</select>
						</div>
					</div>
					<div class="class-level cnt-akhlaq-level hide">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Level :</div>
						<div class="span2">
							<select name="akhlaq_level_id" style="width: 100%;">
								<?php echo ShowOption(array( 'Array' => $array_akhlag_level, 'ArrayTitle' => 'name' )); ?>
							</select>
						</div>
					</div>
					<div class="class-level cnt-taareekh-level hide">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Level :</div>
						<div class="span2">
							<select name="taareekh_level_id" style="width: 100%;">
								<?php echo ShowOption(array( 'Array' => $array_taareekh_level, 'ArrayTitle' => 'name' )); ?>
							</select>
						</div>
					</div>
					<div class="class-level cnt-aqaid-level hide">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Level :</div>
						<div class="span2">
							<select name="aqaid_level_id" style="width: 100%;">
								<?php echo ShowOption(array( 'Array' => $array_aqaid_level, 'ArrayTitle' => 'name' )); ?>
							</select>
						</div>
					</div>
				</div>
			</div>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Task List</h4>
					<table class="table table-striped" id="task-grid">
						<thead>
							<tr>
								<th style="width: 15%;">Task Type</th>
								<th style="width: 15%;">Assign By</th>
								<th>Title</th>
								<th style="width: 10%;">Complete</th>
								<th style="width: 10%;">Due Date</th>
								<th style="width: 10%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-form-task hide">
				<div class="box">
					<h4 class="center-title">Task Form</h4>
					<form id="form-task" class="form-horizontal" style="margin: 0px;">
						<input type="hidden" name="id" value="0" />
						<input type="hidden" name="action" value="update" />
						
						<div class="control-group">
							<label class="control-label">Task Type</label>
							<div class="controls">
								<select name="task_type_id" class="span6">
									<?php echo ShowOption(array( 'Array' => $array_task_type, 'ArrayTitle' => 'name' )); ?>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Title</label>
							<div class="controls"><input type="text" name="title" class="span8" placeholder="Title" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Content</label>
							<div class="controls"><textarea name="content" class="span8" style="height: 100px;" placeholder="Content"></textarea></div>
						</div>
						<div class="control-group">
							<label class="control-label" style="padding-top: 9px;">Due Date</label>
							<div class="controls">
								<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>">
									<input type="text" name="due_date" class="input-small input-datepicker" size="16" />
									<span class="add-on"><i class="icon-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="control-group cnt-attachment">
							<label class="control-label">Attachment</label>
							<div class="controls">
								<input type="button" class="btn btn-attachment" value="Add Attachment" />
								<ul style="margin: 0px 0px 0px 13px; padding: 10px 0 0 0;"></ul>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Is Complete</label>
							<div class="controls"><input type="checkbox" name="is_complete" value="1" /></div>
						</div>
						<div class="control-group cnt-send-mail">
							<label class="control-label">Send Email</label>
							<div class="controls"><input type="checkbox" name="send_mail" value="1" /></div>
						</div>
						<div class="control-group">
							<div class="controls">
								<input type="submit" class="btn btn-primary" value="Save" />
								<input type="button" class="btn btn-show-grid" value="Cancel" />
							</div>
						</div>
					</form>
				</div>
			</div>
			
			<div class="box-task-grade hide">
				<div class="box">
					<form class="form-horizontal">
						<input type="hidden" name="id" value="0" />
						<input type="hidden" name="task_id" value="0" />
						<input type="hidden" name="action" value="task_class_update" />
						
						<div class="list-student"></div>
						
						<div class="center">
							<input type="submit" value="Save" class="btn btn-primary">
							<input type="button" value="Back" class="btn btn-show-grid">
						</div>
					</form>
				</div>
			</div>
			<?php } else if ($user['user_type_id'] == USER_TYPE_PARENT) { ?>
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Task List</h4>
					<table class="table table-striped" id="task-grid">
						<thead>
							<tr>
								<th style="width: 15%;">Class</th>
								<th style="width: 15%;">Task Type</th>
								<th>Title</th>
								<th style="width: 15%;">Due Date</th>
								<th style="width: 15%;">Grade</th>
								<th style="width: 15%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<?php } ?>
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
		},
		get_filter: function() {
			var temp = Func.form.get_value('cnt-class-filter');
			
			// set value
			var result = {
				class_type: temp.class_type,
				quran_level_id: 0,
				class_level_id: 0
			};
			if (temp.class_type == 1) {
				result.quran_level_id = temp.quran_level_id;
			} else if (temp.class_type == 2) {
				result.class_level_id = temp.fiqh_level_id;
			} else if (temp.class_type == 3) {
				result.class_level_id = temp.akhlaq_level_id;
			} else if (temp.class_type == 4) {
				result.class_level_id = temp.taareekh_level_id;
			} else if (temp.class_type == 5) {
				result.class_level_id = temp.aqaid_level_id;
			}
			
			return result;
		},
		is_valid: function() {
			var filter = page.get_filter();
			filter.quran_level_id = (filter.quran_level_id == '') ? 0 : filter.quran_level_id;
			filter.class_level_id = (filter.class_level_id == '') ? 0 : filter.class_level_id;
			
			var result = true;
			if (filter.quran_level_id == 0 && filter.class_level_id == 0) {
				result = false;
			}
			
			return result;
		},
		show_grid: function() {
			$('.box-grid').show();
			$('.box-form-task').hide();
			$('.box-task-grade').hide();
		},
		show_form_task: function() {
			$('.box-grid').hide();
			$('.box-form-task').show();
			$('.box-task-grade').hide();
			var form_param = Func.form.get_value('form-task');
			
			// email
			if (form_param.id == 0) {
				$('#form-task .cnt-send-mail').show();
			} else {
				$('#form-task .cnt-send-mail').hide();
			}
		},
		show_form_task_grade: function() {
			$('.box-grid').hide();
			$('.box-form-task').hide();
			$('.box-task-grade').show();
		},
		init_task_class: function(p) {
			// set task id
			$('.box-task-grade [name="task_id"]').val(p.id);
			
			// load grid
			Func.ajax({
				is_json: false,
				url: web.base + 'task/get_view',
				param: { action: 'task_grade', id: p.id },
				callback: function(result) {
					$('.box-task-grade .list-student').html(result);
					page.show_form_task_grade();
					
					// init button add
					$('.box-task-grade .btn-task-class-add').click(function() {
						// set data
						$('#modal-student form')[0].reset();
						var task_id = $('.box-task-grade [name="task_id"]').val();
						$('#modal-student [name="task_id"]').val(task_id);
						
						// show modal
						$('#modal-student').modal();
					});
					
					// init button delete
					$('.box-task-grade .btn-delete').click(function() {
						var dom_row = $(this).parents('tr');
						var raw_record = $(this).siblings('.hide').text();
						eval('var record = ' + raw_record);
						
						Func.form.submit({
							url: web.base + 'task/action',
							param: { action: 'task_class_delete', id: record.id },
							callback: function(result) {
								dom_row.remove();
							}
						});
					});
				}
			});
		},
		attachment: {
			append: function(p) {
				// append
				var raw_html = '<li>' + p.file_only + ' <a class="cursor btn-remove">(remove)</a> <input type="hidden" name="array_attachment[]" value=\'' + Func.object_to_json(p) + '\' /></li>';
				$('#form-task .cnt-attachment ul').append(raw_html);
				
				// init
				$('#form-task .btn-remove').last().click(function() {
					$(this).parent('li').remove();
				});
			},
			populate: function(p) {
				if (p.raw_attachment == '') {
					return;
				}
				
				// prepare data
				eval('var array_attachment = ' + p.raw_attachment);
				
				// generate html
				$('#form-task .cnt-attachment ul').html('');
				for (var i = 0; i < array_attachment.length; i++) {
					page.attachment.append(array_attachment[i]);
				}
			}
		},
		callback: function() {
			// set filter
			if (page.data.class_type_id != null) {
				$('#cnt-class-filter [name="class_type"]').val(page.data.class_type_id);
				$('#cnt-class-filter [name="class_type"]').change();
			}
			if (page.data.class_level_id != null) {
				$('#cnt-class-filter .class-level select:visible').val(page.data.class_level_id);
				$('#cnt-class-filter .class-level select:visible').change();
			}
			if (page.data.quran_level_id != null) {
				$('#cnt-class-filter .class-level select:visible').val(page.data.quran_level_id);
				$('#cnt-class-filter .class-level select:visible').change();
			}
			
			// page.data.quran_level_id
			
			// set message
			if (page.data.message != null) {
				$.notify(page.data.message, "success");
			}
		}
	}
	page.init();
	
	// upload
	$('#form-task .btn-attachment').click(function() { window.iframe_attachment.browse() });
	add_attachment = function(p) {
		page.attachment.append(p);
	}
	
	// selector
	if (page.data.USER_TYPE_TEACHER == page.data.user.user_type_id || page.data.USER_TYPE_PRINCIPAL == page.data.user.user_type_id) {
		// grid
		var param = {
			id: 'task-grid',
			source: 'task/grid', aaSorting: [[ 2, "DESC" ]],
			column: [ { sClass: 'column-small' }, { sClass: 'column-small' }, { }, { sClass: 'column-small center' }, { sClass: 'column-small center' }, { bSortable: false, sClass: 'center' } ],
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
			callback: function() {
				$('#task-grid .btn-edit').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					
					// show modal
					Func.populate({ cnt: '#form-task', record: record });
					page.attachment.populate({ raw_attachment: record.attachment });
					page.show_form_task();
				});
				
				$('#task-grid .btn-update-score').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					page.init_task_class(record);
				});
				
				$('#task-grid .btn-delete').click(function() {
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
		
		// filter
		$('#cnt-class-filter [name="class_type"]').change(function() {
			var value = $('[name="class_type"]').val();
			
			// show hide
			$('#cnt-class-filter .class-level').hide();
			if (value == 1) {
				$('.cnt-quran-level').show();
			} else if (value == 2) {
				$('.cnt-fiqh-level').show();
			} else if (value == 3) {
				$('.cnt-akhlaq-level').show();
			} else if (value == 4) {
				$('.cnt-taareekh-level').show();
			} else if (value == 5) {
				$('.cnt-aqaid-level').show();
			}
		});
		$('#cnt-class-filter .class-level select').change(function() {
			dt.reload();
		});
		$('#cnt-class-filter [name="class_type"]').change();
		
		// autocomplete
		var student_store = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			prefetch: web.base + 'typeahead?action=student',
			remote: web.base + 'typeahead?action=student&namelike=%QUERY'
		});
		student_store.initialize();
		var student = $('.typeahead-student').typeahead(null, {
			name: 'student',
			displayKey: 's_name',
			source: student_store.ttAdapter(),
			templates: {
				empty: [
					'<div class="empty-message">',
					'no result found.',
					'</div>'
				].join('\n'),
				suggestion: Handlebars.compile('<p><strong>{{s_name}}</strong></p>')
			}
		});
		student.on('typeahead:selected', function(evt, data) {
			$('#modal-student [name="student_id"]').val(data.s_id);
		});
		
		// form task
		$('.btn-task-add').click(function() {
			if (page.is_valid()) {
				// reset form
				$('#form-task')[0].reset();
				$('#form-task [name="id"]').val(0);
				$('#form-task [name="send_mail"]').prop('checked', true);
				$('#form-task .cnt-attachment ul').html('');
				
				// show form
				page.show_form_task();
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
		
		// form modal task weight
		$('.btn-grade-weight').click(function() {
			Func.ajax({
				url: web.base + 'task/action',
				param: { action: 'task_type_info' },
				callback: function(result) {
					// populate task weight
					for (var i = 0; i < result.array_task_type.length; i++) {
						$('#modal-weight [data-id="' + result.array_task_type[i].id + '"]').val(result.array_task_type[i].weight);
					}
					
					// show modal
					$('#modal-weight').modal();
				}
			});
		});
		$('#modal-weight form').submit(function(e) {
			e.preventDefault();
			
			// get param
			var param = Func.form.get_value('modal-weight form');
			
			// get weight grade
			var weight_total = 0;
			param.array_weight = [];
			for (var i = 0; i < $('#modal-weight .weight-score').length; i++) {
				// set weight
				var id = $('#modal-weight .weight-score').eq(i).data('id');
				var weight = parseInt($('#modal-weight .weight-score').eq(i).val(), 10);
				param.array_weight.push(id + ',' + weight);
				
				// get total
				weight_total += weight;
			}
			
			// validate
			if (weight_total != 100) {
				$.notify('Total weight must 100.', "error");
				return;
			}
			
			// ajax request
			Func.form.submit({
				url: web.base + 'task/action',
				param: param,
				callback: function(result) {
					$('#modal-weight').modal('hide');
				}
			});
		});
		
		// form grade finalize
		$('.btn-finalize').click(function() {
			if (page.is_valid()) {
				var link_grade = web.base + 'grade_finalize?class_type_id=' + page.get_filter().class_type;
				link_grade += (page.get_filter().class_level_id == 0) ? '' : '&class_level_id=' + page.get_filter().class_level_id;
				link_grade += (page.get_filter().quran_level_id == 0) ? '' : '&quran_level_id=' + page.get_filter().quran_level_id;
				window.location = link_grade;
			} else {
				$.notify("Please select Class Level", "error");
			}
		});
		
		// page callback
		page.callback();
	}
	else {
		// grid
		var param = {
			id: 'task-grid',
			source: 'task/grid', aaSorting: [[ 3, "DESC" ]],
			column: [ { }, { }, { }, { }, { }, { sClass: 'center' } ],
			fnServerParams: function(aoData) {
				aoData.push( { name: 'grid_type', value: 'parent' } );
				aoData.push( { name: 'student_id', value: page.data.user.student_id } );
			},
			callback: function() {
				$('#task-grid .btn-detail').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					
					// show modal
					if (record.task_is_complete == 0) {
						record.grade = '';
					}
					Func.populate({ cnt: '#modal-task-detail', record: record });
					$('#modal-task-detail').modal();
				});
			}
		}
		var dt = Func.datatable(param);
	}
});
</script>

</html>