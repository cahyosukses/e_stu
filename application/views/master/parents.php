<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Parent</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Parent List</h4>
					<table class="table table-striped" id="parents-grid">
						<thead>
							<tr>
								<th style="width: 20%;">Father Name</th>
								<th style="width: 20%;">Father Email</th>
								<th style="width: 20%;">Father Cell</th>
								<th style="width: 20%;">Mother Name</th>
								<th style="width: 20%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-form-parents hide">
				<div class="box">
					<h4 class="center-title">Parent Form</h4>
					<form id="form-parents" class="form-horizontal" style="margin: 0px;">
						<input type="hidden" name="p_id" value="0" />
						<input type="hidden" name="action" value="update" />
						
						<div class="control-group">
							<label class="control-label">Father Name</label>
							<div class="controls"><input type="text" name="p_father_name" class="span8" placeholder="Father Name" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Father Email</label>
							<div class="controls"><input type="text" name="p_father_email" class="span8" placeholder="Father Email" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Father Cell</label>
							<div class="controls"><input type="text" name="p_father_cell" class="span8" placeholder="Father Cell" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Mother Name</label>
							<div class="controls"><input type="text" name="p_mother_name" class="span8" placeholder="Mother Cell" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Mother Email</label>
							<div class="controls"><input type="text" name="p_mother_email" class="span8" placeholder="Mother Email" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Mother Cell</label>
							<div class="controls"><input type="text" name="p_mother_cell" class="span8" placeholder="Mother Cell" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Phone</label>
							<div class="controls"><input type="text" name="p_phone" class="span8" placeholder="Phone" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Address</label>
							<div class="controls"><input type="text" name="p_address" class="span8" placeholder="Address" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Apt</label>
							<div class="controls"><input type="text" name="p_apt" class="span8" placeholder="Apt" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">City</label>
							<div class="controls"><input type="text" name="p_city" class="span8" placeholder="City" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">State</label>
							<div class="controls"><input type="text" name="p_state" class="span8" placeholder="State" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Zip</label>
							<div class="controls"><input type="text" name="p_zip" class="span8" placeholder="Zip" /></div>
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
			$('.box-form-parents').hide();
		},
		show_form_parent: function() {
			$('.box-grid').hide();
			$('.box-form-parents').show();
		}
	}
	
	// grid
	var param = {
		id: 'parents-grid',
		source: web.base + 'master/parents/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		init: function() {
			$('#parents-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-parents-add" value="Add" /></div>');
		},
		callback: function() {
			$('#parents-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				Func.populate({ cnt: '#form-parents', record: record });
				page.show_form_parent();
			});
			
			$('#parents-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', p_id: record.p_id },
					url: web.base + 'master/parents/action', callback: function() { dt.reload(); }
				});
			});
		}
	}
	var dt = Func.datatable(param);
	
	// form student
	$('.btn-parents-add').click(function() {
		page.show_form_parent();
		
		// reset form
		$('#form-parents')[0].reset();
		$('#form-parents [name="p_id"]').val(0);
	});
	$('.btn-show-grid').click(function() {
		page.show_grid();
	});
	$('#form-parents').validate({
		rules: {
			p_father_name: { required: true },
			p_father_email: { required: true, email: true },
			p_father_cell: { required: true },
			p_mother_name: { required: true }
		}
	});
	$('#form-parents').submit(function(e) {
		e.preventDefault();
		if (! $('#form-parents').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('form-parents');
		Func.form.submit({
			url: web.base + 'master/parents/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-parents')[0].reset();
			}
		});
	});
});
</script>

</html>