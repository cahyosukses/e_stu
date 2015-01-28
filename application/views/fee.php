<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<style>
		@media screen and (max-width: 1000px) {
			.cnt-button-small { display: block; }
			.dataTables_length { display: none; }
			.dataTables_filter { display: none; }
			.dataTables_info { display: none; }
			.dataTable .column-small { display: none; }
			.paging_full_numbers > a { display: none; }
		}
	</style>
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<div id="modal-fee" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-feeLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="parent_id" value="0" />
			<input type="hidden" name="action" value="generate_fee" />
			<input type="hidden" name="payment_type" value="register" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-feeLabel">Generate Fee</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Payment For</label>
					<div class="controls">
						<select name="payment_for" class="span4">
							<option value="All Parent">All Parent</option>
							<option value="Spesific Parent">Specific Parent</option>
						</select>
					</div>
				</div>
				<div class="control-group cnt-parent">
					<label class="control-label">Father Name</label>
					<div class="controls cnt-typeahead">
						<input type="text" name="parent_text" class="span4 typeahead-parent" placeholder="Select a Parent" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">1st Child</label>
					<div class="controls">
						<input type="text" name="first_student" class="span4" placeholder="1st Child" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Additional Child</label>
					<div class="controls">
						<input type="text" name="additional_student" class="span4" placeholder="Additional Child" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Discount</label>
					<div class="controls">
						<input type="text" name="discount" class="span4" placeholder="Discount" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Generate" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<div id="modal-paid" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-paidLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="id" value="0" />
			<input type="hidden" name="action" value="update_fee" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-paidLabel">Payment Fee</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Discount</label>
					<div class="controls">
						<input type="text" name="discount" class="span4" placeholder="Discount" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Method</label>
					<div class="controls">
						<label class="radio"><input name="payment_method" type="radio" value="Cash" checked="checked" /> Cash</label>
						<label class="radio"><input name="payment_method" type="radio" value="Check" /> Check</label>
					</div>
				</div>
				<div class="control-group cnt-check">
					<label class="control-label">Last 4 digits</label>
					<div class="controls">
						<input type="text" name="last_digit" class="span4" placeholder="Last 4 digits of check number" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Submit" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Fee</h3>
			
			<div class="box-grid">
				<div id="cnt-grid-display" class="box">
					<div class="row-fluid">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Show Fee :</div>
						<div class="span2">
							<select name="display_type" style="width: 100%;">
								<option value="not_paid">Not Paid</option>
								<option value="already_paid">Already Paid</option>
							</select>
						</div>
					</div>
				</div>
				
				<div id="cnt-not-paid" class="box">
					<h4 class="center-title">Not Paid List</h4>
					
					<div class="cnt-button-small hide" style="padding: 10px 0;">
						<input type="button" value="Generate Fee" class="btn btn-fee-add">
					</div>
					
					<table class="table table-striped" id="fee-not-paid-grid">
						<thead>
							<tr>
								<th style="width: 30%;">Father Name</th>
								<th style="width: 30%;">Mother Name</th>
								<th style="width: 10%;">Total</th>
								<th style="width: 10%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				
				<div id="cnt-already-paid" class="box">
					<h4 class="center-title">Already Paid List</h4>
					
					<table class="table table-striped" id="fee-already-paid-grid">
						<thead>
							<tr>
								<th style="width: 30%;">Father Name</th>
								<th style="width: 15%;">Method</th>
								<th style="width: 15%;">Last Digits</th>
								<th style="width: 10%;">Total</th>
								<th style="width: 10%;">Control</th>
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
	var page = {
		init: function() {
			// method rule
			$.validator.addMethod("required_parent", function(value, element) {
				var payment_for = $('#modal-fee [name="payment_for"]').val();
				
				var result = true;
				if (payment_for == 'Spesific Parent' && value == '') {
					result = false;
				}
				
				return result;
			}, "Please select Parent.");
			$.validator.addMethod("required_payment_check", function(value, element) {
				var param = Func.form.get_value('#modal-paid form');
				
				var result = true;
				if (param.payment_method == 'Check' && value == '') {
					result = false;
				}
				
				return result;
			}, "Please enter Last 4 digits of check number.");
			
			// show grid
			page.show_not_paid();
		},
		show_not_paid: function() {
			$('#cnt-not-paid').show();
			$('#cnt-already-paid').hide();
		},
		show_already_paid: function() {
			$('#cnt-not-paid').hide();
			$('#cnt-already-paid').show();
		}
	}
	page.init();
	
	// display grid
	$('#cnt-grid-display [name="display_type"]').change(function() {
		var value = $(this).val();
		if (value == 'not_paid') {
			page.show_not_paid();
		} else if (value == 'already_paid') {
			page.show_already_paid();
		}
	});
	
	// grid not paid
	var param_not_paid = {
		id: 'fee-not-paid-grid',
		source: web.base + 'fee/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { sClass: 'column-small' }, { bSortable: false, sClass: 'center' }, { bSortable: false, sClass: 'center' } ],
		fnServerParams: function(aoData) {
			aoData.push(
				{ name: 'grid_type', value: 'not_paid' },
				{ name: 'is_paid', value: '0' }
			);
		},
		init: function() {
			$('#fee-not-paid-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-fee-add" value="Generate Fee" /></div>');
		},
		callback: function() {
			$('#fee-not-paid-grid .btn-paid').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				$('#modal-paid form')[0].reset();
				Func.populate({ cnt: '#modal-paid', record: record });
				$('#modal-paid [name="payment_method"]:checked').click();
				$('#modal-paid').modal();
			});
			
			$('#fee-not-paid-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', id: record.id },
					url: web.base + 'fee/action', callback: function() { dt_not_paid.reload(); }
				});
			});
		}
	}
	var dt_not_paid = Func.datatable(param_not_paid);
	
	// grid already paid
	var param_already_paid = {
		id: 'fee-already-paid-grid',
		source: web.base + 'fee/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { sClass: 'column-small' }, { sClass: 'column-small' }, { bSortable: false, sClass: 'center' }, { bSortable: false, sClass: 'center' } ],
		fnServerParams: function(aoData) {
			aoData.push(
				{ name: 'grid_type', value: 'already_paid' },
				{ name: 'is_paid', value: '1' }
			);
		},
		callback: function() {
			$('#fee-already-paid-grid .btn-invoice').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				window.open(record.link_nvoice);
			});
			
			$('#fee-already-paid-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', id: record.id },
					url: web.base + 'fee/action', callback: function() { dt_already_paid.reload(); }
				});
			});
		}
	}
	var dt_already_paid = Func.datatable(param_already_paid);
	
	// autocomplete
	var parent_store = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('p_father_name'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		prefetch: web.base + 'typeahead?action=parent',
		remote: web.base + 'typeahead?action=parent&namelike=%QUERY'
	});
	parent_store.initialize();
	var modal_parent = $('.typeahead-parent').typeahead(null, {
		name: 'modal_parent',
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
	modal_parent.on('typeahead:selected', function(evt, data) {
		$('#modal-fee [name="parent_id"]').val(data.p_id);
	});
	
	// form generate fee
	$('.btn-fee-add').click(function() {
		// reset form
		$('#modal-fee form')[0].reset();
		$('#modal-fee [name="payment_for"]').change();
		$('#modal-fee').modal();
	});
	$('#modal-fee [name="payment_for"]').change(function() {
		var value = $(this).val();
		if (value == 'All Parent') {
			$('#modal-fee .cnt-parent').hide();
		} else if (value == 'Spesific Parent') {
			$('#modal-fee .cnt-parent').show();
		}
	});
	$('#modal-fee form').validate({
		rules: {
			payment_for: { required: true },
			parent_text: { required_parent: true },
			first_student: { required: true },
			additional_student: { required: true }
		}
	});
	$('#modal-fee form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-fee form').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('modal-fee');
		Func.form.submit({
			url: web.base + 'fee/action',
			param: param,
			callback: function(result) {
				dt_not_paid.reload();
				$('#modal-fee').modal('hide');
				$('#modal-fee form')[0].reset();
			}
		});
	});
	
	// form paid
	$('#modal-paid [name="payment_method"]').click(function() {
		var param = Func.form.get_value('#modal-paid form');
		if (param.payment_method == 'Check') {
			$('#modal-paid .cnt-check').show();
		} else {
			$('#modal-paid .cnt-check').hide();
		}
	});
	$('#modal-paid form').validate({
		rules: {
			payment_method: { required: true },
			last_digit: { required_payment_check: true }
		}
	});
	$('#modal-paid form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-paid form').valid()) {
			return false;
		}
		
		// ajax request
		$('#modal-paid [type="submit"]').attr('disabled', true);
		var param = Func.form.get_value('modal-paid');
		Func.form.submit({
			url: web.base + 'fee/action',
			param: param,
			callback: function(result) {
				dt_not_paid.reload();
				dt_already_paid.reload();
				$('#modal-paid').modal('hide');
				$('#modal-paid form')[0].reset();
				
				$('#modal-paid [type="submit"]').attr('disabled', false);
			}
		});
	});
});
</script>

</html>