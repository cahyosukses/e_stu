<?php
	$array_mail_info = get_array_mail_info();
	$array_teacher = $this->user_model->get_array(array( 'user_type_id' => USER_TYPE_TEACHER, 'limit' => 50 ));
	
	// class info
	$array_class_type = get_array_class_type();
	$array_quran_level = $this->quran_level_model->get_array(array( 'option_all' => 1 ));
	$array_fiqh_level = $this->class_level_model->get_array(array( 'option_all' => 1, 'fiqh' => 1 ));
	$array_akhlag_level = $this->class_level_model->get_array(array( 'option_all' => 1, 'akhlaq' => 1 ));
	$array_taareekh_level = $this->class_level_model->get_array(array( 'option_all' => 1, 'taareekh' => 1 ));
	$array_aqaid_level = $this->class_level_model->get_array(array( 'option_all' => 1, 'aqaid' => 1 ));
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	<div class="hide">
		<iframe name="iframe_attachment" src="<?php echo base_url('upload?callback_name=add_attachment&file_rename=0'); ?>"></iframe>
	</div>
	
	<div id="modal-email" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-emailLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-emailLabel">Email Detail</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">From</label>
					<div class="controls">
						<input type="text" name="from" class="span6" placeholder="From" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Subject</label>
					<div class="controls"><input type="text" name="subject" class="span6" placeholder="Subject" /></div>
				</div>
				<div class="control-group">
					<label class="control-label">Message</label>
					<div class="controls"><textarea name="content" class="span6" style="height: 100px;" placeholder="Message"></textarea></div>
				</div>
				<div class="control-group cnt-attachment">
					<label class="control-label">Attachment</label>
					<div class="controls" style="padding-top: 5px;">
						<ul style="margin-left: 15px;"></ul>
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
			<h3 class="box-header">Email</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Email List</h4>
					<table class="table table-striped" id="mail-grid">
						<thead>
							<tr>
								<th style="width: 25%;">From</th>
								<th>Subject</th>
								<th style="width: 15%;">Due Date</th>
								<th style="width: 15%;">Control</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
			<div class="box-form-mail hide">
				<div class="box">
					<h4 class="center-title">Email Form</h4>
					<form id="form-mail" class="form-horizontal" style="margin: 0px;">
						<input type="hidden" name="id" value="0" />
						<input type="hidden" name="student_id" value="0" />
						<input type="hidden" name="action" value="sent_mail" />
						
						<div class="control-group">
							<label class="control-label">Send to</label>
							<div class="controls">
								<select name="mail_info" class="span6">
									<?php echo ShowOption(array( 'Array' => $array_mail_info, 'ArrayID' => 'value', 'ArrayTitle' => 'title' )); ?>
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
						<div class="control-group input-for-teacher-classroom hide">
							<div class="control-group">
								<label class="control-label">Class Type</label>
								<div class="controls">
									<select name="class_type_id" class="span6">
										<?php echo ShowOption(array( 'Array' => $array_class_type, 'WithEmptySelect' => 0 )); ?>
									</select>
								</div>
							</div>
							<div>
								<div class="class-level cnt-quran-level">
									<label class="control-label">Quran Level</label>
									<div class="controls">
										<select name="quran_level_id" class="span6">
											<?php echo ShowOption(array( 'Array' => $array_quran_level, 'ArrayTitle' => 'name' )); ?>
										</select>
									</div>
								</div>
								<div class="class-level cnt-fiqh-level hide">
									<label class="control-label">Class Level</label>
									<div class="controls">
										<select name="fiqh_level_id" class="span6">
											<?php echo ShowOption(array( 'Array' => $array_fiqh_level, 'ArrayTitle' => 'name' )); ?>
										</select>
									</div>
								</div>
								<div class="class-level cnt-akhlaq-level hide">
									<label class="control-label">Class Level</label>
									<div class="controls">
										<select name="akhlaq_level_id" class="span6">
											<?php echo ShowOption(array( 'Array' => $array_akhlag_level, 'ArrayTitle' => 'name' )); ?>
										</select>
									</div>
								</div>
								<div class="class-level cnt-taareekh-level hide">
									<label class="control-label">Class Level</label>
									<div class="controls">
										<select name="taareekh_level_id" class="span6">
											<?php echo ShowOption(array( 'Array' => $array_taareekh_level, 'ArrayTitle' => 'name' )); ?>
										</select>
									</div>
								</div>
								<div class="class-level cnt-aqaid-level hide">
									<label class="control-label">Class Level</label>
									<div class="controls">
										<select name="aqaid_level_id" class="span6">
											<?php echo ShowOption(array( 'Array' => $array_aqaid_level, 'ArrayTitle' => 'name' )); ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Subject</label>
							<div class="controls"><input type="text" name="subject" class="span8" placeholder="Subject" /></div>
						</div>
						<div class="control-group">
							<label class="control-label">Message</label>
							<div class="controls"><textarea name="content" class="span8" style="height: 100px;" placeholder="Message"></textarea></div>
						</div>
						<div class="control-group cnt-attachment">
							<label class="control-label">Attachment</label>
							<div class="controls">
								<input type="button" class="btn btn-attachment" value="Add Attachment" />
								<ul style="margin: 0px 0px 0px 13px; padding: 10px 0 0 0;"></ul>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Send me a copy</label>
							<div class="controls"><input type="checkbox" name="send_copy" value="1" /></div>
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
			$.validator.addMethod("required_level", function(value, element) {
				var record = Func.form.get_value('form-mail');
				var classroom_filter = page.classroom.get_filter();
				
				var result = true;
				if (record.mail_info == 'Teacher Classroom') {
					if (typeof(classroom_filter.class_level_id) == 'undefined' && typeof(classroom_filter.quran_level_id) == 'undefined') {
						result = false;
					}
				}
				
				return result;
			}, "Please select Quran or Class Level.");
		},
		show_grid: function() {
			$('.box-grid').show();
			$('.box-form-mail').hide();
		},
		show_form_mail: function() {
			$('.box-grid').hide();
			$('.box-form-mail').show();
		},
		classroom: {
			get_filter: function() {
				var temp = Func.form.get_value('form-mail');
				
				// set value
				var result = { class_type_id: temp.class_type_id };
				if (temp.class_type_id == 1 && temp.quran_level_id != '') {
					result.quran_level_id = temp.quran_level_id;
				} else if (temp.class_type_id == 2 && temp.fiqh_level_id != '') {
					result.class_level_id = temp.fiqh_level_id;
				} else if (temp.class_type_id == 3 && temp.akhlaq_level_id != '') {
					result.class_level_id = temp.akhlaq_level_id;
				} else if (temp.class_type_id == 4 && temp.taareekh_level_id != '') {
					result.class_level_id = temp.taareekh_level_id;
				} else if (temp.class_type_id == 5 && temp.aqaid_level_id != '') {
					result.class_level_id = temp.aqaid_level_id;
				}
				
				return result;
			}
		},
		attachment: {
			append: function(p) {
				// prepare data
				p.cnt = (typeof(p.cnt) == 'undefined') ? '#form-mail' : p.cnt;
				p.enable_link = (typeof(p.enable_link) == 'undefined') ? false : p.enable_link;
				p.button_delete = (typeof(p.button_delete) == 'undefined') ? true : p.button_delete;
				
				// generate link
				var string_title = p.row.file_only;
				if (p.enable_link) {
					string_title = '<a href="' + web.base + 'static/upload/' + p.row.file_name + '" target="_blank">' + p.row.file_only + '</a>';
				}
				
				// generate button delete
				var string_button_delete = '';
				if (p.button_delete) {
					string_button_delete = '<a class="cursor btn-remove">(remove)</a>';
				}
				
				// append
				var raw_html = '<li>' + string_title + ' ' + string_button_delete + ' <input type="hidden" name="array_attachment[]" value=\'' + Func.object_to_json(p.row) + '\' /></li>';
				$(p.cnt + ' .cnt-attachment ul').append(raw_html);
				
				// init
				if (p.button_delete) {
					$(p.cnt + ' .btn-remove').last().click(function() {
						$(this).parent('li').remove();
					});
				}
			},
			populate: function(p) {
				// prepare data
				p.cnt = (typeof(p.cnt) == 'undefined') ? '#form-mail' : p.cnt;
				p.enable_link = (typeof(p.enable_link) == 'undefined') ? false : p.enable_link;
				p.button_delete = (typeof(p.button_delete) == 'undefined') ? true : p.button_delete;
				
				if (p.raw_attachment.length == 0) {
					return;
				}
				
				// generate html
				eval('var array_attachment = ' + p.raw_attachment);
				$(p.cnt + ' .cnt-attachment ul').html('');
				for (var i = 0; i < array_attachment.length; i++) {
					page.attachment.append({ cnt: p.cnt, enable_link: p.enable_link, button_delete: p.button_delete, row: array_attachment[i] });
				}
			}
		}
	}
	page.init();
	
	// upload
	$('#form-mail .btn-attachment').click(function() { window.iframe_attachment.browse() });
	add_attachment = function(p) {
		page.attachment.append({ row: p });
	}
	
	// grid
	var param = {
		id: 'mail-grid',
		source: 'email/grid', aaSorting: [[ 2, "DESC" ]],
		column: [ { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		init: function() {
			$('#mail-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;"><input type="button" class="btn btn-mail-add" value="Send Email" /></div>');
		},
		callback: function() {
			$('#mail-grid .btn-detail').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				Func.populate({ cnt: '#modal-email', record: record });
				page.attachment.populate({ cnt: '#modal-email', button_delete: false, enable_link: true, raw_attachment: record.attachment });
				$('#modal-email [name="from"]').val(record.from_title + '<' + record.from_email + '>');
				$('#modal-email').modal();
				
				// set read
				if (record.is_read == 0) {
					var param = Func.form.get_value('form-mail');
					Func.form.submit({
						notify: false,
						url: web.base + 'email/action',
						param: { action: 'update', id: record.id, is_read: 1 },
						callback: function(result) {
							dt.reload();
						}
					});
				}
			});
			
			$('#mail-grid .btn-delete').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.confirm_delete({
					data: { action: 'delete', id: record.id },
					url: web.base + 'email/action', callback: function() { dt.reload(); }
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
		$('#form-mail [name="student_id"]').val(data.s_id);
	});
	
	// form
	$('.btn-mail-add').click(function() {
		page.show_form_mail();
		
		// reset form
		$('#form-mail')[0].reset();
		$('#form-mail [name="id"]').val(0);
		$('#form-mail [name="student_id"]').val(0);
		$('#form-mail .cnt-attachment ul').html('');
	});
	$('.btn-show-grid').click(function() {
		page.show_grid();
	});
	$('#form-mail [name="mail_info"]').change(function() {
		var value = $('#form-mail [name="mail_info"]').val();
		$('#form-mail .input-for-teacher, #form-mail .input-for-parent, #form-mail .input-for-teacher-classroom').hide();
		
		if (value == 'Specific Teachers') {
			$('#form-mail .input-for-teacher').show();
		} else if (value == 'Specific Parents') {
			$('#form-mail .input-for-parent').show();
		} else if (value == 'Teacher Classroom') {
			$('#form-mail .input-for-teacher-classroom').show();
		}
	});
	$('#form-mail [name="class_type_id"]').change(function() {
		var value = $('#form-mail [name="class_type_id"]').val();
		
		// show hide
		$('#form-mail .class-level').hide();
		if (value == 1) {
			$('.cnt-quran-level').show();
		} else if (value == 2) {
			$('.cnt-fiqh-level').show();
		} else if (value == 3) {
			$('.cnt-akhlaq-level').show();
		} else if (value == 4) {
			$('.cnt-taareekh-level').show();
		} else if (value == 5) {
			$('.cnt-aqaid-level').show();
		}
		
		// reset
		$('#form-mail .class-level select').val('');
	});
	$('#form-mail').validate({
		rules: {
			mail_info: { required: true },
			quran_level_id: { required_level: true },
			fiqh_level_id: { required_level: true },
			akhlaq_level_id: { required_level: true },
			taareekh_level_id: { required_level: true },
			aqaid_level_id: { required_level: true },
			teacher_select: { required_teacher: true },
			subject: { required: true },
			content: { required: true }
		}
	});
	$('#form-mail').submit(function(e) {
		e.preventDefault();
		if (! $('#form-mail').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('form-mail');
		$('#form-mail [type="submit"]').attr('disabled', true);
		Func.form.submit({
			url: web.base + 'email/action',
			param: param,
			callback: function(result) {
				dt.reload();
				page.show_grid();
				$('#form-mail')[0].reset();
				$('#form-mail [type="submit"]').attr('disabled', false);
			}
		});
	});
});
</script>

</html>