<?php
	// user
	$user = $this->user_model->get_session();
	
	// page
	$array_page['user'] = $user;
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
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Calendar</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Calendar List</h4>
					<table class="table table-striped" id="calendar-grid">
						<thead>
							<tr>
								<th style="width: 20%;">Create By</th>
								<th style="width: 20%;">Start Date</th>
								<th>Title</th>
								<th style="width: 15%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-form-calendar hide">
				<div class="box">
					<h4 class="center-title">Calendar Form</h4>
					<form id="form-calendar" class="form-horizontal" style="margin: 0px;">
						<input type="hidden" name="id" value="0" />
						<input type="hidden" name="action" value="update" />
						
						<div class="control-group">
							<label class="control-label" style="padding-top: 9px;">Start Date</label>
							<div class="controls">
								<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>">
									<input type="text" name="start_temp_date" class="input-small input-datepicker" size="16" />
									<span class="add-on"><i class="icon-calendar"></i></span>
								</div>
								<div class="input-append bootstrap-timepicker">
									<input name="start_temp_time" class="timepicker input-small" type="text" />
									<span class="add-on"><i class="icon-time"></i></span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" style="padding-top: 9px;">End Date</label>
							<div class="controls">
								<div class="input-append date datepicker" data-date="<?php echo date("m-d-Y"); ?>">
									<input type="text" name="end_temp_date" class="input-small input-datepicker" size="16" />
									<span class="add-on"><i class="icon-calendar"></i></span>
								</div>
								<div class="input-append bootstrap-timepicker">
									<input name="end_temp_time" class="timepicker input-small" type="text" />
									<span class="add-on"><i class="icon-time"></i></span>
								</div>
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
		init: function() {
			var raw = $('.cnt-page').html();
			eval('var data = ' + raw);
			page.data = data;
		},
		show_grid: function() {
			$('.box-grid').show();
			$('.box-form-calendar').hide();
		},
		show_form_calendar: function() {
			$('.box-grid').hide();
			$('.box-form-calendar').show();
		}
	}
	page.init();
	
	// grid
	var param = {
		id: 'calendar-grid',
		source: 'calendar/grid', aaSorting: [[ 2, "DESC" ]],
		column: [ { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		init: function() {
			if (page.data.USER_TYPE_ADMINISTRATOR == page.data.user.user_type_id) {
				$('#calendar-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-calendar-add" value="Add" /></div>');
			}
		},
		callback: function() {
			$('#calendar-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				Func.populate({ cnt: '#form-calendar', record: record });
				page.show_form_calendar();
			});
			
			$('#calendar-grid .btn-detail').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				$('#form-calendar [type="submit"]').remove();
				Func.populate({ cnt: '#form-calendar', record: record });
				page.show_form_calendar();
			});
			
			$('#calendar-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', id: record.id },
					url: web.base + 'calendar/action', callback: function() { dt.reload(); }
				});
			});
		}
	}
	var dt = Func.datatable(param);
	
	// form calendar
	$('.btn-calendar-add').click(function() {
		page.show_form_calendar();
		
		// reset form
		$('#form-calendar')[0].reset();
		$('#form-calendar [name="id"]').val(0);
	});
	$('.btn-show-grid').click(function() {
		page.show_grid();
	});
	$('#form-calendar').validate({
		rules: {
			start_temp_date: { required: true },
			title: { required: true },
			content: { required: true }
		}
	});
	$('#form-calendar').submit(function(e) {
		e.preventDefault();
		if (! $('#form-calendar').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('form-calendar');
		param.start_date = param.start_temp_date + ' ' + param.start_temp_time;
		param.end_date = param.end_temp_date + ' ' + param.end_temp_time;
		Func.form.submit({
			url: web.base + 'calendar/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-calendar')[0].reset();
			}
		});
	});
});
</script>

</html>