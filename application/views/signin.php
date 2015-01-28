<?php
	$web['base'] = base_url();
?>
<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>JEC Student Dashboard - Sign In</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
	
	<script type="text/javascript">var web = <?php echo json_encode($web); ?></script>
	<script src="<?php echo base_url('static/js/1.3.0/adminflare-demo-init.min.js'); ?>" type="text/javascript"></script>

	<link href="<?php echo base_url('static/fonts/open-sans.css'); ?>" rel="stylesheet" type="text/css">
	<script type="text/javascript">
		// Include Bootstrap stylesheet 
		document.write('<link href="' + web.base + 'static/css/' + DEMO_ADMINFLARE_VERSION + '/' + DEMO_CURRENT_THEME + '/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">');
		// Include AdminFlare stylesheet 
		document.write('<link href="' + web.base + 'static/css/' + DEMO_ADMINFLARE_VERSION + '/' + DEMO_CURRENT_THEME + '/adminflare.min.css" media="all" rel="stylesheet" type="text/css" id="adminflare-css">');
		// Include AdminFlare page stylesheet 
		document.write('<link href="' + web.base + 'static/css/' + DEMO_ADMINFLARE_VERSION + '/pages.min.css" media="all" rel="stylesheet" type="text/css">');
	</script>
	
	<script src="<?php echo base_url('static/js/1.3.0/modernizr-jquery.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('static/js/1.3.0/adminflare-demo.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('static/js/1.3.0/bootstrap.min.js'); ?>" type="text/javascript"></script>
	
	<!-- notify -->
	<script src="<?php echo base_url('static/js/notify.min.js'); ?>"></script>
	
	<!-- validate -->
	<script src="<?php echo base_url('static/js/jquery.validate.min.js'); ?>"></script>
	
	<!-- helper -->
	<script src="<?php echo base_url('static/js/public_function.js'); ?>"></script>
	
	<!--[if lte IE 9]>
		<script src="static/js/jquery.placeholder.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function () {
				$('input, textarea').placeholder();
			});
		</script>
	<![endif]-->
	
	<script type="text/javascript">
		$(document).ready(function() {
			// warning
			var array_link = window.location.href.split('/');
			if (array_link[array_link.length - 1] == 'absent') {
				$.notify('Only teacher allowed to login here', "error");
			}
			
			// show form
			$('#signin-container .show-sign-form').click(function() {
				$('#form-signin').show();
				$('#form-forget').hide();
			});
			$('#signin-container .show-forget-form').click(function() {
				$('#form-signin').hide();
				$('#form-forget').show();
			});
			
			// form sign in
			$('#form-signin').submit(function(e) {
				e.preventDefault();
				
				// param
				var param = Func.form.get_value('form-signin');
				
				// validate
				if (param.user_uname == '' || param.user_pword == '') {
					$.notify('Please enter username & password', "error");
					return false;
				}
				
				// ajax request
				Func.ajax({ url: web.base + 'home/action', param: param, callback: function(result) {
					if (result.status == 1) {
						window.location = window.location.href;
					} else {
						$.notify(result.message, "error");
					}
				} });
			});
			
			// form forget password
			$('#form-forget').submit(function(e) {
				e.preventDefault();
				
				// param
				var param = Func.form.get_value('form-forget');
				
				// validate
				if (param.email == '') {
					$.notify('Please enter your email', "error");
					return false;
				}
				
				// ajax request
				Func.form.submit({
					url: web.base + 'home/action',
					param: param
				});
			});
			
			// update position
			var updateBoxPosition = function() {
				$('#signin-container').css({
					'margin-top': ($(window).height() - $('#signin-container').height()) / 2
				});
			};
			$(window).resize(updateBoxPosition);
			setTimeout(updateBoxPosition, 50);
		});
	</script>
</head>
<body class="signin-page">
	<section id="signin-container">
		<a title="AdminFlare" class="header">
			<img src="<?php echo base_url('static/images/Logo.gif'); ?>" alt="Sign in to Admin Flare">
			<span><strong>&nbsp;</strong></span>
		</a>
		
		<form id="form-signin">
			<input type="hidden" name="action" value="signin" />
			
			<fieldset>
				<div class="fields">
					<input type="text" name="user_uname" placeholder="Username" />
					<input type="password" name="user_pword" placeholder="Password" />
				</div>
				<a href="#" class="forgot-password show-forget-form">Forgot?</a>
				<button type="submit" class="btn btn-primary btn-block">Sign In</button>
			</fieldset>
		</form>
		
		<form id="form-forget" class="hide">
			<input type="hidden" name="action" value="forget_password" />
			
			<fieldset>
				<div class="fields">
					<input type="text" name="email" placeholder="Email" />
					<input type="password" disabled="disabled" />
				</div>
				<a href="#" class="forgot-password show-sign-form">Sign In?</a>
				
				<button type="submit" class="btn btn-primary btn-block">Request Password</button>
			</fieldset>
		</form>
		
		<div class="social hide">
			<p>...or sign in with</p>
			<a href="dashboard.html" tabindex="5" class="twitter">
				<i class="icon-twitter"></i>
			</a>

			<a href="dashboard.html" tabindex="6" class="facebook">
				<i class="icon-facebook"></i>
			</a>

			<a href="dashboard.html" tabindex="7" class="google">
				<i class="icon-google-plus"></i>
			</a>
		</div>
	</section>
	
</body>
</html>