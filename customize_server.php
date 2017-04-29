<?php 
	include 'core/init.php';
	include 'includes/overall/header.php'; 
	protect_page();


if(isset($_GET['id']) && !empty($_GET['id'])){
	
	$id 	= (INT)$_GET['id'];
	$query 	= $database->query("SELECT `ip` FROM `servers` WHERE `id` = '$id'");
	$fetch 	= $query->fetch_assoc();
	$ip 	= $fetch['ip'];
	
	$domain = $_SERVER['HTTP_HOST'];
	$folder = basename(dirname(__FILE__));
	$link   = "http://" . $domain ."/".$folder;
 
	if(id_to_user_id($id) !== $_SESSION['user_id']){
		$errors[] = 'You don\'t own this server, sorry!';
	}

	if(!empty($errors)){
		echo output_errors($errors);
	} elseif(empty($errors)) {
?>
	<script type="text/javascript" src="includes/jscolor/jscolor.js"></script>

	<h2>Customize Server <?php echo $ip; ?></h2>
	<table>
	<tr>
		<td>Top Gradient</td>
		<td><input id="top_color" class="color" value="692108" default="692108"/></td>
	</tr>
	<tr>
		<td>Bottom Gradient</td>
		<td><input id="bottom_color" class="color" value="381007" default="381007"/></td>
	</tr>
	<tr>
		<td>Text Color</td>
		<td><input id="font_color" class="color" value="ffffff" default="ffffff"/></td>
	</tr>
	<tr>
		<td>Border Color</td>
		<td><input id="border_color" class="color" value="000000" default="000000"/></td>
	</tr>
	<input id="link" type="hidden" value="<?php echo $link; ?>" />
</table>
<br /><br />
	<img id="small_image"  sid="<?php echo $id; ?>" src="dynamic_image.php?s=<?php echo $id; ?>&type=small&top=692108&bottom=381007&font=ffffff&border=000000&time=<?php echo mt_rand(100000,900000); ?>"/>
	
	<h3 style="margin-top: 25px;">BB/HTML Code </h3>
	<textarea id="bb_small_code" rows="3" style="width: 95%;">[url=<?php echo $link; ?>/server.php?id=<?php echo $id; ?>][img]<?php echo $link; ?>/dynamic_image.php?s=<?php echo $id; ?>&type=small&top=692108&bottom=381007&font=ffffff&border=000000[/img][/url]</textarea>
	<textarea id="html_small_code" rows="3" style="width: 95%;"><a href="<?php echo $link; ?>/server.php?id=<?php echo $id; ?>"><img src="<?php echo $link; ?>/dynamic_image.php?s=<?php echo $id; ?>&type=small&top=692108&bottom=381007&font=ffffff&border=000000"></a></textarea>
	<br /><br />
	<img src="dynamic_image.php?s=<?php echo $id; ?>&type=background" />
	<h3 style="margin-top: 25px;">BB/HTML Code </h3>
	<textarea id="bb_small_code" rows="3" style="width: 95%;">[url=<?php echo $link; ?>/server.php?id=<?php echo $id; ?>][img]<?php echo $link; ?>/dynamic_image.php?s=<?php echo $id; ?>&type=background[/img][/url]</textarea>
	<textarea id="html_small_code" rows="3" style="width: 95%;"><a href="<?php echo $link; ?>/server.php?id=<?php echo $id; ?>"><img src="<?php echo $link; ?>/dynamic_image.php?s=<?php echo $id; ?>&type=background"></a></textarea>

	<?php
	}
}

?>
<?php include 'includes/overall/footer.php'; ?>