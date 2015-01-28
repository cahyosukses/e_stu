<?php
	// user
	$user = $this->user_model->get_session();
	
	// master
	$array_teacher = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_TEACHER, 'limit' => 50 ));
	
	// get array quran & class
	if ($user['user_type_id'] == USER_TYPE_TEACHER) {
		$array_class_type = $this->teacher_class_model->get_class_teacher(array( 'user_id' => $user['user_id'] ));
		$array_quran_level = $this->quran_level_model->get_teacher_array(array( 'user_id' => $user['user_id'] ));
		$array_fiqh_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'fiqh' => 1 ));
		$array_akhlag_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'akhlaq' => 1 ));
		$array_taareekh_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'taareekh' => 1 ));
		$array_aqaid_level = $this->class_level_model->get_teacher_array(array( 'user_id' => $user['user_id'], 'aqaid' => 1 ));
	} else {
		$array_class_type = get_array_class_type();
		$array_quran_level = $this->quran_level_model->get_array();
		$array_fiqh_level = $this->class_level_model->get_array(array( 'fiqh' => 1 ));
		$array_akhlag_level = $this->class_level_model->get_array(array( 'akhlaq' => 1 ));
		$array_taareekh_level = $this->class_level_model->get_array(array( 'taareekh' => 1 ));
		$array_aqaid_level = $this->class_level_model->get_array(array( 'aqaid' => 1 ));
	}
	
	// page
	$array_page['user'] = $user;
	$array_page['USER_TYPE_TEACHER'] = USER_TYPE_TEACHER;
	$array_page['USER_TYPE_ADMINISTRATOR'] = USER_TYPE_ADMINISTRATOR;
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<style>
	.pie-chart { margin: 0 2.5% !important; }
	.pie-chart .caption { height: 45px; }
	</style>
	
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	<div class="hide">
		<div class="cnt-page"><?php echo json_encode($array_page); ?></div>
	</div>
	
	<div id="modal-teacher" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-teacherLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="id" value="0" />
			<input type="hidden" name="class_level_id" value="0" />
			<input type="hidden" name="quran_level_id" value="0" />
			<input type="hidden" name="action" value="update_teacher" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-teacherLabel">Add Teacher</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Teacher</label>
					<div class="controls">
						<select name="user_id" class="span6">
							<?php echo ShowOption(array( 'Array' => $array_teacher, 'ArrayID' => 'user_id', 'ArrayTitle' => 'user_display' )); ?>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Add" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<div id="modal-student" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-studentLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="s_id" value="" />
			<input type="hidden" name="class_level_id" value="0" />
			<input type="hidden" name="quran_level_id" value="0" />
			<input type="hidden" name="action" value="update_student" />
			
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
	
	<div id="modal-grade" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-studentLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="modal-studentLabel">Average Grade</h3>
		</div>
		<div class="modal-body">
			<section class="row-fluid cnt-chart"></section>
		</div>
		<div class="modal-footer">
			<input type="button" class="btn" data-dismiss="modal" value="Close" />
		</div>
	</div>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Classes</h3>
			
			<div id="cnt-class-filter" class="box">
				<div class="row-fluid">
					<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Type :</div>
					<div class="span2">
						<select name="class_type" style="width: 100%;">
							<?php echo ShowOption(array( 'Array' => $array_class_type, 'WithEmptySelect' => 0 )); ?>
						</select>
					</div>
					<div class="span4">&nbsp;</div>
					<div class="span4 center">
						<input type="button" value="Average Grades" class="btn btn-average-grades">
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
					<h4 class="center-title">Teacher List</h4>
					<table class="table table-striped" id="teacher-grid">
						<thead>
							<tr>
								<th style="width: 75%;">Teacher</th>
								<th style="width: 25%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Student List</h4>
					<table class="table table-striped" id="student-grid">
						<thead>
							<tr>
								<th style="width: 30%;">Name</th>
								<th style="width: 25%;">Father Name</th>
								<th style="width: 25%;">Father Phone</th>
								<th style="width: 20%;">Control</th>
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
		}
	}
	page.init();
	
	// grid teacher
	var param_teacher = {
		id: 'teacher-grid',
		source: 'classes/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { bSortable: false, sClass: 'center' } ],
		fnServerParams: function(aoData) {
			var data = page.get_filter();
			aoData.push( { name: 'grid_type', value: 'teacher' } );
			aoData.push( { name: 'class_type_id', value: data.class_type } );
			
			// check
			data.quran_level_id = (data.quran_level_id == '') ? 0 : data.quran_level_id;
			data.class_level_id = (data.class_level_id == '') ? 0 : data.class_level_id;
			
			if (data.quran_level_id == 0 && data.class_level_id == 0) {
				aoData.push( { name: 'quran_level_id', value: 0 } );
				aoData.push( { name: 'class_level_id', value: 0 } );
			} else {
				if (data.quran_level_id != 0) {
					aoData.push( { name: 'quran_level_id', value: data.quran_level_id } );
				}
				
				if (data.class_level_id != 0) {
					aoData.push( { name: 'class_level_id', value: data.class_level_id } );
				}
			}
		},
		init: function() {
			if (page.data.USER_TYPE_ADMINISTRATOR == page.data.user.user_type_id) {
				$('#teacher-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-teacher-add" value="Add" /></div>');
			}
		},
		callback: function() {
			$('#teacher-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete_teacher', id: record.id },
					url: web.base + 'classes/action', callback: function() { dt_teacher.reload(); }
				});
			});
		}
	}
	var dt_teacher = Func.datatable(param_teacher);
	
	// grid student
	var param_student = {
		id: 'student-grid',
		source: 'classes/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		fnServerParams: function(aoData) {
			var data = page.get_filter();
			aoData.push( { name: 'grid_type', value: 'student' } );
			
			// check
			data.quran_level_id = (data.quran_level_id == '') ? 0 : data.quran_level_id;
			data.class_level_id = (data.class_level_id == '') ? 0 : data.class_level_id;
			
			if (data.quran_level_id == 0 && data.class_level_id == 0) {
				aoData.push( { name: 'quran_level_id', value: 0 } );
				aoData.push( { name: 'class_level_id', value: 0 } );
			} else {
				if (data.quran_level_id != 0) {
					aoData.push( { name: 'quran_level_id', value: data.quran_level_id } );
				}
				if (data.class_level_id != 0) {
					aoData.push( { name: 'class_level_id', value: data.class_level_id } );
				}
			}
		},
		init: function() {
			if (page.data.USER_TYPE_ADMINISTRATOR == page.data.user.user_type_id) {
				$('#student-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-student-add" value="Add" /></div>');
			}
		},
		callback: function() {
			$('#student-grid .btn-delete').click(function() {
				var filter = page.get_filter();
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// set param
				var param_update = {
					action: 'delete_student',
					s_id: record.s_id
				}
				if (filter.quran_level_id != 0) {
					param_update.quran_level_id = 0;
				} else if (filter.class_level_id != 0) {
					param_update.class_level_id = 0;
				} else {
					$.notify("This students do not have class.", "error");
					return false;
				}
				
				// request ajax
				Func.form.confirm_delete({
					data: param_update,
					url: web.base + 'classes/action', callback: function() { dt_student.reload(); }
				});
			});
		}
	}
	var dt_student = Func.datatable(param_student);
	
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
		dt_teacher.reload();
		dt_student.reload();
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
		$('#modal-student [name="s_id"]').val(data.s_id);
	});
	
	// form modal teacher
	$('.btn-teacher-add').click(function() {
		if (page.is_valid()) {
			$('#modal-teacher').modal();
			
			// reset form
			$('#modal-teacher form')[0].reset();
			$('#modal-teacher [name="id"]').val(0);
			
			var data = page.get_filter();
			Func.populate({ cnt: '#modal-teacher', record: data });
		} else {
			$.notify("Please select Quran or Class Level", "error");
		}
	});
	$('#modal-teacher form').validate({
		rules: {
			user_id: { required: true }
		}
	});
	$('#modal-teacher form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-teacher form').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('modal-teacher form');
		param.class_type_id = $('#cnt-class-filter [name="class_type"]').val();
		Func.form.submit({
			url: web.base + 'classes/action',
			param: param,
			callback: function(result) {
				dt_teacher.reload();
				$('#modal-teacher').modal('hide');
			}
		});
	});
	
	// form modal student
	$('.btn-student-add').click(function() {
		if (page.is_valid()) {
			$('#modal-student').modal();
			
			// reset form
			$('#modal-student form')[0].reset();
			$('#modal-student [name="s_id"]').val('');
			
			var data = page.get_filter();
			Func.populate({ cnt: '#modal-student', record: data });
		} else {
			$.notify("Please select Quran or Class Level", "error");
		}
	});
	$('#modal-student form').validate({
		rules: {
			s_id: { required: true },
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
			url: web.base + 'classes/action',
			param: param,
			callback: function(result) {
				dt_student.reload();
				$('#modal-student').modal('hide');
			}
		});
	});
	
	// modal average grade
	$('.btn-average-grades').click(function() {
		Func.ajax({
			is_json: false,
			url: web.base + 'classes/get_view',
			param: { action: 'get_class_grade' },
			callback: function(result) {
				$('#modal-grade .cnt-chart').html(result);
				$('#modal-grade').modal();
				
				var chart_config = { animate: 2000, scaleColor: false, lineWidth: 12, lineCap: 'square', size: 100, trackColor: '#e5e5e5' }
				$('#easy-pie-chart-1').easyPieChart($.extend({}, chart_config, { barColor: Func.get_color($('#easy-pie-chart-1').data('percent')) }));
				$('#easy-pie-chart-2').easyPieChart($.extend({}, chart_config, { barColor: Func.get_color($('#easy-pie-chart-2').data('percent')) }));
				$('#easy-pie-chart-3').easyPieChart($.extend({}, chart_config, { barColor: Func.get_color($('#easy-pie-chart-3').data('percent')) }));
				$('#easy-pie-chart-4').easyPieChart($.extend({}, chart_config, { barColor: Func.get_color($('#easy-pie-chart-4').data('percent')) }));
				$('#easy-pie-chart-5').easyPieChart($.extend({}, chart_config, { barColor: Func.get_color($('#easy-pie-chart-5').data('percent')) }));
				
				// tooltips
				$('#modal-grade .pie-chart').tooltip({ placement: 'top' });
			}
		});
	});
});
</script>

</html>