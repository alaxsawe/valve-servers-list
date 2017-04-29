<?php 
include 'core/init.php';
protect_page();
admin_page();
include 'includes/overall/header.php'; 

if(isset($_GET['delete']) && !empty($_GET['delete'])){

	$user_id = (INT)$_GET['delete'];
	$database->query("DELETE FROM `users` WHERE `user_id` = $user_id");
	$database->query("DELETE FROM `servers` WHERE `user_id` = $user_id");
	echo "<h1>Delete User</h1>";
	echo "<p>The user with the id of <b>" . $user_id . "</b> and his servers, were deleted successfully!";
	
} elseif(isset($_GET['status']) && !empty($_GET['status'])) {

	$user_id = (INT)$_GET['status'];
	$status  = $database->query("SELECT `active` FROM `users` WHERE `user_id` = $user_id")->fetch_object()->active;
	if($status == 1){
		$database->query("UPDATE `users` SET `active` = 0 WHERE `user_id` = $user_id");
		echo "<h1>User Deactivated</h1>";
		echo "<p>The user with the id of <b>" . $user_id . "</b>, was deactivated successfully!";
	}	
	if($status == 0){
		$database->query("UPDATE `users` SET `active` = 1 WHERE `user_id` = $user_id");
		echo "<h1>User Activated</h1>";
		echo "<p>The user with the id of <b>" . $user_id . "</b>, was activated successfully!";
	}
} else {
?>
<h2>Users Management</h2>
<?php 
if(isset($_GET['successUpdate']) && empty($_GET['successUpdate'])) {
	echo '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Congratulations!</strong> The changes have been made!
		</div>';
}
?>
<table class="table table-bordered" style="background:white;">
	<thead>
		<tr>
			<th>Username</th>
			<th>Email</th>
			<th>Name</th>
			<th>IP</th>
			<th>Reg.Date</th>
			<th>Status</th>
			<th>Settings</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$result = $database->query("SELECT `user_id`, `username`, `email`, `ip`, `name`, `type`, `active`, `date` FROM `users` ORDER BY `type` DESC");
		while ($row = $result->fetch_assoc()) {
		$username = $row['username'];
		$email    = $row['email'];
		$name     = $row['name'];
		$type     = $row['type'];
		$ip       = $row['ip'];
		$date     = $row['date'];
		$status   = $row['active'];
		?>
		
		<tr>
			<td><a href="profile-<?php echo $username ?>"><?php echo $username ?></a><?php if($type == 1) echo "<sup>admin</sup>"; ?></td>
			<td><?php echo $email; ?></td>
			<td><?php echo $name; ?></td>
			<td><?php echo $ip; ?></td>
			<td><?php echo $date; ?></td>
			<td>
				<?php if($status == 0){ ?>
				<a href="adm_users_management.php?status=<?php echo $row['user_id']; ?>">
					<font color="green">Activate</font>
				</a>
				<?php } elseif($status == 1) { ?>
				<a href="adm_users_management.php?status=<?php echo $row['user_id']; ?>">
					<font color="red">Deactivate</font>
				</a>
				<?php } ?>
			</td>
			<td>	
				<a href="adm_users_management.php?delete=<?php echo $row['user_id']; ?>">
					<img alt='' src='includes/img/delete.png' title='Delete user' />
				</a>
				<a href="adm_user_settings.php?update=<?php echo $row['user_id']; ?>">
					<img alt='' src='includes/img/settings.png' title='Change User Settings' />
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