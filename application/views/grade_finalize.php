<?php
	// data
	$class_type_id = (isset($_GET['class_type_id'])) ? $_GET['class_type_id'] : 0;
	$class_level_id = (isset($_GET['class_level_id'])) ? $_GET['class_level_id'] : 0;
	$quran_level_id = (isset($_GET['quran_level_id'])) ? $_GET['quran_level_id'] : 0;
	
	$param_class_note = array( 'class_type_id' => $class_type_id );
	if (!empty($class_level_id)) {
		$param_class_note['class_level_id'] = $class_level_id;
	}
	if (!empty($quran_level_id)) {
		$param_class_note['quran_level_id'] = $quran_level_id;
	}
	$class_note = $this->class_note_model->get_by_id($param_class_note);
	
	// user
	$user = $this->user_model->get_session();
	$param_class_type = array( 'user_id' => $user['user_id'], 'return_list_id' => true );
	if (!empty($class_level_id)) {
		$param_class_type['class_level_id'] = $class_level_id;
	}
	if (!empty($quran_level_id)) {
		$param_class_type['quran_level_id'] = $quran_level_id;
	}
	$user_class_type = $this->teacher_class_model->get_array($param_class_type);
	
	// page
	$array_page['user'] = $user;
	$array_page['user_class_type'] = $user_class_type;
	$array_page['class_type_id'] = $class_type_id;
	$array_page['class_level_id'] = $class_level_id;
	$array_page['quran_level_id'] = $quran_level_id;
	$array_page['USER_TYPE_PARENT'] = USER_TYPE_PARENT;
	$array_page['USER_TYPE_TEACHER'] = USER_TYPE_TEACHER;
	$array_page['USER_TYPE_ADMINISTRATOR'] = USER_TYPE_ADMINISTRATOR;
	$array_page['CLASS_TYPE_QURAN'] = CLASS_TYPE_QURAN;
	$array_page['CLASS_TYPE_FIQH'] = CLASS_TYPE_FIQH;
	$array_page['CLASS_TYPE_AKHLAG'] = CLASS_TYPE_AKHLAG;
	$array_page['CLASS_TYPE_TAREEKH'] = CLASS_TYPE_TAREEKH;
	$array_page['CLASS_TYPE_AQAID'] = CLASS_TYPE_AQAID;
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	<div class="hide">
		<div class="cnt-page"><?php echo json_encode($array_page); ?></div>
	</div>
	
	<div id="modal-comment" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-commentLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="id" value="" />
			<input type="hidden" name="student_id" value="" />
			<input type="hidden" name="action" value="update_comment" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-commentLabel">Update Comment</h3>
			</div>
			<div class="modal-body">
				<table border="1" style="width: 80%; margin: 0 auto 25px;;">
					<thead>
						<tr style="text-align: center; font-weight: bold;">
							<td style="width: 50%;">Good comments</td>
							<td style="width: 50%;">Bad comments</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 10px;">
								<button class="btn btn-mini btn-success" type="button" style="margin-bottom: 3px;">1. Great Work</button><br />
								<button class="btn btn-mini btn-success" type="button" style="margin-bottom: 3px;">2. Outstanding Student</button><br />
								<button class="btn btn-mini btn-success" type="button" style="margin-bottom: 3px;">3. Good Work Habits</button><br />
								<button class="btn btn-mini btn-success" type="button" style="margin-bottom: 3px;">4. Very Neat & Accurate work</button><br />
								<button class="btn btn-mini btn-success" type="button" style="margin-bottom: 3px;">5. Highly motivated</button><br />
								<button class="btn btn-mini btn-success" type="button" style="margin-bottom: 3px;">6. Contributes intelligently to class</button><br />
								<button class="btn btn-mini btn-success" type="button" style="margin-bottom: 3px;">7. Works well in group activities</button><br />
							</td>
							<td style="padding: 10px;">
								<button class="btn btn-mini btn-danger" type="button" style="margin-bottom: 3px;">8. Appears disorganized</button><br />
								<button class="btn btn-mini btn-danger" type="button" style="margin-bottom: 3px;">9. Quality of Work Declining</button><br />
								<button class="btn btn-mini btn-danger" type="button" style="margin-bottom: 3px;">10. Does not bring Materials</button><br />
								<button class="btn btn-mini btn-danger" type="button" style="margin-bottom: 3px;">11. Does not follow Directions</button><br />
								<button class="btn btn-mini btn-danger" type="button" style="margin-bottom: 3px;">12. Inconsistent effort</button><br />
								<button class="btn btn-mini btn-danger" type="button" style="margin-bottom: 3px;">13. Behavior Needs Improvement</button><br />
								<button class="btn btn-mini btn-danger" type="button" style="margin-bottom: 3px;">14. Difficulty in understanding subject matter</button><br />
							</td>
						</tr>
					</tbody>
				</table>
				
				<div class="control-group">
					<label class="control-label">Good</label>
					<div class="controls">
						<textarea name="comment_good" class="span6" placeholder="Good"></textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Bad</label>
					<div class="controls">
						<textarea name="comment_bad" class="span6" placeholder="Bad"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Save" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Finalize</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Finalize</h4>
					<table class="table table-striped" id="grade-finalize-grid">
						<thead>
							<tr>
								<th style="width: 15%;">Name</th>
								<th style="width: 15%;">Quran</th>
								<th style="width: 15%;">Fiqh</th>
								<th style="width: 15%;">Akhlaq</th>
								<th style="width: 15%;">Tareekh</th>
								<th style="width: 15%;">Aqaid</th>
								<th style="width: 10%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					
					<div style="margin: 10px 0px; text-align: center;">
						<button class="btn btn-blue btn-save-exit" type="button">Save & Exit</button>
						<button class="btn btn-blue btn-finalize" type="button">Finalize</button>
					</div>
					
					<?php if (!empty($class_note['finalize_date'])) { ?>
					<div style="padding: 15px 0 25px 0;">Grades were finalized for this class on <?php echo get_format_date($class_note['finalize_date'], array( 'date_format' => 'F j Y, H:i' )); ?></div>
					<?php } ?>
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
		},
		modal_set_value: function(p) {
			// get value
			var raw_value = p.button.html().trim();
			var array_temp = raw_value.split('.');
			var value = array_temp[0];
			
			// merge with exisitng value
			var target_value = p.target.val().trim();
			var array_data = (target_value == '') ? [] : target_value.split(',');
			if (Func.in_array(value, array_data)) {
				for (var i = 0; i < array_data.length; i++) {
					if (array_data[i] == value) {
						array_data.splice(i, 1);
					}
				}
			} else {
				array_data.push(value);
			}
			
			// set to form
			var result = array_data.sort(function (a, b) {return a - b;}).join(',');
			p.target.val(result);
		}
	}
	page.init();
	
	// grid column
	var grid_column = [
		{ },
		{ bSortable: false, sClass: (Func.in_array(page.data.CLASS_TYPE_QURAN, page.data.user_class_type)) ? 'center' : 'center hide' },
		{ bSortable: false, sClass: (Func.in_array(page.data.CLASS_TYPE_FIQH, page.data.user_class_type)) ? 'center' : 'center hide' },
		{ bSortable: false, sClass: (Func.in_array(page.data.CLASS_TYPE_AKHLAG, page.data.user_class_type)) ? 'center' : 'center hide' },
		{ bSortable: false, sClass: (Func.in_array(page.data.CLASS_TYPE_TAREEKH, page.data.user_class_type)) ? 'center' : 'center hide' },
		{ bSortable: false, sClass: (Func.in_array(page.data.CLASS_TYPE_AQAID, page.data.user_class_type)) ? 'center' : 'center hide' },
		{ bSortable: false, sClass: 'center' }
	];
	
	// grid
	var param = {
		id: 'grade-finalize-grid', aaSorting: [[ 0, "ASC" ]],
		source: 'grade_finalize/grid', aaSorting: [[ 0, "ASC" ]], bFilter: false, bLengthChange: false,
		column: grid_column,
		fnServerParams: function(aoData) {
			aoData.push( { name: 'class_type_id', value: page.data.class_type_id } );
			if (page.data.class_level_id != 0) {
				aoData.push( { name: 'class_level_id', value: page.data.class_level_id } );
			}
			if (page.data.quran_level_id != 0) {
				aoData.push( { name: 'quran_level_id', value: page.data.quran_level_id } );
			}
		},
		callback: function() {
			$('#grade-finalize-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.ajax({
					url: web.base + 'grade_finalize/action',
					param: { action: 'get_teacher_comment', student_id: record.id, class_type_id: page.data.class_type_id },
					callback: function(result) {
						Func.populate({ cnt: '#modal-comment form', record: result });
						$('#modal-comment').modal();
					}
				});
				
			});
		}
	}
	var dt = Func.datatable(param);
	
	// form modal student
	$('#modal-comment .btn-success').click(function() {
		page.modal_set_value({ button: $(this), target: $('#modal-comment [name="comment_good"]') });
		
	});
	$('#modal-comment .btn-danger').click(function() {
		page.modal_set_value({ button: $(this), target: $('#modal-comment [name="comment_bad"]') });
	});
	
	$('#modal-comment form').validate({
		rules: {
//			comment_good: { required: true },
//			comment_bad: { required: true }
		}
	});
	$('#modal-comment form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-comment form').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('modal-comment form');
		Func.form.submit({
			url: web.base + 'grade_finalize/action',
			param: param,
			callback: function(result) {
				$('#modal-comment').modal('hide');
			}
		});
	});
	
	// button
	$('.btn-save-exit').click(function() {
		var param = { class_type_id: page.data.class_type_id };
		if (page.data.class_level_id != 0) {
			param.class_level_id = page.data.class_level_id;
		}
		if (page.data.quran_level_id != 0) {
			param.quran_level_id = page.data.quran_level_id;
		}
		
		// redirect
		var link_task = web.base + 'task?class_type_id=' + page.data.class_type_id;
		if (page.data.class_level_id != 0) {
			link_task += '&class_level_id=' + page.data.class_level_id;
		}
		if (page.data.quran_level_id != 0) {
			link_task += '&quran_level_id=' + page.data.quran_level_id;
		}
		window.location = link_task;
	});
	$('.btn-finalize').click(function() {
		var param = { action: 'finalize', class_type_id: page.data.class_type_id };
		if (page.data.class_level_id != 0) {
			param.class_level_id = page.data.class_level_id;
		}
		if (page.data.quran_level_id != 0) {
			param.quran_level_id = page.data.quran_level_id;
		}
		
		Func.form.submit({
			url: web.base + 'grade_finalize/action',
			param: param,
			callback: function(result) {
				var link_task = web.base + 'task?class_type_id=' + page.data.class_type_id;
				if (page.data.class_level_id != 0) {
					link_task += '&class_level_id=' + page.data.class_level_id;
				}
				if (page.data.quran_level_id != 0) {
					link_task += '&quran_level_id=' + page.data.quran_level_id;
				}
				window.location = link_task;
			}
		});
	});
});
</script>

</html>