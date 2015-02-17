<?php
	$array_teacher = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_TEACHER, 'limit' => 100 ));
	$array_parent = $this->parents_model->get_array(array( 'limit' => 100 ));
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<div id="modal-schedule" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-scheduleLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="id" />
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-scheduleLabel">Update Time Frame</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label" style="padding-top: 9px;">Date</label>
					<div class="controls">
						<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>" style="padding-left: 0px;">
							<input type="text" name="date_only" class="input-small input-datepicker" size="16" />
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" style="padding-top: 9px;">Time</label>
					<div class="controls">
						<div class="input-append bootstrap-timepicker" style="padding-top: 6px;">
							<input name="time_only" class="timepicker input-small" type="text" />
							<span class="add-on"><i class="icon-time"></i></span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<div id="modal-teacher" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-teachereLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="action" value="mail_teacher" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-teachereLabel">Mail Teacher</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Teacher</label>
					<div class="controls">
						<select name="user_id" class="span3">
							<?php echo ShowOption(array( 'Array' => $array_teacher, 'ArrayID' => 'user_id', 'ArrayTitle' => 'user_display', 'OptAll' => true )); ?>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Send" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<div id="modal-pdf" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-pdfLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-pdfLabel">Generate PDF</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Teacher</label>
					<div class="controls">
						<select name="user_id" class="span3">
							<?php echo ShowOption(array( 'Array' => $array_teacher, 'ArrayID' => 'user_id', 'ArrayTitle' => 'user_display', 'OptAll' => true )); ?>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Generate PDF" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<div id="modal-parent" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-parenteLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="action" value="mail_parent" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-parenteLabel">Mail Parent</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Parent</label>
					<div class="controls">
						<select name="p_id" class="span3">
							<?php echo ShowOption(array( 'Array' => $array_parent, 'ArrayID' => 'p_id', 'ArrayTitle' => 'p_father_name', 'OptAll' => true )); ?>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Send" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Schedule</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Schedule List</h4>
					
					<div style="padding: 0 0 15px 0;">
						<div class="btn-group">
							<button class="btn btn-schedule-generate">Generate</button>
							<button class="btn btn-pdf-generate">PDF</button>
						</div>
						<div class="btn-group">
							<button data-toggle="dropdown" class="btn btn-notofication dropdown-toggle" style="margin: 0px;">Send Mail <span class="caret"></span></button>
							<ul class="dropdown-menu">
								<li><a class="cursor btn-mail-parent">Parent</a></li>
								<li><a class="cursor btn-mail-teacher">Teacher</a></li>
							</ul>
						</div>
					</div>
					
					<table class="table table-striped" id="schedule-grid">
						<thead>
							<tr>
								<th style="width: 15%;">Time Frame</th>
								<th style="width: 15%;">Father Name</th>
								<th style="width: 15%;">Mother Name</th>
								<th style="width: 15%;">Teacher</th>
								<th style="width: 25%;">Student</th>
								<th style="width: 15%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-form-schedule hide">
				<div class="box">
					<h4 class="center-title">Schedule Form</h4>
					<form id="form-schedule" class="form-horizontal" style="margin: 0px;">
						<input type="hidden" name="id" value="0" />
						<input type="hidden" name="action" value="generate" />
						
						<div class="control-group">
							<label class="control-label" style="padding-top: 9px;">Date</label>
							<div class="controls">
								<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>" style="padding-left: 0px;">
									<input type="text" name="date_only" class="input-small input-datepicker" size="16" />
									<span class="add-on"><i class="icon-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Select a Teacher</label>
							<div class="controls">
								<select name="user_id" class="span8" multiple size="10">
									<?php echo ShowOption(array( 'Array' => $array_teacher, 'ArrayID' => 'user_id', 'ArrayTitle' => 'user_display', 'WithEmptySelect' => 0 )); ?>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" style="padding-top: 9px;">Time From - To</label>
							<div class="controls">
								<div class="input-append bootstrap-timepicker" style="padding-top: 6px;">
									<input name="available_time_start" class="timepicker input-small" type="text" />
									<span class="add-on"><i class="icon-time"></i></span>
								</div>
								<div class="input-append bootstrap-timepicker" style="padding-top: 6px;">
									<input name="available_time_to" class="timepicker input-small" type="text" />
									<span class="add-on"><i class="icon-time"></i></span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" style="padding-top: 9px;">Busy Time From - To</label>
							<div class="controls">
								<div class="input-append bootstrap-timepicker" style="padding-top: 6px;">
									<input name="busy_time_start" class="timepicker input-small" type="text" />
									<span class="add-on"><i class="icon-time"></i></span>
								</div>
								<div class="input-append bootstrap-timepicker" style="padding-top: 6px;">
									<input name="busy_time_to" class="timepicker input-small" type="text" />
									<span class="add-on"><i class="icon-time"></i></span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Length of time</label>
							<div class="controls"><input type="text" name="length_of_time" class="span8" placeholder="Length of time of a parent with each student in minutes" /></div>
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
			$('.box-form-schedule').hide();
		},
		show_form_parent: function() {
			$('.box-grid').hide();
			$('.box-form-schedule').show();
		}
	}
	
	// grid
	var param = {
		id: 'schedule-grid',
		source: web.base + 'schedule/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { }, { sClass: 'column-small' }, { sClass: 'column-small' }, { bSortable: false, sClass: 'column-small' }, { bSortable: false, sClass: 'center' } ],
		init: function() {
			//  init button
			$('.btn-mail-parent').click(function() {
				$('#modal-parent').modal();
			});
			$('.btn-mail-teacher').click(function() {
				$('#modal-teacher').modal();
			});
			$('.btn-pdf-generate').click(function() {
				$('#modal-pdf').modal();
			});
		},
		callback: function() {
			$('#schedule-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// set record
				var array_temp = record.time_frame.split(' ');
				record.date_only = array_temp[0];
				record.time_only = array_temp[1];
				
				aaa = record;
				record = aaa;
				
				// show modal
				Func.populate({ cnt: '#modal-schedule', record: record });
				$('#modal-schedule').modal();
			});
			
			$('#schedule-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', id: record.id },
					url: web.base + 'schedule/action', callback: function() { dt.reload(); }
				});
			});
		}
	}
	var dt = Func.datatable(param);
	
	// form student
	$('.btn-schedule-generate').click(function() {
		// reset form
		$('#form-schedule')[0].reset();
		
		page.show_form_parent();
	});
	$('.btn-show-grid').click(function() {
		page.show_grid();
	});
	$('#form-schedule').validate({
		rules: {
			date_only: { required: true },
			available_time_start: { required: true },
			available_time_to: { required: true },
			user_id: { required: true },
			length_of_time: { required: true, number: true }
		}
	});
	$('#form-schedule').submit(function(e) {
		e.preventDefault();
		if (! $('#form-schedule').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('form-schedule');
		$('#form-schedule [type="submit"]').attr('disabled', true);
		Func.form.submit({
			url: web.base + 'schedule/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-schedule')[0].reset();
				$('#form-schedule [type="submit"]').attr('disabled', false);
			},
			callback_error: function() {
				$('#form-schedule [type="submit"]').attr('disabled', false);
			}
		});
	});
	
	// modal mail parent
	$('#modal-parent form').validate({
		rules: {
			p_id: { required: true }
		}
	});
	$('#modal-parent form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-parent form').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('modal-parent form');
		$('#modal-parent [type="submit"]').attr('disabled', true);
		Func.form.submit({
			url: web.base + 'schedule/action',
			param: param,
			callback: function(result) {
				$('#modal-parent form')[0].reset();
				$('#modal-parent [type="submit"]').attr('disabled', false);
				$('#modal-parent').modal('hide');
			}
		});
	});
	
	// modal mail teacher
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
		$('#form-mail [type="submit"]').attr('disabled', true);
		Func.form.submit({
			url: web.base + 'schedule/action',
			param: param,
			callback: function(result) {
				$('#modal-teacher form')[0].reset();
				$('#modal-teacher [type="submit"]').attr('disabled', false);
				$('#modal-teacher').modal('hide');
			}
		});
	});
	
	// modal generate pdf
	$('#modal-pdf form').validate({
		rules: {
			user_id: { required: true }
		}
	});
	$('#modal-pdf form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-pdf form').valid()) {
			return false;
		}
		
		var param = Func.form.get_value('modal-pdf form');
		var link_pdf = web.base + 'schedule/generate/' + param.user_id;
		window.open(link_pdf);
	});
});
</script>

</html>