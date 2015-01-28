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
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Report Card</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Report Card</h4>
					<table class="table table-striped" id="grade-finalize-grid">
						<thead>
							<tr>
								<th style="width: 15%;">Name</th>
								<th style="width: 15%;">Quran</th>
								<th style="width: 15%;">Figh</th>
								<th style="width: 15%;">Akhlaq</th>
								<th style="width: 15%;">Tareekh</th>
								<th style="width: 10%;">Control</th>
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
		}
	}
	page.init();
	
	// grid
	var param = {
		id: 'grade-finalize-grid',
		source: 'grade_finalize/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { sClass: 'center' }, { sClass: 'center' }, { sClass: 'center' }, { sClass: 'center' }, { bSortable: false, sClass: 'center' } ],
		callback: function() {
			$('#grade-finalize-grid .btn-edit').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				Func.ajax({
					url: web.base + 'grade_finalize/action',
					param: { action: 'get_student', student_id: record.id },
					callback: function(result) {
						Func.populate({ cnt: '#modal-comment form', record: result });
						$('#modal-comment').modal();
					}
				});
				
			});
		}
	}
	var dt = Func.datatable(param);
	
	// form modal student
	$('#modal-comment form').validate({
		rules: {
			comment_good: { required: true },
			comment_bad: { required: true }
		}
	});
	$('#modal-comment form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-comment form').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('modal-comment form');
		Func.form.submit({
			url: web.base + 'grade_finalize/action',
			param: param,
			callback: function(result) {
				$('#modal-comment').modal('hide');
			}
		});
	});
});
</script>

</html>