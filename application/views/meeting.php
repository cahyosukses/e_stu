<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<div id="modal-schedule" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-scheduleLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="action" value="update_schdule" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-scheduleLabel">Schedule</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Time Frame</label>
					<div class="controls">
						<select name="id" class="span3">
							<option value="">-</option>
						</select>
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
			<h3 class="box-header">Schedule</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Schedule Required</h4>
					<table class="table table-striped" id="meeting-required-grid">
						<thead>
							<tr>
								<th style="width: 40%;">Teacher</th>
								<th style="width: 40%;">Student</th>
								<th style="width: 20%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				
				<div class="box">
					<h4 class="center-title">Schedule List</h4>
					<table class="table table-striped" id="meeting-list-grid">
						<thead>
							<tr>
								<th style="width: 30%;">Time Frame</th>
								<th style="width: 30%;">Teacher</th>
								<th style="width: 40%;">Student</th>
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
	// grid meeting required
	var param_meeting_required = {
		id: 'meeting-required-grid',
		source: web.base + 'meeting/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { bSortable: false }, { bSortable: false, sClass: 'center' } ],
		fnServerParams: function(aoData) {
			aoData.push( { name: 'grid_type', value: 'meeting_required' } );
		},
		callback: function() {
			$('#meeting-required-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				combo.schedule({ user_id: record.user_id, target: $('#modal-schedule [name="id"]') });
				$('#modal-schedule').modal();
			});
		}
	}
	var dt_meeting_required = Func.datatable(param_meeting_required);
	
	// grid meeting list
	var param_meeting_list = {
		id: 'meeting-list-grid',
		source: web.base + 'meeting/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { sClass: 'center' }, { }, { bSortable: false } ],
		fnServerParams: function(aoData) {
			aoData.push( { name: 'grid_type', value: 'meeting_list' } );
		}
	}
	var dt_meeting_list = Func.datatable(param_meeting_list);
	
	// form schedule
	$('#modal-schedule form').validate({
		rules: {
			schedule_id: { required: true }
		}
	});
	$('#modal-schedule form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-schedule form').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('modal-schedule');
		$('#modal-schedule [type="submit"]').attr('disabled', true);
		Func.form.submit({
			url: web.base + 'meeting/action',
			param: param,
			callback: function(result) {
				dt_meeting_list.reload();
				dt_meeting_required.reload();
				$('#modal-schedule').modal('hide');
				$('#modal-schedule form')[0].reset();
				$('#modal-schedule [type="submit"]').attr('disabled', false);
			}
		});
	});
});
</script>

</html>