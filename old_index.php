<?php 
include 'core/init.php';
include 'includes/overall/header.php'; 
?>

<div class="frontBG" style="padding: 25px;">
	<div class="pull-left" style="margin-right:15px;">
		<img src="includes/img/mc-head.png" width="180" height="180" alt="Minecraft" />
	</div>
	<div class="container">
		<h1><?php echo $settings['title']; ?></h1>
		<p style="font-size:16px;">We currently have <?php echo servers_count(); ?> servers added in our database by a total of <?php echo user_count(); ?> successfully registered users.
		<br />On the website there are currently <?php echo online_guests(); ?> guests and <a href="online_users.php"><?php echo online_users(); ?></a> users online, take your time to register on this beautiful website.
		</p>			
	</div>
</div>
<?php if(servers_count() < 1) {
	echo "<br />";
	include 'includes/overall/footer.php'; 
	die();
}
?>
<a href="#" id="example" class="btn btn-success" rel="popover" data-content="It's so simple to create a tooltop for my website!" data-original-title="Twitter Bootstrap Popover">hover for popover</a>
<h2>Sponsored servers</h2>
<script>
$(function ()
{ $("#example").popover();
});
</script>
<?php
$result  = $database->query("SELECT `id`, `user_id`, `ip`, `port`, `vip`, `status`, `banner`, `name`, `votes`, `cache`, `cache_time` FROM `servers` WHERE `vip` = '1' AND `disabled` = '0'");
while ($server_data = mysql_fetch_array($result)) {
	$server_id = $server_data['id'];
	$info = array();
	$last_update = time() - $server_data['cache_time'];
	$last_updateM = intval($last_update/60);
	if($server_data['cache_time'] > time() - $settings['server_cache']){
		$server_cache_data  = $server_data['cache'];
		$server_cache_data  = explode("-del-", $server_cache_data);
		$status             = $server_data['status'];
		if($status == 1){
			$info['HostName']   = string_resize($server_cache_data[0], 16);
			$info['Players']    = $server_cache_data[1];
			$info['MaxPlayers'] = $server_cache_data[2];
		} elseif ($status == 0) {
			$info['HostName']  	 = "Server Offline";
			$info['Players']	 = "";
			$info['MaxPlayers']	 = "";
		}
	} else {
		//Server checks
		$info = QueryMinecraft($server_data['ip'], $server_data['port']);
		
		//Check status of the server
		if($info['status'] == 1){ $status = 1; } else { $status = 0; }

		
		//If hostname / mapname too big
		if($status == 1){
			$originalHostName = $info['HostName'];
			$info['HostName'] = string_resize($info['HostName'], 16);
			//Update cache in database
			$cache = $originalHostName . "-del-" . $info['Players'] . "-del-" . $info['MaxPlayers'];
		} elseif($status == 0){
			$cache = $status . "-del--del-";
		}
		$cache = trim($cache);
		$database->query("UPDATE `servers` SET `cache` = '$cache' WHERE `id` = '$server_id'");
		//Update cache time
		$database->query("UPDATE `servers` SET `cache_time` = unix_timestamp() WHERE `id` = '$server_id'");
		//Update cache status of the server
		$database->query("UPDATE `servers` SET `status` = '$status' WHERE `id` = '$server_id'");
	}
	?>
	
<table class="table table-bordered table-stripped" style="background:white;">
	<tbody>			
		<tr>
			<td style="width:600px;">
				<?php if($status == 1){	?>
				<span class="badge badge-success"><i class="icon-ok icon-white"></i></span>&nbsp;
				<?php } else { ?>
				<span class="badge badge-important"><i class="icon-remove icon-white"></i></span>&nbsp;
				<?php } ?>
				<?php echo $server_data['ip'] . ":" . $server_data['port']; ?><br />
				
				<?php if($server_data['banner'] !== ''){ ?>
				<img class="img-polaroid" style="margin-top:5px;" src="<?php echo $server_data['banner']; ?>" alt="minecraft banner"/>
				<?php } else { ?>
				<img class="img-polaroid" style="margin-top:5px;" src="dynamic_image.php?s=<?php echo $server_id; ?>&top=692108&bottom=381007&font=FFFFFF&border=000000&type=background" alt="dynamic minecraft banner"/>
				<?php } ?> 
			</td>
			<td style="width: 265px;">
				<strong>Name:</strong> <?php echo $server_data['name']; ?><br />
				<strong>Online players:</strong> <?php if($status == 1){echo $info['Players'] . "/" . $info['MaxPlayers']; } else { echo "server offline"; } ?><br />
				<strong>Last update: </strong><?php echo $last_updateM; ?> minutes ago<br />
				<strong>Votes:</strong> <?php echo $server_data['votes']; ?>
			</td>
			<td style="vertical-align:middle;"><a href="server.php?id=<?php echo $server_data['id']; ?>"><img src="includes/img/icon_details.gif" title="Server Informations" alt="details"/></a></td>
		</tr>
	</tbody>
</table>
<?php } ?>


