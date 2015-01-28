<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Quran Level</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Quran Level List</h4>
					<table class="table table-striped" id="level-grid">
						<thead>
							<tr>
								<th style="width: 40%;">Name</th>
								<th style="width: 40%;">No Order</th>
								<th style="width: 20%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-form-quran hide">
				<div class="box">
					<h4 class="center-title">Quran Level Form</h4>
					<form id="form-quran" class="form-horizontal" style="margin: 0px;">
						<input type="hidden" name="id" value="0" />
						<input type="hidden" name="action" value="update" />
						
						<div class="control-group">
							<label class="control-label">Name</label>
							<div class="controls"><input type="text" name="name" class="span8" placeholder="Name" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">No Order</label>
							<div class="controls"><input type="text" name="no_order" class="span8" placeholder="No Order" /></div>
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
			$('.box-form-quran').hide();
		},
		show_form_parent: function() {
			$('.box-grid').hide();
			$('.box-form-quran').show();
		}
	}
	
	// grid
	var param = {
		id: 'level-grid',
		source: web.base + 'level/quran_level/grid', aaSorting: [[ 1, "ASC" ]],
		column: [ { }, { sClass: 'center' }, { bSortable: false, sClass: 'center' } ],
		init: function() {
			$('#level-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-quran-add" value="Add" /></div>');
		},
		callback: function() {
			$('#level-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				Func.populate({ cnt: '#form-quran', record: record });
				page.show_form_parent();
			});
			
			$('#level-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', id: record.id },
					url: web.base + 'level/quran_level/action', callback: function() { dt.reload(); }
				});
			});
		}
	}
	var dt = Func.datatable(param);
	
	// form quran
	$('.btn-quran-add').click(function() {
		page.show_form_parent();
		
		// reset form
		$('#form-quran')[0].reset();
		$('#form-quran [name="id"]').val(0);
	});
	$('.btn-show-grid').click(function() {
		page.show_grid();
	});
	$('#form-quran').validate({
		rules: {
			name: { required: true },
			no_order: { required: true }
		}
	});
	$('#form-quran').submit(function(e) {
		e.preventDefault();
		if (! $('#form-quran').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('form-quran');
		Func.form.submit({
			url: web.base + 'level/quran_level/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-quran')[0].reset();
			}
		});
	});
});
</script>

</html>