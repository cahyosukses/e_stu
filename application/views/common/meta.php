<?php
	$parklet = (isset($parklet)) ? $parklet : false;
	
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
	<title>Jafaria Education Center - Dashboard</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
	
	<script type="text/javascript">var web = <?php echo json_encode($web); ?></script>
	<script src="<?php echo base_url('static/js/1.3.0/adminflare-demo-init.min.js'); ?>" type="text/javascript"></script>
	
	<link href="<?php echo base_url('static/fonts/open-sans.css'); ?>" rel="stylesheet" type="text/css">
	<?php if ($parklet) { ?>
	<link href="<?php echo base_url('static/css/parklet-apps.css'); ?>" media="all" rel="stylesheet" type="text/css" />
	<?php } ?>
	
	<script type="text/javascript">
		// Include Bootstrap stylesheet 
//		document.write('<link href="' + web.base + 'static/css/' + DEMO_ADMINFLARE_VERSION + '/' + DEMO_CURRENT_THEME + '/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">');
		// Include AdminFlare stylesheet 
//		document.write('<link href="' + web.base + 'static/css/' + DEMO_ADMINFLARE_VERSION + '/' + DEMO_CURRENT_THEME + '/adminflare.min.css" media="all" rel="stylesheet" type="text/css" id="adminflare-css">');
	</script>
	<link href="<?php echo base_url('static/css/1.3.0/black-blue/bootstrap.min.css'); ?>" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
	<link href="<?php echo base_url('static/css/1.3.0/black-blue/adminflare.min.css'); ?>" media="all" rel="stylesheet" type="text/css" id="adminflare-css">
	
	<script src="<?php echo base_url('static/js/1.3.0/modernizr-jquery.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('static/js/1.3.0/adminflare-demo.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('static/js/1.3.0/bootstrap.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('static/js/1.3.0/adminflare.min.js'); ?>" type="text/javascript"></script>
	
	<!-- dataTables -->
	<link href="<?php echo base_url('static/js/datatables/css/dataTables.bootstrap.css'); ?>" rel="stylesheet" type="text/css" />
	<script src="<?php echo base_url('static/js/datatables/js/jquery.dataTables.js'); ?>"></script>
	
	<!-- notify -->
	<script src="<?php echo base_url('static/js/notify.min.js'); ?>"></script>
	
	<!-- validate -->
	<script src="<?php echo base_url('static/js/jquery.validate.min.js'); ?>"></script>
	
	<!-- typeahead -->
	<link rel="stylesheet" href="<?php echo base_url('static/js/typeahead/examples.css'); ?>">
	<script src="<?php echo base_url('static/js/typeahead/handlebars.js'); ?>"></script>
	<script src="<?php echo base_url('static/js/typeahead/typeahead.bundle.js'); ?>"></script>
	<script src="<?php echo base_url('static/js/typeahead/examples.js'); ?>"></script>
	
	<!-- signature -->
	<link href="<?php echo base_url('static/js/signature/assets/jquery.signaturepad.css'); ?>" rel="stylesheet">
	<!--[if lt IE 9]><script src="<?php echo base_url('static/js/signature/assets/flashcanvas.js'); ?>"></script><![endif]-->
	<script src="<?php echo base_url('static/js/signature/jquery.signaturepad.js'); ?>"></script>
	
	<!-- helper -->
	<link href="<?php echo base_url('static/css/style.css'); ?>" rel="stylesheet" type="text/css">
	<script src="<?php echo base_url('static/js/public_function.js?unix='.time()); ?>"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {
			prettyPrint();
		});
	</script>
</head>