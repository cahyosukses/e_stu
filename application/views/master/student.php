<?php
	$array_quran_level = $this->quran_level_model->get_array();
	$array_class_level = $this->class_level_model->get_array();
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Student</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Student List</h4>
					<table class="table table-striped" id="student-grid">
						<thead>
							<tr>
								<th style="width: 20%;">Name</th>
								<th style="width: 20%;">Father Name</th>
								<th style="width: 15%;">Mother Name</th>
								<th style="width: 15%;">Quran Level</th>
								<th style="width: 15%;">Class Level</th>
								<th style="width: 15%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-form-student hide">
				<div class="box">
					<h4 class="center-title">Student Form</h4>
					<form id="form-student" class="form-horizontal" style="margin: 0px;">
						<input type="hidden" name="s_id" value="0" />
						<input type="hidden" name="s_parent_id" value="0" />
						<input type="hidden" name="action" value="update" />
						
						<div class="control-group">
							<label class="control-label">Name</label>
							<div class="controls"><input type="text" name="s_name" class="span8" placeholder="Name" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Parent Name</label>
							<div class="controls cnt-typeahead">
								<input type="text" name="father_name" class="span8 typeahead-parent" placeholder="Select a Parent" />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" style="padding-top: 9px;">Date of Birth</label>
							<div class="controls">
								<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>">
									<input type="text" name="s_dob" class="input-small input-datepicker" size="16" />
									<span class="add-on"><i class="icon-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Quran Level</label>
							<div class="controls">
								<select name="quran_level_id" class="span4">
									<?php echo ShowOption(array( 'Array' => $array_quran_level, 'ArrayTitle' => 'name' )); ?>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Class Level</label>
							<div class="controls">
								<select name="class_level_id" class="span4">
									<?php echo ShowOption(array( 'Array' => $array_class_level, 'ArrayTitle' => 'name' )); ?>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Gender</label>
							<div class="controls">
								<select name="s_gender" class="span4">
									<option value="1">Male</option>
									<option value="0">Female</option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Last Level</label>
							<div class="controls"><input type="text" name="s_last_level" class="span8" placeholder="Last Level" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Age</label>
							<div class="controls"><input type="text" name="s_age" class="span8" placeholder="Age" /></div>
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
		</section>
		
		<?php echo $this->load->view( 'common/footer' ); ?>
	</section>
</body>

<script type="text/javascript">
$(document).ready(function() {
	var page = {
		show_grid: function() {
			$('.box-grid').show();
			$('.box-form-student').hide();
		},
		show_form_student: function() {
			$('.box-grid').hide();
			$('.box-form-student').show();
		}
	}
	
	// grid
	var param = {
		id: 'student-grid',
		source: web.base + 'master/student/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { }, { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		init: function() {
			$('#student-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-student-add" value="Add" /></div>');
		},
		callback: function() {
			$('#student-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				Func.populate({ cnt: '#form-student', record: record });
				page.show_form_student();
			});
			
			$('#student-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', s_id: record.s_id },
					url: web.base + 'master/student/action', callback: function() { dt.reload(); }
				});
			});
		}
	}
	var dt = Func.datatable(param);
	
	// autocomplete
	var parent_store = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('p_father_name'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		prefetch: web.base + 'typeahead?action=parent',
		remote: web.base + 'typeahead?action=parent&namelike=%QUERY'
	});
	parent_store.initialize();
	var parent = $('.typeahead-parent').typeahead(null, {
		name: 'parent',
		displayKey: 'p_father_name',
		source: parent_store.ttAdapter(),
		templates: {
			empty: [
				'<div class="empty-message">',
				'no result found.',
				'</div>'
			].join('\n'),
			suggestion: Handlebars.compile('<p><strong>{{p_father_name}}</strong></p>')
		}
	});
	parent.on('typeahead:selected', function(evt, data) {
		$('#form-student [name="s_parent_id"]').val(data.p_id);
	});
	
	// form student
	$('.btn-student-add').click(function() {
		page.show_form_student();
		
		// reset form
		$('#form-student')[0].reset();
		$('#form-student [name="s_id"]').val(0);
		$('#form-student [name="s_parent_id"]').val(0);
	});
	$('.btn-show-grid').click(function() {
		page.show_grid();
	});
	$('#form-student').validate({
		rules: {
			s_name: { required: true },
			s_parent_id: { required: true },
			father_name: { required: true },
			s_dob: { required: true },
			quran_level_id: { required: true },
			class_level_id: { required: true },
			s_gender: { required: true }
		}
	});
	$('#form-student').submit(function(e) {
		e.preventDefault();
		if (! $('#form-student').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('form-student');
		Func.form.submit({
			url: web.base + 'master/student/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-student')[0].reset();
			}
		});
	});
});
</script>

</html>