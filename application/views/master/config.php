<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Config</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Config List</h4>
					<table class="table table-striped" id="config-grid">
						<thead>
							<tr>
								<th style="width: 80%;">Description</th>
								<th style="width: 20%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-form-config hide">
				<div class="box">
					<h4 class="center-title">Config Form</h4>
					<form id="form-config" class="form-horizontal" style="margin: 0px;">
						<input type="hidden" name="config_id" value="0" />
						<input type="hidden" name="action" value="update" />
						
						<div class="control-group">
							<label class="control-label">Description</label>
							<div class="controls"><input type="text" name="config_desc" class="span8" placeholder="Description" readonly="readonly" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Content</label>
							<div class="controls"><textarea name="config_value" class="span8" placeholder="Content" style="height: 300px;"></textarea></div>
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
			$('.box-form-config').hide();
		},
		show_form_config: function() {
			$('.box-grid').hide();
			$('.box-form-config').show();
		}
	}
	
	// grid
	var param = {
		id: 'config-grid',
		source: web.base + 'master/config/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { bSortable: false, sClass: 'center' } ],
		callback: function() {
			$('#config-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				Func.populate({ cnt: '#form-config', record: record });
				page.show_form_config();
			});
		}
	}
	var dt = Func.datatable(param);
	
	// form config
	$('.btn-show-grid').click(function() {
		page.show_grid();
	});
	$('#form-config').validate({
		rules: {
			config_desc: { required: true },
			config_value: { required: true }
		}
	});
	$('#form-config').submit(function(e) {
		e.preventDefault();
		if (! $('#form-config').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('form-config');
		Func.form.submit({
			url: web.base + 'master/config/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-config')[0].reset();
			}
		});
	});
});
</script>

</html>