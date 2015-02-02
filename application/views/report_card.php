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
					<h4 class="center-title">Report Card</h4>
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
					<div style="padding: 15px 0 25px 0;">Grades were finalized for this class on &lt;Date and Time Stamp&gt;</div>
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
							dt.reload();
						} else {
							page.report_card.generate(p);
						}
					}
				});
			}
		}
	}
	page.init();
	
	// grid
	var param = {
		id: 'grade-finalize-grid',
		source: 'report_card/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { }, { sClass: 'center' }, { bSortable: false, sClass: 'center' } ],
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
						dt.reload();
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
	var dt = Func.datatable(param);
	
	// button
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
});
</script>

</html>
