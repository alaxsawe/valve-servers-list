<?php 
include 'core/init.php';
include 'includes/overall/header.php'; 


if(servers_count() < 1) {
	echo "<br />";
	include 'includes/overall/footer.php'; 
	die();
}
?>

<table class="table table-stripped" >
	<thead>
		<th></th>
		<th></th>
		<th>Server Informations</th>
		<th>Name</th>
		<th>Players</th>
		<th>Map</th>
		<th>Votes</th>
		<th>Status</th>
	</thead>
	<tbody>
<?php

/* Pagination Settings */
$page = 1;
if(isset($_GET['page'])) { $page = $_GET['page']; } 
$limitT 		= (($page * $settings['pagination']) - $settings['pagination']); 
$pagination = $settings['pagination'];


/* Query options */
$offline_filter = $game_filter = $country_filter = $map_filter = "";

$order = "`votes` DESC";
if(isset($_GET['sort'])){
	switch($_GET['sort']){
		case "players" : $order = "`players` DESC"; break;
		case "status"  : $order = "`status` DESC";  break;
		case "votes"   : $order = "`votes` DESC";   break;
	}
}

if($settings['show_offline_servers'] == '0') $offline_filter = "AND `status` = 1"; 

if(isset($_GET['country'])) { 
	$country = sanitize($_GET['country']);
	$country_filter = "AND `country` = '{$country}'";
}

if(isset($_GET['map'])) { 
	$map = sanitize($_GET['map']);
	$game_filter = "AND `map` = '{$map}'";
}

if(isset($_GET['game'])) { 
	$game = sanitize($_GET['game']);
	$game_filter = "AND `game` = '{$game}'";
}

/* Ranking settings so it doesnt loses the count on the next page */
$rank = 1;
if($page > 1){
	$rank = $page * $pagination - $pagination + 1;
}

/* Setting the maximum live queries to be made on page refresh, preventing long execution time errors and improving performance */
$max_live_queries = 1;
$query_nr = 1;

$result  = $database->query("SELECT `id`, `user_id`, `ip`, `game`, `port`, `status`, `country`, `vip`, `name`, `votes`, `map`, `players`, `maxPlayers`, `cache_time` FROM `servers` WHERE `disabled` = '0' {$offline_filter} {$game_filter} {$country_filter} {$map_filter} ORDER BY `vip` DESC, {$order}, `cache_time` ASC LIMIT $limitT, $pagination");
while ($server_data = $result->fetch_array()) {
	$server_id = $server_data['id'];
	$info = new stdClass();

	if($server_data['cache_time'] > time() - $settings['server_cache'] || $query_nr > $max_live_queries || $settings['disable_index_querying'] == 1){
		$status             = $server_data['status'];

		if($status == 1){
			$info->Map			= $server_data['map'];
			$info->PlayerCount	= $server_data['players'];
			$info->MaxPlayers	= $server_data['maxPlayers'];
		} elseif ($status == 0) {
			$info->Map			= "";
			$info->PlayerCount	= "";
			$info->MaxPlayers	= "";
		}

	} else {
	
		try {
			$Query = new LiveStats($server_data['ip'], $server_data['port']);
			$info = $Query->GetServer();
		}catch (LSError $e) {}

		if(empty($e)){ $status = 1; } else { $status = 0; }
			

		if($status == 1) {
			$database->query("UPDATE `servers` SET `map` = '{$info->Map}', `players` = '{$info->PlayerCount}', `maxPlayers` = '{$info->MaxPlayers}' WHERE `id` = '$server_id'");
		}

		$database->query("UPDATE `servers` SET `cache_time` = unix_timestamp(), `status` = '$status' WHERE `id` = '$server_id'");
		
		$query_nr++;
	}
?>		
		<tr>
			<td>
				<?php if($server_data['vip'] == "1"){ ?>
				<i class="icon-star"></i>
				<?php } else { ?>
				<span class="muted">#<?php echo $rank; ?></span>
				<?php } ?>
			</td>
			<td>
				<?php if($server_data['game'] !== "samp") echo '<a href="steam://connect/' . $server_data['ip'] . ':' . $server_data['port'] .'"><img src="includes/img/steam.png" alt="Connect" /></a>'; ?>
				<?php if(file_exists("includes/game_icons/" . $server_data['game'] . ".gif")) echo "<img src='includes/game_icons/" . $server_data['game'] . ".gif' title='" . $server_data['game'] . "'/>"; ?>
				<?php if($server_data['country'] !== 'XX') { ?><img src="includes/locations/<?php echo $server_data['country']; ?>.png" alt="country" title="<?php echo $server_data['country']; ?>"/><?php } ?>
			</td>
			<td>
				<a href="server.php?id=<?php echo $server_data['id']; ?>" >
				<?php 
					echo strtolower($server_data['ip']);
					if($server_data['port'] !== "27015") echo ":" . $server_data['port']; 
				?>
				</a>
			</td>
			</td>
			<td><?php echo $server_data['name']; ?></td>
			
			<td><?php if($status == 1){echo $info->PlayerCount . "/" . $info->MaxPlayers; } else { echo "server offline"; } ?></td>
			<td><?php if($status == 1){echo $info->Map; } else { echo "server offline"; } ?></td>
			<td><?php echo $server_data['votes']; ?></td>
			<td>
				<?php if($status == 1) echo "<span class='label label-success'>Online</span>"; else echo "<span class='label label-warning'>Offline</span>"; ?>
				<?php if(logged_in() && is_admin($session_user_id)) { ?>
					&nbsp;
					<a href="adm_servers_management.php?vip=<?php echo $server_id; ?>" title="VIP">
						<span class="label label-info">
							<i class="icon-star icon-white"></i>
						</span>
					</a>&nbsp;
					
					<a href="adm_servers_management.php?status=<?php echo $server_id; ?>" title="Disable">
						<span class="label label-important">
							<i class="icon-off icon-white"></i>
						</span>
					</a>&nbsp;
					
					<a href="adm_servers_management.php?delete=<?php echo $server_id; ?>" title="Delete">
						<span class="label label-important">
							<i class="icon-remove icon-white"></i>
						</span>
					</a>	
			<?php } ?>
			</td>
		</tr>
<?php $rank++;} ?>
	</tbody>
</table>

<?php
include "pagination.php";
include 'includes/overall/footer.php'; 
?>