<?php
	$array_teacher = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_TEACHER, 'limit' => 50 ));
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<div id="modal-schedule" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-scheduleLabel" aria-hidden="true">
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
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Schedule</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Schedule List</h4>
					<table class="table table-striped" id="schedule-grid">
						<thead>
							<tr>
								<th style="width: 20%;">Time Frame</th>
								<th style="width: 20%;">Father Name</th>
								<th style="width: 20%;">Mother Name</th>
								<th style="width: 20%;">Teacher</th>
								<th style="width: 20%;">Control</th>
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
							<label class="control-label" style="padding-top: 9px;">Time From</label>
							<div class="controls">
								<div class="input-append bootstrap-timepicker" style="padding-top: 6px;">
									<input name="available_time_start" class="timepicker input-small" type="text" />
									<span class="add-on"><i class="icon-time"></i></span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" style="padding-top: 9px;">Time To</label>
							<div class="controls">
								<div class="input-append bootstrap-timepicker" style="padding-top: 6px;">
									<input name="available_time_to" class="timepicker input-small" type="text" />
									<span class="add-on"><i class="icon-time"></i></span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Select a Teacher</label>
							<div class="controls">
								<select name="user_id" class="span8">
									<?php echo ShowOption(array( 'Array' => $array_teacher, 'ArrayID' => 'user_id', 'ArrayTitle' => 'user_display' )); ?>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" style="padding-top: 9px;">Busy Time From</label>
							<div class="controls">
								<div class="input-append bootstrap-timepicker" style="padding-top: 6px;">
									<input name="busy_time_start" class="timepicker input-small" type="text" />
									<span class="add-on"><i class="icon-time"></i></span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" style="padding-top: 9px;">Busy Time To</label>
							<div class="controls">
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
		column: [ { }, { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		init: function() {
			$('#schedule-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn box-schedule-generate" value="Generate" /></div>');
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
	$('.box-schedule-generate').click(function() {
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
		Func.form.submit({
			url: web.base + 'schedule/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-schedule')[0].reset();
			}
		});
	});
});
</script>

</html>