<h2>All servers</h2>
<?php
//Pagination
$page = 1;
if(isset($_GET['page'])) { $page = $_GET['page']; } 
$limitT 		= (($page * $settings['pagination']) - $settings['pagination']); 
$pagination = $settings['pagination'];

$offline_where = "";
if($settings['show_offline_servers'] == '0'){
	$offline_where = "AND `status` = 1"; 
}	
$result  = $database->query("SELECT `id`, `user_id`, `ip`, `port`, `vip`, `status`, `banner`, `name`, `votes`, `cache`, `cache_time` FROM `servers` WHERE `vip` = 0 {$offline_where} AND `disabled` = '0' ORDER BY `votes` DESC LIMIT $limitT, $pagination");
while ($server_data = mysql_fetch_array($result)) {
	$server_id = $server_data['id'];
	$info = array();
	$last_update = time() - $server_data['cache_time'];
	$last_updateM = intval($last_update/60);
	if($server_data['cache_time'] > time() - $settings['server_cache']){
		$server_cache_data  = $server_data['cache'];
		$server_cache_data  = explode("-del-", $server_cache_data);
		$status             = $server_data['status'];
		if($status == 1){
			$info['HostName']   = string_resize($server_cache_data[0], 16);
			$info['Players']    = $server_cache_data[1];
			$info['MaxPlayers'] = $server_cache_data[2];
		} elseif ($status == 0) {
			$info['HostName']  	 = "Server Offline";
			$info['Players']	 = "";
			$info['MaxPlayers']	 = "";
		}
	} else {
		//Server checks
		$info = QueryMinecraft($server_data['ip'], $server_data['port']);
		
		//Check status of the server
		if($info['status'] == 1){ $status = 1; } else { $status = 0; }

		
		//If hostname / mapname too big
		if($status == 1){
			$originalHostName = $info['HostName'];
			$info['HostName'] = string_resize($info['HostName'], 16);
			//Update cache in database
			$cache = $originalHostName . "-del-" . $info['Players'] . "-del-" . $info['MaxPlayers'];
		} elseif($status == 0){
			$cache = $status . "-del--del-";
		}
		$cache = trim($cache);
		$database->query("UPDATE `servers` SET `cache` = '$cache' WHERE `id` = '$server_id'");
		//Update cache time
		$database->query("UPDATE `servers` SET `cache_time` = unix_timestamp() WHERE `id` = '$server_id'");
		//Update cache status of the server
		$database->query("UPDATE `servers` SET `status` = '$status' WHERE `id` = '$server_id'");
	}
	?>
	
<table class="table table-bordered table-stripped" style="background:white;">
	<tbody>			
		<tr>
			<td style="width:600px;">
				<?php if($status == 1){	?>
				<span class="badge badge-success"><i class="icon-ok icon-white"></i></span>&nbsp;
				<?php } else { ?>
				<span class="badge badge-important"><i class="icon-remove icon-white"></i></span>&nbsp;
				<?php } ?>
				<?php echo $server_data['ip'] . ":" . $server_data['port']; ?><br />
				
				<?php if($server_data['banner'] !== ''){ ?>
				<img class="img-polaroid" style="margin-top:5px;" src="<?php echo $server_data['banner']; ?>" alt="minecraft banner"/>
				<?php } else { ?>
				<img class="img-polaroid" style="margin-top:5px;" src="dynamic_image.php?s=<?php echo $server_id; ?>&top=692108&bottom=381007&font=FFFFFF&border=000000&type=background" alt="dynamic minecraft banner" />
				<?php } ?> 
			</td>
			<td style="width: 265px;">
				<strong>Name:</strong> <?php echo $server_data['name']; ?><br />
				<strong>Online players:</strong> <?php if(!empty($info['Players'])) {echo $info['Players'] . "/" . $info['MaxPlayers'];} else { echo "Offline"; } ?><br />
				<strong>Last update: </strong><?php echo $last_updateM; ?> minutes ago<br />
				<strong>Votes:</strong> <?php echo $server_data['votes']; ?>
			</td>
			<td style="vertical-align:middle;"><a href="server.php?id=<?php echo $server_data['id']; ?>"><img src="includes/img/icon_details.gif" title="Server Informations" alt="details" /></a></td>
		</tr>
	</tbody>
</table>
<?php } ?>
<?php

include "pagination.php";
include 'includes/overall/footer.php'; 
?>