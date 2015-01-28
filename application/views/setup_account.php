<?php
	$sendgrid = $this->config_model->get_row(array( 'config_key' => 'sendgrid' ));
	$twilio = $this->config_model->get_row(array( 'config_key' => 'twilio' ));
?>

<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">Setup Account</h3>
			
			<div class="box">
				<h4 class="center-title">Sendgrid Account</h4>
				<form id="form-sendgrid" class="form-horizontal" style="margin: 0px;">
					<input type="hidden" name="action" value="sendgrid" />
					
					<div class="control-group">
						<label class="control-label">User</label>
						<div class="controls"><input type="text" name="user" class="span8" placeholder="User" value="<?php echo @$sendgrid['user']; ?>" /></div>
					</div>
					<div class="control-group">
						<label class="control-label">Password</label>
						<div class="controls"><input type="password" name="passwd" class="span8" placeholder="Password" value="<?php echo @$sendgrid['passwd']; ?>" /></div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="submit" class="btn btn-primary" value="Save" />
						</div>
					</div>
				</form>
			</div>
			
			<div class="box">
				<h4 class="center-title">Twilio Account</h4>
				<form id="form-twilio" class="form-horizontal" style="margin: 0px;">
					<input type="hidden" name="action" value="twilio" />
					
					<div class="control-group">
						<label class="control-label">SID</label>
						<div class="controls"><input type="text" name="sid" class="span8" placeholder="SID" value="<?php echo @$twilio['sid']; ?>" /></div>
					</div>
					<div class="control-group">
						<label class="control-label">Token</label>
						<div class="controls"><input type="text" name="token" class="span8" placeholder="Token" value="<?php echo @$twilio['token']; ?>" /></div>
					</div>
					<div class="control-group">
						<label class="control-label">Phone No</label>
						<div class="controls"><input type="text" name="phone_no" class="span8" placeholder="Phone No" value="<?php echo @$twilio['phone_no']; ?>" /></div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="submit" class="btn btn-primary" value="Save" />
						</div>
					</div>
				</form>
			</div>
		</section>
		
		<?php echo $this->load->view( 'common/footer' ); ?>
	</section>
</body>

<script type="text/javascript">
$(document).ready(function() {
	var page = {
		init: function() {
		}
	}
	page.init();
	
	// form
	$('#form-sendgrid').validate({
		rules: {
			user: { required: true },
			passwd: { required: true }
		}
	});
	$('#form-sendgrid').submit(function(e) {
		e.preventDefault();
		if (! $('#form-sendgrid').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('form-sendgrid');
		Func.form.submit({
			url: web.base + 'setup_account/action',
			param: param
		});
	});
	
	// form twilio
	$('#form-twilio').validate({
		rules: {
			sid: { required: true },
			token: { required: true },
			phone: { required: true }
		}
	});
	$('#form-twilio').submit(function(e) {
		e.preventDefault();
		if (! $('#form-twilio').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('form-twilio');
		Func.form.submit({
			url: web.base + 'setup_account/action',
			param: param
		});
	});
});
</script>

</html>