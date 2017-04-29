<!DOCTYPE html>
<html>
<?php include 'includes/head.php'; ?>
<body>
    <?php include 'includes/header.php'; ?>
	<div class="container" style="margin-top: 25px;">
		<div class="row-fluid">
		<div class="span10">
		<?php if($settings['advertise_top'] !== 'false'){ ?>
			<div class="well well-small" style="text-align:center;">
				<?php echo urldecode($settings['advertise_top']); ?>
			</div>
		<?php } ?>
		<?php if(current_page_name() == "index.php"){ include 'includes/hero-unit.php'; } else { ?><div class="well"><?php } ?>