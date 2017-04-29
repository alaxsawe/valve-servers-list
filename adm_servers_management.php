<?php 
include 'core/init.php';
protect_page();
admin_page();
include 'includes/overall/header.php'; 

if(isset($_GET['delete']) && !empty($_GET['delete'])){
	
	$server_id = (INT)$_GET['delete'];
	$database->query("DELETE FROM `servers` WHERE `id` = $server_id");
	$database->query("DELETE FROM `comments` WHERE `server_id` = $server_id");
	header('Location: adm_servers_management.php');
	
} elseif(isset($_GET['status']) && !empty($_GET['status'])) {

	$server_id = (INT)$_GET['status'];
	$status    = $database->query("SELECT `disabled` FROM `servers` WHERE `id` = $server_id")->fetch_object()->disabled;
	if($status == 0){
		$database->query("UPDATE `servers` SET `disabled` = 1 WHERE `id` = $server_id");
		header('Location: adm_servers_management.php');
	}	
	if($status == 1){
		$database->query("UPDATE `servers` SET `disabled` = 0 WHERE `id` = $server_id");
		header('Location: adm_servers_management.php');
	}
	
} elseif(isset($_GET['vip']) && !empty($_GET['vip'])) { 

	$server_id = (INT)$_GET['vip'];
	$vip       = $database->query("SELECT `vip` FROM `servers` WHERE `id` = $server_id")->fetch_object()->vip;
	if($vip == 0){
		$database->query("UPDATE `servers` SET `vip` = 1 WHERE `id` = $server_id");
		header('Location: adm_servers_management.php');
	}	
	if($vip == 1){
		$database->query("UPDATE `servers` SET `vip` = 0 WHERE `id` = $server_id");
		header('Location: adm_servers_management.php');
	}
	
} else {
?>
<h2>Servers Management</h2>

<table class="table table-bordered" style="background:white;">
	<thead>
		<tr>
			<th>Name</th>
			<th>Ip : Port</th>
			<th>Votes</th>
			<th>Added By</th>
			<th>Status</th>
			<th>Settings</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$result = $database->query("SELECT `id`, `user_id`, `ip`, `port`, `vip`, `disabled`, `votes`, `name` FROM `servers` ORDER BY `user_id` DESC");
		while ($row = $result->fetch_assoc()) {
		$addedBy = username_from_user_id($row['user_id']);
		$status  = $row['disabled'];
		$vip 	 = $row['vip'];
		?>
		
		<tr>
			<td><a href="server.php?id=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></td>
			<td><?php echo $row['ip'] . " : " . $row['port']; ?></td>
			<td><?php echo $row['votes']; ?></td>
			<td><a href="profile-<?php echo $addedBy; ?>"><?php echo $addedBy; ?></a></td>
			<td>
				<?php if($status == 1){ ?>
				<a href="adm_servers_management.php?status=<?php echo $row['id']; ?>">
					<font color="green">Activate</font>
				</a>
				<?php } elseif($status == 0) { ?>
				<a href="adm_servers_management.php?status=<?php echo $row['id']; ?>">
					<font color="red">Deactivate</font>
				</a>
				<?php } ?>
			</td>
			<td>	
				<?php if($vip == 1){ ?>
				<a href="adm_servers_management.php?vip=<?php echo $row['id']; ?>">
					<span class="label label-warning"><i class="icon-star icon-white"></i></span>
				</a>
				<?php } elseif($vip == 0) { ?>
				<a href="adm_servers_management.php?vip=<?php echo $row['id']; ?>">
					<span class="label label-warning"><i class="icon-star-empty icon-white"></i></span>
				</a>
				<?php } ?>
				
				
				<a href="adm_servers_management.php?delete=<?php echo $row['id']; ?>">
					<span class="label label-important"><i class="icon-remove icon-white"></i></span>
				</a>
			</td>
		</tr>
		
		<?php
		}
		?>
	</tbody>
</table>

<?php
}
include 'includes/overall/footer.php'; 
?>