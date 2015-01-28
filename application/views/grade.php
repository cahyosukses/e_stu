<?php
	$array_class_type = get_array_class_type();
	$array_quran_level = $this->quran_level_model->get_array();
	$array_fiqh_level = $this->class_level_model->get_array(array( 'fiqh' => 1 ));
	$array_akhlag_level = $this->class_level_model->get_array(array( 'akhlaq' => 1 ));
	$array_taareekh_level = $this->class_level_model->get_array(array( 'taareekh' => 1 ));
	$array_aqaid_level = $this->class_level_model->get_array(array( 'aqaid' => 1 ));
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<div id="modal-email" class="modal modal-big hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-emailLabel" aria-hidden="true">
		<form class="form-horizontal" style="margin: 0px;">
			<input type="hidden" name="action" value="sent_report" />
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-emailLabel">Email Detail</h3>
			</div>
			<div class="modal-body">
					<div class="control-group">
						<label class="control-label">To</label>
						<div class="controls">
							<input type="text" name="to" class="span6" placeholder="To" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Subject</label>
						<div class="controls"><input type="text" name="subject" class="span6" placeholder="Subject" /></div>
					</div>
					<div class="control-group">
						<label class="control-label">Message</label>
						<div class="controls"><textarea name="message" class="span6" style="height: 100px;" placeholder="Message"></textarea></div>
					</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" value="Sent" />
				<input type="button" class="btn" data-dismiss="modal" value="Close" />
			</div>
		</form>
	</div>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Grade</h3>
			
			<div id="cnt-class-filter" class="box">
				<div class="row-fluid">
					<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Type :</div>
					<div class="span2">
						<select name="class_type" style="width: 100%;">
							<?php echo ShowOption(array( 'Array' => $array_class_type, 'WithEmptySelect' => 0 )); ?>
						</select>
					</div>
				</div>
				<div class="row-fluid">
					<div class="class-level cnt-quran-level">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Quran Level :</div>
						<div class="span2">
							<select name="quran_level_id" style="width: 100%;">
								<?php echo ShowOption(array( 'Array' => $array_quran_level, 'ArrayTitle' => 'name' )); ?>
							</select>
						</div>
					</div>
					<div class="class-level cnt-fiqh-level hide">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Level :</div>
						<div class="span2">
							<select name="fiqh_level_id" style="width: 100%;">
								<?php echo ShowOption(array( 'Array' => $array_fiqh_level, 'ArrayTitle' => 'name' )); ?>
							</select>
						</div>
					</div>
					<div class="class-level cnt-akhlaq-level hide">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Level :</div>
						<div class="span2">
							<select name="akhlaq_level_id" style="width: 100%;">
								<?php echo ShowOption(array( 'Array' => $array_akhlag_level, 'ArrayTitle' => 'name' )); ?>
							</select>
						</div>
					</div>
					<div class="class-level cnt-taareekh-level hide">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Level :</div>
						<div class="span2">
							<select name="taareekh_level_id" style="width: 100%;">
								<?php echo ShowOption(array( 'Array' => $array_taareekh_level, 'ArrayTitle' => 'name' )); ?>
							</select>
						</div>
					</div>
					<div class="class-level cnt-aqaid-level hide">
						<div class="span2" style="padding: 3px 0 0 0; text-align: right;">Class Level :</div>
						<div class="span2">
							<select name="aqaid_level_id" style="width: 100%;">
								<?php echo ShowOption(array( 'Array' => $array_aqaid_level, 'ArrayTitle' => 'name' )); ?>
							</select>
						</div>
					</div>
				</div>
			</div>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">Student List</h4>
					<table class="table table-striped" id="student-grid">
						<thead>
							<tr>
								<th style="width: 30%;">Name</th>
								<th style="width: 25%;">Father Name</th>
								<th style="width: 25%;">Father Phone</th>
								<th style="width: 20%;">Control</th>
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
	// page
	var page = {
		get_filter: function() {
			var temp = Func.form.get_value('cnt-class-filter');
			
			// set value
			var result = {
				class_type: temp.class_type,
				quran_level_id: 0,
				class_level_id: 0
			};
			if (temp.class_type == 1) {
				result.quran_level_id = temp.quran_level_id;
			} else if (temp.class_type == 2) {
				result.class_level_id = temp.fiqh_level_id;
			} else if (temp.class_type == 3) {
				result.class_level_id = temp.akhlaq_level_id;
			} else if (temp.class_type == 4) {
				result.class_level_id = temp.taareekh_level_id;
			} else if (temp.class_type == 5) {
				result.class_level_id = temp.aqaid_level_id;
			}
			
			return result;
		},
		is_valid: function() {
			var filter = page.get_filter();
			filter.quran_level_id = (filter.quran_level_id == '') ? 0 : filter.quran_level_id;
			filter.class_level_id = (filter.class_level_id == '') ? 0 : filter.class_level_id;
			
			var result = true;
			if (filter.quran_level_id == 0 && filter.class_level_id == 0) {
				result = false;
			}
			
			return result;
		}
	}
	
	// grid student
	var param_student = {
		id: 'student-grid',
		source: 'grade/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { }, { }, { bSortable: false, sClass: 'center' } ],
		fnServerParams: function(aoData) {
			var data = page.get_filter();
			aoData.push( { name: 'grid_type', value: 'student' } );
			
			// check
			data.quran_level_id = (data.quran_level_id == '') ? 0 : data.quran_level_id;
			data.class_level_id = (data.class_level_id == '') ? 0 : data.class_level_id;
			
			if (data.quran_level_id == 0 && data.class_level_id == 0) {
				aoData.push( { name: 'quran_level_id', value: 0 } );
				aoData.push( { name: 'class_level_id', value: 0 } );
			} else {
				if (data.quran_level_id != 0) {
					aoData.push( { name: 'quran_level_id', value: data.quran_level_id } );
				}
				if (data.class_level_id != 0) {
					aoData.push( { name: 'class_level_id', value: data.class_level_id } );
				}
			}
		},
		callback: function() {
			$('#student-grid .btn-dashboard').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				var link = web.base + 'home/report/' + record.s_id;
				window.open(link);
			});
			
			$('#student-grid .btn-report').click(function() {
				var raw_record = $(this).siblings('.hide').text();
				eval('var record = ' + raw_record);
				
				// show modal
				Func.populate({ cnt: '#modal-email', record: { to: record.parent_email } });
				$('#modal-email').modal();
			});
		}
	}
	var dt_student = Func.datatable(param_student);
	
	// filter
	$('#cnt-class-filter [name="class_type"]').change(function() {
		var value = $('[name="class_type"]').val();
		
		// show hide
		$('#cnt-class-filter .class-level').hide();
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
	});
	$('#cnt-class-filter .class-level select').change(function() {
		dt_student.reload();
	});
	
	// form modal report
	$('#modal-email form').validate({
		rules: {
			to: { required: true, email: true },
			subject: { required: true },
			message: { required: true }
		}
	});
	$('#modal-email form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-email form').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('modal-email form');
		Func.form.submit({
			url: web.base + 'grade/action',
			param: param,
			callback: function(result) {
				$('#modal-email').modal('hide');
			}
		});
	});
});
</script>

</html>