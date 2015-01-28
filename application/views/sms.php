<?php
	// get user
	$user = $this->user_model->get_session();
	
	// page data
	$array_page['character_length'] = 140;
	$array_sms_info = get_array_mail_info();
	$array_teacher = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_TEACHER, 'limit' => 50 ));
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	<div class="hide">
		<div class="cnt-page"><?php echo json_encode($array_page); ?></div>
	</div>
	
	<div id="modal-sms" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-smsLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-smsLabel">SMS Detail</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">SMS Name</label>
					<div class="controls"><input type="text" name="sms_name" class="span6" placeholder="SMS Name" /></div>
				</div>
				<div class="control-group">
					<label class="control-label">SMS No</label>
					<div class="controls"><input type="text" name="sms_no" class="span6" placeholder="SMS No" /></div>
				</div>
				<div class="control-group">
					<label class="control-label">Twilio SID</label>
					<div class="controls"><input type="text" name="twilio_status" class="span6" placeholder="SID" /></div>
				</div>
				<div class="control-group">
					<label class="control-label">Message</label>
					<div class="controls"><textarea name="message" class="span6" style="height: 100px;" placeholder="Message"></textarea></div>
				</div>
				<div class="control-group">
					<label class="control-label">Create Date</label>
					<div class="controls"><input type="text" name="create_date_title" class="span6" placeholder="Create Date" /></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">SMS</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">SMS List</h4>
					<table class="table table-striped" id="sms-grid">
						<thead>
							<tr>
								<th style="width: 25%;">Name</th>
								<th>Message</th>
								<th style="width: 15%;">Create Date</th>
								<th style="width: 15%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-form-sms hide">
				<div class="box">
					<h4 class="center-title">SMS Form</h4>
					<form id="form-sms" class="form-horizontal" style="margin: 0px;">
						<input type="hidden" name="id" value="0" />
						<input type="hidden" name="student_id" value="0" />
						<input type="hidden" name="action" value="sent_sms" />
						
						<div class="control-group">
							<label class="control-label">Send to</label>
							<div class="controls">
								<select name="sms_info" class="span6">
									<?php echo ShowOption(array( 'Array' => $array_sms_info, 'ArrayID' => 'value', 'ArrayTitle' => 'title' )); ?>
								</select>
							</div>
						</div>
						<div class="control-group input-for-teacher hide">
							<label class="control-label">Select a Teacher</label>
							<div class="controls">
								<select name="teacher_select" class="span6">
									<?php echo ShowOption(array( 'Array' => $array_teacher, 'ArrayID' => 'user_id', 'ArrayTitle' => 'user_display' )); ?>
								</select>
							</div>
						</div>
						<div class="control-group input-for-parent hide">
							<label class="control-label">Select a Parent</label>
							<div class="controls cnt-typeahead">
								<input type="text" name="parent_text" class="span8 typeahead-student" placeholder="Select a Parent" />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">SMS Name</label>
							<div class="controls"><input type="text" name="sms_name" class="span8" placeholder="SMS Name" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Message</label>
							<div class="controls cnt-textarea">
								<div class="span8 sms-message" style="min-height: 20px; text-align: right;"><span>140</span> characters left</div>
								<textarea name="message" class="span8" style="height: 100px;" placeholder="Message"></textarea>
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<input type="submit" class="btn btn-primary" value="Send" />
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
			// page data
			var raw = $('.cnt-page').html();
			eval('var data = ' + raw);
			page.data = data;
			
			// jquery validate
			$.validator.addMethod("sms_teacher", function(value, element) {
				var sms_info = $('#form-sms [name="sms_info"]').val();
				
				var result = true;
				if (sms_info == 'Specific Teachers' && value == '') {
					result = false;
				}
				
				return result;
			}, "Please select Teacher.");
			
			
		},
		show_grid: function() {
			$('.box-grid').show();
			$('.box-form-sms').hide();
		},
		show_form_mail: function() {
			$('.box-grid').hide();
			$('.box-form-sms').show();
		}
	}
	page.init();
	
	// grid
	var param = {
		id: 'sms-grid',
		source: 'sms/grid', aaSorting: [[ 2, "DESC" ]],
		column: [ { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		init: function() {
			$('#sms-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-sms-add" value="Send SMS" /></div>');
		},
		callback: function() {
			$('#sms-grid .btn-detail').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				Func.populate({ cnt: '#modal-sms', record: record });
				$('#modal-sms').modal();
			});
			
			$('#sms-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', id: record.id },
					url: web.base + 'sms/action', callback: function() { dt.reload(); }
				});
			});
		}
	}
	var dt = Func.datatable(param);
	
	// autocomplete
	var student_store = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		prefetch: web.base + 'typeahead?action=student',
		remote: web.base + 'typeahead?action=student&namelike=%QUERY'
	});
	student_store.initialize();
	var student = $('.typeahead-student').typeahead(null, {
		name: 'student',
		displayKey: 's_name',
		source: student_store.ttAdapter(),
		templates: {
			empty: [
				'<div class="empty-message">',
				'no result found.',
				'</div>'
			].join('\n'),
			suggestion: Handlebars.compile('<p><strong>{{s_name}}</strong></p>')
		}
	});
	student.on('typeahead:selected', function(evt, data) {
		// get data
		var sms_info = $('#form-sms [name="sms_info"] option:selected').text();
		
		// set form
		$('#form-sms [name="student_id"]').val(data.s_id);
		$('#form-sms [name="sms_name"]').val(sms_info + ' - ' + data.father_name);
	});
	
	// form
	$('.btn-sms-add').click(function() {
		page.show_form_mail();
		
		// reset form
		$('#form-sms')[0].reset();
		$('#form-sms [name="id"]').val(0);
		$('#form-sms [name="student_id"]').val(0);
		$('#form-sms [type="submit"]').attr('disabled', false);
	});
	$('.btn-show-grid').click(function() {
		page.show_grid();
	});
	$('#form-sms [name="message"]').keyup(function() {
		var value = $(this).val();
		var char_left = page.data.character_length - value.length;
		var el_parent = $(this).parents('.cnt-textarea');
		
		if (char_left < 0) {
			el_parent.find('.sms-message').addClass('red');
		} else {
			el_parent.find('.sms-message').removeClass('red');
		}
		el_parent.find('.sms-message span').html(char_left);
	});
	$('#form-sms [name="sms_info"]').change(function() {
		var text = $('#form-sms [name="sms_info"] option:selected').text();
		var value = $('#form-sms [name="sms_info"]').val();
		
		// set view
		$('.input-for-teacher').hide();
		$('.input-for-parent').hide();
		if (value == 'Specific Teachers') {
			$('.input-for-teacher').show();
		} else if (value == 'Specific Parents') {
			$('.input-for-parent').show();
		}
		
		// set data
		var text_display = (text == '-') ? '' : text + ' - ';
		$('#form-sms [name="sms_name"]').val(text_display);
	});
	$('#form-sms [name="teacher_select"]').change(function() {
		var sms_info = $('#form-sms [name="sms_info"] option:selected').text();
		var teacher_select = $('#form-sms [name="teacher_select"] option:selected').text();
		$('#form-sms [name="sms_name"]').val(sms_info + ' - ' + teacher_select);
	});
	$('#form-sms').validate({
		rules: {
			sms_info: { required: true },
			teacher_select: { sms_teacher: true },
			sms_name: { required: true },
			message: { required: true }
		}
	});
	$('#form-sms').submit(function(e) {
		e.preventDefault();
		if (! $('#form-sms').valid()) {
			return false;
		}
		
		// ajax request
		$('#form-sms [type="submit"]').attr('disabled', true);
		var param = Func.form.get_value('form-sms');
		Func.form.submit({
			url: web.base + 'sms/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-sms')[0].reset();
				$('#form-sms [type="submit"]').attr('disabled', false);
			},
			callback_error: function() {
				$('#form-sms [type="submit"]').attr('disabled', false);
			}
		});
	});
});
</script>

</html>