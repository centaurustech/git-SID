<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>SID | <?php echo $x->TableTitle; ?></title>
		<link rel="shortcut icon" href="">

		<link rel="stylesheet" href="resources/initializr/css/flatly.css">
		<link rel="stylesheet" href="resources/lightbox/css/lightbox.css" media="screen">
		<link rel="stylesheet" href="resources/select2/select2.css" media="screen">
		<link rel="stylesheet" href="dynamic.css.php">

		<!--[if lt IE 9]>
			<script src="resources/initializr/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		<![endif]-->
		<script src="resources/jquery/js/jquery-1.10.1.min.js"></script>
		<script>var $j = jQuery.noConflict();</script>
		<script src="resources/initializr/js/vendor/bootstrap.min.js"></script>
		<script src="resources/lightbox/js/prototype.js"></script>
		<script src="resources/lightbox/js/scriptaculous.js?load=effects,builder,dragdrop,controls"></script>
		<script src="resources/lightbox/js/lightbox.js"></script>
		<script src="resources/select2/select2.min.js"></script>
		<script src="common.js.php"></script>

	</head>
	<body>
		<div class="container">
			<?php if(!$_REQUEST['Embedded']){ ?>
				<?php echo htmlUserBar(); ?>
				<div style="height: 70px;" class="hidden-print"></div>
			<?php } ?>

			<!-- process notifications -->
			<?php if(function_exists('showNotifications')) echo showNotifications(); ?>

			<!-- Add header template below here .. -->

