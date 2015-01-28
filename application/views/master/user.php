<?php
	$array_user_type = $this->user_type_model->get_array(array( 'id_in' => '1,2,3' ));
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">User</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">User List</h4>
					<table class="table table-striped" id="user-grid">
						<thead>
							<tr>
								<th style="width: 15%;">User Type</th>
								<th style="width: 30%;">Display Name</th>
								<th style="width: 25%;">Email</th>
								<th style="width: 15%;">Phone</th>
								<th style="width: 15%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-form-user hide">
				<div class="box">
					<h4 class="center-title">User Form</h4>
					<form id="form-user" class="form-horizontal" style="margin: 0px;">
						<input type="hidden" name="user_id" value="0" />
						<input type="hidden" name="action" value="update" />
						
						<div class="control-group">
							<label class="control-label">User Name</label>
							<div class="controls"><input type="text" name="user_uname" class="span8" placeholder="User Name" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">User Type</label>
							<div class="controls">
								<select name="user_type_id" class="span4">
									<?php echo ShowOption(array( 'Array' => $array_user_type )); ?>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Display Name</label>
							<div class="controls"><input type="text" name="user_display" class="span8" placeholder="Display Name" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Password</label>
							<div class="controls"><input type="password" name="user_pword" class="span8" placeholder="Password" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Email</label>
							<div class="controls"><input type="text" name="user_email" class="span8" placeholder="Email" /></div>
						</div>
						<div class="control-group hide">
							<label class="control-label">Subject</label>
							<div class="controls"><input type="text" name="teacher_subject" class="span8" placeholder="Subject" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Phone</label>
							<div class="controls"><input type="text" name="phone" class="span8" placeholder="Phone" /></div>
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
			$('.box-form-user').hide();
		},
		show_form_parent: function() {
			$('.box-grid').hide();
			$('.box-form-user').show();
		}
	}
	
	// grid
	var param = {
		id: 'user-grid',
		source: web.base + 'master/user/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		init: function() {
			$('#user-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-user-add" value="Add" /></div>');
		},
		callback: function() {
			$('#user-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				delete record.user_pword;
				Func.populate({ cnt: '#form-user', record: record });
				page.show_form_parent();
			});
			
			$('#user-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', user_id: record.user_id },
					url: web.base + 'master/user/action', callback: function() { dt.reload(); }
				});
			});
		}
	}
	var dt = Func.datatable(param);
	
	// form student
	$('.btn-user-add').click(function() {
		page.show_form_parent();
		
		// reset form
		$('#form-user')[0].reset();
		$('#form-user [name="user_id"]').val(0);
	});
	$('.btn-show-grid').click(function() {
		page.show_grid();
	});
	$('#form-user').validate({
		rules: {
			user_uname: { required: true },
			user_type_id: { required: true },
			user_email: { required: true, email: true }
		}
	});
	$('#form-user').submit(function(e) {
		e.preventDefault();
		if (! $('#form-user').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('form-user');
		Func.form.submit({
			url: web.base + 'master/user/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-user')[0].reset();
			}
		});
	});
});
</script>

</html>