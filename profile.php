<?php 
	include 'core/init.php';
	include 'includes/overall/header.php';

if(isset($_GET['username']) == true && empty($_GET['username']) == false){
	$username = $_GET['username'];
	if(user_exists($username)){
		$user_id      = user_id_from_username($username);
		$profile_data = user_data($user_id, 'name', 'username', 'active', 'date', 'avatar');
		$servers_added = $database->query("SELECT COUNT(`user_id`) AS `count` FROM `servers` WHERE `user_id` = '$user_id'")->fetch_object()->count;
?>
		<h2><?php echo $profile_data['name']; ?>'s profile</h2>
		<table>
			<tr>
				<td>
					<?php if(empty($profile_data['avatar']) == false) {
						echo "<img src='" . $profile_data['avatar'] . "' alt='" . $profile_data['name'] . "' />";
					}
					?>
				</td>
				<td style="padding-left: 10px;">
					<ul style="list-style-type: none">
						<li><b>Username:</b> <?php echo $profile_data['username']; ?></li>
						<li><b>Registration Date:</b> <?php echo $profile_data['date']; ?></li>
						<li><b>Servers Added:</b> <?php echo $servers_added; ?></li>
						<?php if(logged_in() && is_admin($session_user_id)){ ?>
						<li>
							<a href="adm_user_settings.php?update=<?php echo $user_id; ?>">
								<span class="label label-info">
									<i class="icon-wrench icon-white"></i> Edit User
								</span>
							</a>&nbsp;
							
							<a href="adm_user_settings.php?delete=<?php echo $user_id; ?>">
								<span class="label label-important">
									<i class="icon-remove icon-white"></i> Delete User
								</span>
							</a>
						</li>
						<?php } ?>
					</ul>
				</td>
			</tr>
		</table>
		<br />
		<?php if($servers_added > 0){ ?>
			<table class="table table-bordered" style="background:white;">
				<thead>
					<tr>
						<th>Ip</th>
						<th>Status</th>
						<th>details</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$result = $database->query("SELECT `id`, `user_id`, `ip`, `vip`, `disabled` FROM `servers` WHERE `user_id` = '$user_id' ORDER BY `user_id` DESC");
					while ($row = $result->fetch_assoc()) {
					$addedBy = username_from_user_id($row['user_id']);
					$status  = $row['disabled'];
					$vip 	 = $row['vip'];
					?>
					
					<tr>
						<td><?php if($vip == 1) echo "<font color='#ffac1e'>" . $row['ip'] . "</font>"; else echo $row['ip'] ?></td>
						<td><?php if($status == 1) echo "Unactive server"; else echo "Active server"; ?></td>
						<td><a href="server.php?id=<?php echo $row['id']; ?>"><img src="includes/img/icon_details.gif" title="Server Informations" /></a></td>

					</tr>
					
					<?php
					}
					?>
				</tbody>
			</table>

<?php
		}
	} else {
		echo "This user doesn\'t exist!";
	}
} else {
	header('Location: index.php');
}

	include 'includes/overall/footer.php'; 
?>