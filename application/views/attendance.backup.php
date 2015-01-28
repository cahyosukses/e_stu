<?php
	// view_type
	$view_type = (isset($view_type)) ? $view_type : 'normal';
	
	// user
	$user = $this->user_model->get_session();
	
	// master
	$array_class_type = $this->teacher_class_model->get_class_teacher(array( 'user_id' => $user['user_id'] ));
	$array_quran_level = $this->quran_level_model->get_teacher_array(array( 'user_id' => $user['user_id'] ));
	$array_fiqh_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'fiqh' => 1 ));
	$array_akhlag_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'akhlaq' => 1 ));
	$array_taareekh_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'taareekh' => 1 ));
	$array_aqaid_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'aqaid' => 1 ));
	
	// page
	$array_page['user'] = $user;
	$array_page['USER_TYPE_PARENT'] = USER_TYPE_PARENT;
	$array_page['USER_TYPE_TEACHER'] = USER_TYPE_TEACHER;
	$array_page['USER_TYPE_ADMINISTRATOR'] = USER_TYPE_ADMINISTRATOR;
	$array_page['current_date'] = $this->config->item('current_date');
	$array_page['format_date'] = date('m-d-Y');
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<style>
		.toggle-on, .toggle-off { font-size: 10px  !important; }
		
		@media screen and (max-width: 1000px) {
			.dataTables_filter { display: none; }
		}
	</style>
	
	<?php echo $this->load->view( 'common/header', array( 'view_type' => $view_type ) ); ?>
	<?php echo $this->load->view( 'common/panel_left', array( 'view_type' => $view_type ) ); ?>
	<div class="hide">
		<div class="cnt-page"><?php echo json_encode($array_page); ?></div>
	</div>
	
	<div id="modal-attendance" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-attendanceLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="id" value="0" />
			<input type="hidden" name="action" value="update_attendance" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-attendanceLabel">Add Attendance</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label" style="padding-top: 9px;">Due Date</label>
					<div class="controls">
						<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>">
							<input type="text" name="due_date" class="input-small input-datepicker" size="16" />
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Title</label>
					<div class="controls"><input type="text" name="title" class="span3" placeholder="Title" /></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Add" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Attendance</h3>
			
			<?php if ($user['user_type_id'] == USER_TYPE_TEACHER || $user['user_type_id'] == USER_TYPE_PRINCIPAL) { ?>
			<div id="cnt-class-filter" class="box-grid box">
				<div class="row-fluid">
					<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Type :</div>
					<div class="span2">
						<select name="class_type" style="width: 100%;">
							<?php echo ShowOption(array( 'Array' => $array_class_type, 'WithEmptySelect' => 0 )); ?>
						</select>
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
				<div class="row-fluid">
					<div class="span2">&nbsp;</div>
					<div class="span2" style="padding: 3px 0 25px 0;">
						<input type="button" class="btn btn-attendance-add" value="Add" />
					</div>
				</div>
			</div>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Attendance List</h4>
					<table class="table table-striped" id="attendance-grid">
						<thead>
							<tr>
								<th style="width: 30%;">Due Date</th>
								<th style="width: 40%;">Title</th>
								<th style="width: 30%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-grid-attendance-student hide">
				<div class="box">
					<h4 class="center-title">Attendance Student List</h4>
					<h4 class="center-title cnt-label">Attendance Student List</h4>
					
					<form class="form-horizontal">
						<input type="hidden" name="attendance_id" value="0" />
						<input type="hidden" name="action" value="attendance_student_update" />
						
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
					<h4 class="center-title">Attendance List</h4>
					<table class="table table-striped" id="attendance-grid">
						<thead>
							<tr>
								<th style="width: 15%;">Due Date</th>
								<th style="width: 15%;">Class</th>
								<th>Title</th>
								<th style="width: 15%;">Status</th>
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
	// page
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
			// set view
			$('.box-grid').show();
			$('.box-grid-attendance-student').hide();
		},
		show_attendance_student: function(p) {
			// set view
			$('.box-grid').hide();
			$('.box-grid-attendance-student').show();
			
			// set task id
			$('.box-grid-attendance-student [name="attendance_id"]').val(p.attendance_id);
			
			// load grid
			Func.ajax({
				is_json: false,
				url: web.base + 'attendance/get_view',
				param: { action: 'attendance_student_list', attendance_id: p.attendance_id },
				callback: function(result) {
					// set dom
					$('.box-grid-attendance-student .list-student').html(result);
					
					// toggle button
					for (var i = 0; i < $('.normal-toggle-button').length; i++) {
						var value = $('.normal-toggle-button').eq(i).siblings('.award').val();
						
						$('.normal-toggle-button').eq(i)
							.toggles({ text: { on: 'Present', off: 'Absent' }, on: (value == 1) ? true : false })
							.on('toggle', function (e, active) {
								if (active) {
									$(this).parents('td').find('.award').val(1);
								} else {
									$(this).parents('td').find('.award').val(0);
								}
							}
						);
					}
				}
			});
		}
	}
	page.init();
	
	// selector
	if (page.data.USER_TYPE_TEACHER == page.data.user.user_type_id || page.data.USER_TYPE_PRINCIPAL == page.data.user.user_type_id) {
		// grid attendance
		var param_attendance = {
			id: 'attendance-grid',
			source: 'attendance/grid', aaSorting: [[ 0, "DESC" ]],
			column: [ { }, { }, { bSortable: false, sClass: 'center' } ],
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
				$('#attendance-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-attendance-add" value="Add" /></div>');
			},
			callback: function() {
				$('#attendance-grid .btn-edit').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					
					// show modal
					Func.populate({ cnt: '#modal-attendance', record: record });
					$('#modal-attendance').modal();
				});
				
				$('#attendance-grid .btn-attendance-student').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					
					// show table
					$('.box-grid-attendance-student').show();
					
					// show title
					var title = record.due_date_swap;
					if (record.title != '') {
						title += ' - ' + record.title;
					}
					$('.box-grid-attendance-student .cnt-label').html(title);
					
					// show attendance student
					page.show_attendance_student({ attendance_id: record.id });
				});
				
				$('#attendance-grid .btn-delete').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					
					Func.form.confirm_delete({
						data: { action: 'delete_attendance', id: record.id },
						url: web.base + 'attendance/action', callback: function() { dt_attendance.reload(); }
					});
				});
			}
		}
		var dt_attendance = Func.datatable(param_attendance);
		
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
			
			// reset
			$('#cnt-class-filter .class-level select').val('');
			$('#cnt-class-filter .class-level select').change();
		});
		$('#cnt-class-filter .class-level select').change(function() {
			dt_attendance.reload();
		});
		$('#cnt-class-filter [name="class_type"]').change();
		
		// form modal attendance
		$('.btn-attendance-add').click(function() {
			if (page.is_valid()) {
				var class_level_id = $('[name="class_level_id"]').val();
				if (class_level_id == '') {
					$.notify("Please select Class Level", "error");
					return;
				}
				
				// show modal
				$('#modal-attendance').modal();
				
				// reset form
				var title = $('[name="class_type"] option:selected').text() + ' Attendance ' + page.data.format_date;
				$('#modal-attendance form')[0].reset();
				$('#modal-attendance [name="id"]').val(0);
				$('#modal-attendance [name="due_date"]').val(Func.swap_date(page.data.current_date));
				$('#modal-attendance [name="title"]').val(title);
			} else {
				$.notify("Please select Quran or Class Level", "error");
			}
		});
		$('#modal-attendance form').validate({
			rules: {
				due_date: { required: true }
			}
		});
		$('#modal-attendance form').submit(function(e) {
			e.preventDefault();
			if (! $('#modal-attendance form').valid()) {
				return false;
			}
			
			// filter
			var filter = page.get_filter();
			
			// collect param
			var param = Func.form.get_value('modal-attendance');
			param.class_type_id = filter.class_type;
			param.quran_level_id = filter.quran_level_id;
			param.class_level_id = filter.class_level_id;
			
			// ajax request
			Func.form.submit({
				url: web.base + 'attendance/action',
				param: param,
				callback: function(result) {
					dt_attendance.reload();
					$('#modal-attendance').modal('hide');
					$('#modal-attendance form')[0].reset();
				}
			});
		});
		
		// form update attendance
		$('.box-grid-attendance-student .btn-show-grid').click(function() {
			page.show_grid();
		});
		$('.box-grid-attendance-student form').submit(function(e) {
			e.preventDefault();
			
			// param
			var param = Func.form.get_value('.box-grid-attendance-student');
			
			// collect value
			param.array_award = [];
			for (var i = 0; i < $('.box-grid-attendance-student .award').length; i++) {
				var value = $('.box-grid-attendance-student .award').eq(i).val();
				var attendance_student_id = $('.box-grid-attendance-student .award').eq(i).data('attendance_student_id');
				param.array_award.push(attendance_student_id + ',' + value);
			}
			
			// ajax request
			Func.form.submit({
				url: web.base + 'attendance/action',
				param: param
			});
		});
	}
	else {
		// grid attendance
		var param_attendance = {
			id: 'attendance-grid',
			source: 'attendance/grid', aaSorting: [[ 0, "DESC" ]],
			column: [ { }, { }, { }, { sClass: 'center' } ],
			fnServerParams: function(aoData) {
				aoData.push( { name: 'grid_type', value: 'parent' } );
				aoData.push( { name: 'student_id', value: page.data.user.student_id } );
			}
		}
		var dt_attendance = Func.datatable(param_attendance);
	}
});
</script>

</html>