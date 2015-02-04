<?php
	// user
	$user = $this->user_model->get_session();
	
	// page
	$array_page['user'] = $user;
	$array_page['USER_TYPE_PARENT'] = USER_TYPE_PARENT;
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
	
	<div id="modal-comment" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-commentLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="s_id" value="" />
			<input type="hidden" name="action" value="update_comment" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-commentLabel">Update Comment</h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label">Good</label>
					<div class="controls">
						<textarea name="comment_good" class="span6" placeholder="Good"></textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Bad</label>
					<div class="controls">
						<textarea name="comment_bad" class="span6" placeholder="Bad"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Save" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<div id="modal-progress" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-progressLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-progressLabel">Generating Report Progress</h3>
			</div>
			<div class="modal-body">
				Generating Report Card 0%
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Report Card</h3>
			
			<div class="box-grid">
				<div class="box">
					<div class="tabbable">
						<ul class="nav nav-tabs box-wide">
							<li class="active"><a href="#tab-parent" data-toggle="tab">Parent</a></li>
							<li><a href="#tab-teacher" data-toggle="tab">Teacher</a></li>
						</ul>
						<div class="tab-content box-wide box-no-bottom-padding" style="padding-top: 15px; padding-bottom: 35px;">
							<div class="tab-pane fade in widget-comments active" id="tab-parent">
								<table class="table table-striped" id="grade-finalize-grid">
									<thead>
										<tr>
											<th style="width: 25%;">Father Name</th>
											<th style="width: 25%;">Mother Name</th>
											<th style="width: 25%;">Number of Students Enrolled</th>
											<th style="width: 25%;">Control</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<div class="tab-pane fade widget-threads" id="tab-teacher">
								<table class="table table-striped" id="teacher-grid">
									<thead>
										<tr>
											<th style="width: 25%;">Class</th>
											<th style="width: 25%;">Subject</th>
											<th style="width: 25%;">Teacher</th>
											<th style="width: 25%;">Control</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<div ></div>
						</div>
					</div>
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
		report_card: {
			start: 0, total: 0,
			generate: function(p) {
				p.finalize = (typeof(p.finalize) == 'undefined') ? 0 : p.finalize;
				
				Func.ajax({
					url: web.base + 'report_card/action',
					param: { action: 'generate_all', start: page.report_card.start, finalize: p.finalize },
					callback: function(result) {
						page.report_card.start++;
						page.report_card.total = result.parent_total;
						$('#modal-progress .modal-body').html(result.message);
						$('#modal-progress').modal();
						
						if (result.is_complete) {
							dt_parent.reload();
						} else {
							page.report_card.generate(p);
						}
					}
				});
			}
		}
	}
	page.init();
	
	// grid parent
	var param_parent = {
		id: 'grade-finalize-grid',
		source: 'report_card/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { }, { sClass: 'center' }, { bSortable: false, sClass: 'center' } ],
		fnServerParams: function(aoData) {
			aoData.push( { name: 'grid_type', value: 'report_card' } );
		},
		init: function() {
			$('#grade-finalize-grid_length').prepend('<div style="float: left; padding: 0 5px 0 0;" class="btn-group"><input type="button" class="btn btn-generate" value="Generate All" /><input type="button" class="btn btn-finalize" value="Finalize & Email" /></div>');
		},
		callback: function() {
			$('#grade-finalize-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.submit({
					url: web.base + 'report_card/action',
					param: { action: 'generate_report', parent_id: record.parent_id },
					callback: function(result) {
						dt_parent.reload();
					}
				});
			});
			
			$('#grade-finalize-grid .btn-preview').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				window.open(web.base + 'static/temp/' + record.report_card);
			});
			
			$('#grade-finalize-grid .btn-email').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.form.submit({
					url: web.base + 'report_card/action',
					param: { action: 'email_report', parent_id: record.parent_id }
				});
			});
		}
	}
	var dt_parent = Func.datatable(param_parent);
	
	// button parent
	$('.btn-generate').click(function() {
		// reset data and generate
		page.report_card.total = 0;
		page.report_card.start = 0;
		page.report_card.generate({ });
		
		// reset modal
		$('#modal-progress .modal-body').html('Generating Report Card 0%');
		$('#modal-progress').modal();
	});
	$('.btn-finalize').click(function() {
		// reset data and generate
		page.report_card.total = 0;
		page.report_card.start = 0;
		page.report_card.generate({ finalize: 1 });
		
		// reset modal
		$('#modal-progress .modal-body').html('Generating Report Card 0%');
		$('#modal-progress').modal();
	});
	
	// grid teacher
	var param_teacher = {
		id: 'teacher-grid',
		source: 'report_card/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		init: function() {
			$('#teacher-grid_length').prepend(
				'<div style="float: left; padding: 0 5px 0 0;">' +
					'<div class="btn-group open">' +
						'<button data-toggle="dropdown" class="btn dropdown-toggle" data-status_finalize="complete" style="margin: 0px;">Complete <span class="caret"></span></button>' +
						'<button class="btn btn-sent-mail hide">Send Email to All</button>' +
						'<ul class="dropdown-menu">' +
							'<li><a class="cursor btn-complete">Complete</a></li>' +
							'<li><a class="cursor btn-uncomplete">Not Completed</a></li>' +
						'</ul>' +
					'</div>' +
				'</div>'
			);
			
			// init button
			$('#teacher-grid_length .btn-complete').click(function() {
				// set title
				var title = $(this).html();
				$(this).parents('.btn-group').children('.dropdown-toggle').html(title + ' <span class="caret"></span>');
				$(this).parents('.btn-group').children('.dropdown-toggle').data('status_finalize', 'complete');
				dt_teacher.reload();
				
				// email button
				$('#teacher-grid_length .btn-sent-mail').hide();
			});
			$('#teacher-grid_length .btn-uncomplete').click(function() {
				// set title
				var title = $(this).html();
				$(this).parents('.btn-group').children('.dropdown-toggle').html(title + ' <span class="caret"></span>');
				$(this).parents('.btn-group').children('.dropdown-toggle').data('status_finalize', 'uncomplete');
				dt_teacher.reload();
				
				// email button
				$('#teacher-grid_length .btn-sent-mail').show();
			});
			$('#teacher-grid_length .btn-sent-mail').click(function() {
				Func.form.submit({
					url: web.base + 'report_card/action',
					param: { action: 'sent_mail_to_all', status_finalize: $('#teacher-grid_length .dropdown-toggle').data('status_finalize') }
				});
			});
		},
		fnServerParams: function(aoData) {
			var status_finalize = 'complete';
			if ($('#teacher-grid_length .dropdown-toggle').length == 1) {
				status_finalize = $('#teacher-grid_length .dropdown-toggle').data('status_finalize');
			}
			
			aoData.push( { name: 'grid_type', value: 'report_card_teacher' } );
			aoData.push( { name: 'status_finalize', value: status_finalize } );
		},
		callback: function() {
			$('#teacher-grid .btn-email').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// param
				var param = {
					action: 'sent_mail_to_single',
					class_type_id: record.class_type_id
				}
				if (record.quran_level_id != 0) {
					param.quran_level_id = record.quran_level_id;
				}
				if (record.class_level_id != 0) {
					param.class_level_id = record.class_level_id;
				}
				
				Func.form.submit({ url: web.base + 'report_card/action', param: param });
			});
		}
	}
	var dt_teacher = Func.datatable(param_teacher);
});
</script>

</html>
