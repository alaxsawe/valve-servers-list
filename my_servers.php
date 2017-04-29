<?php 
	include 'core/init.php';
	include 'includes/overall/header.php'; 
	protect_page();
?>
<h2>My servers list</h2>
<?php
if(isset($_GET['successUpdate'])){
	echo '<div class="alert alert-success">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Congratulations!</strong> Your server has been successfully updated!
		  </div>';
}
if(isset($_GET['delete']) && !empty($_GET['delete'])){
	
	$server_id = (INT)$_GET['delete'];
	
	if(id_to_user_id($server_id) !== $_SESSION['user_id']){
		$errors[] = 'You don\'t own this server, sorry!';
	}

	if(empty($errors)){
		$database->query("DELETE FROM `servers` WHERE `id` = $server_id");
		$database->query("DELETE FROM `comments` WHERE `server_id` = $server_id");
		echo '<div class="alert alert-success">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  The server with the id of <b>' . $server_id . '</b>, was deleted successfully! <a href="my_servers.php">Go back</a>
		  </div>';
	} elseif(!empty($errors)) {
		echo output_errors($errors);
	}
	
} else {
$disabled = $database->query("SELECT COUNT(`user_id`) AS `count` FROM `servers` WHERE `user_id` = ". $_SESSION['user_id'] . " AND `disabled` = 1")->fetch_object()->count;
$active   = $database->query("SELECT COUNT(`user_id`) AS `count` FROM `servers` WHERE `user_id` = ". $_SESSION['user_id'] . " AND `disabled` = 0")->fetch_object()->count;
?>	

<p>I have a total of <?php echo $active; ?> active servers and <?php echo $disabled; ?> disabled servers added!</p>
<?php
	if($active > 0){
?>
	<table class="table table-bordered" style="background:white;">
		<thead>
			<tr>
				<th>Status</th>
				<th>Name</th>
				<th>Server Informations</th>
				<th>Players</th>
				<th>Map</th>
				<th>Votes</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$result  = $database->query("SELECT `id`, `user_id`, `ip`, `game`, `port`, `status`, `country`, `vip`, `name`, `votes`, `map`, `players`, `maxPlayers`, `cache_time` FROM `servers` WHERE `disabled` = 0 AND `user_id` = $session_user_id  ORDER BY `vip` DESC, `votes` DESC, `cache_time` ASC");
			while ($server_data = $result->fetch_assoc()) {
				$server_id = $server_data['id'];
				$status    = $server_data['status'];
			?>		
					<tr>
						<td>
							<?php if($status == 1) echo "<span class='label label-success'>Online</span>"; else echo "<span class='label label-warning'>Offline</span>"; ?>
							<?php if(file_exists("includes/game_icons/" . $server_data['game'] . ".gif")) echo "<img src='includes/game_icons/" . $server_data['game'] . ".gif' title='" . $server_data['game'] . "'/>"; ?>
							<?php if($server_data['country'] !== 'XX') { ?><img src="includes/locations/<?php echo $server_data['country']; ?>.png" alt="country" title="<?php echo $server_data['country']; ?>"/><?php } ?>
						</td>

						<td>
							<?php
								if(server_vip($server_data['id']) == 1){
									echo "<font color=\"#FFAC1E\">" . $server_data['name'] . "</font>"; 
								} else {
									echo $server_data['name'];
								}
							?>
						</td>
						<td><?php echo $server_data['ip'] . ":" . $server_data['port']; ?></td>

						<td><?php if($status == 1){echo $server_data['players'] . "/" . $server_data['maxPlayers']; } else { echo "0/0"; } ?></td>
						<td><?php if($status == 1){echo $server_data['map']; } else { echo "Last map: " . $server_data['map']; } ?></td>
						<td><?php echo $server_data['votes']; ?></td>
						</td>
						<td>
							<a href="customize_server.php?id=<?php echo $server_id; ?>"><span class="label label-warning"><i class="icon-film icon-white"></i></span></a>
							<a href="server.php?id=<?php echo $server_data['id']; ?>"><span class="label label-info"><i class="icon-search icon-white"></i></span></a>
							<a href="changeserver.php?id=<?php echo $server_data['id']; ?>"><span class="label label-inverse"><i class="icon-wrench icon-white"></i></span></a>
							<a href="my_servers.php?delete=<?php echo $server_id; ?>"><span class="label label-important"><i class="icon-remove icon-white"></i></span></a>
						</td>
					</tr>
				<?php }	?>
		</tbody>
	</table>

<?php
	}
}
include 'includes/overall/footer.php'; 
?>