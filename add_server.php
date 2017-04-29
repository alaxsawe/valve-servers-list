<?php 
include 'core/init.php';
protect_page();
include 'includes/overall/header.php'; 
	
if(isset($_POST['test_server'])) {
$_POST['ip_address'] = htmlspecialchars($_POST['ip_address'], ENT_QUOTES);

	//Server checks
	try {
		$Query = new LiveStats($_POST['ip_address'], $_POST['connection_port']);
		$info = $Query->GetServer();
	}
	catch (LSError $e) {}
				
	//Check status of the server
	if(empty($e)){ $status = 1; } else { $status = 0; }
	if($info->Type == 2) @$info->Directory = "samp";
}

if(isset($_POST['add_server'])) {
	$ip   	  = htmlspecialchars($_POST['ip'], ENT_QUOTES);
	$port 	  = htmlspecialchars($_POST['port'], ENT_QUOTES);
	$banner   = $_POST['banner_url'];
	$name 	  = $database->escape_string(htmlspecialchars($_POST['name'], ENT_QUOTES));
	$game 	  = ($_POST['Description'] == "Counter-Strike: Source") ? "css" : $_POST['game'];
	$country  = get_country($ip);
	$disabled = ($settings['server_confirmation'] == '1') ? '1' : '0';
	$database->query("INSERT INTO `servers` (`user_id`, `ip`, `port`, `game`, `disabled`, `vip`, `name`, `status`, `country`) VALUES ('$session_user_id', '$ip', '$port', '$game', '$disabled', 0, '$name', '1', '$country')");
	header('Location: add_server.php?success');
	
}
?>
<h2>Submit new server</h2>
<?php
if(isset($_POST['test_server'])) {
	if(server_exists2($_POST['ip_address'], $_POST['connection_port'])) {
		$errors[] = "Server already exists into the database!";
	}
	if(strlen($_POST['nametest']) > 254){
		$errors[] = "Server name is too long !";
	}
	if(strlen($_POST['nametest']) < 4){
		$errors[] = "Server name is too short !";
	}
	if($status == 0) {
		$errors[] = "Server offline or details incorrect !";
	}
}
if(isset($_GET['success']) && empty($_GET['success'])) {
	echo '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Congratulations!</strong> Your server has been added!';
	if($settings['server_confirmation'] == '1') echo "&nbsp;&nbsp; Please wait for admin approval!";
	echo '</div>';
}

if(empty($errors) == false) {
	echo output_errors($errors);
}

?>

<form method="post" action="">
	<label>Name</label>
	<input type="text" name="nametest" class="span4" />
	
	<label>Ip Address</label>
	<input type="text" name="ip_address" class="span4" />

	<label>Connection Port</label>
	<input type="text" name="connection_port" value="27015" class="span4" maxlength="5"/><br />
	
	<input class="btn btn-primary btn-large span4" type="submit" name="test_server" value="Test Server" /><br /><br />
	
	<?php if(isset($_POST['test_server']) && $status == 1 && empty($errors)){ ?>
		<ul>
			<li><strong>Status</strong>: <span class="badge badge-success"><i class="icon-ok icon-white"></i></span></li>
			<li><strong>Hostname</strong>: <?php echo $info->Hostname; ?></li>
			<li><strong>Players</strong>: <?php echo $info->PlayerCount . " / " . $info->MaxPlayers; ?></li>
			<li><strong>Map</strong>: <?php echo $info->Map; ?></li>
			<li><strong>Game</strong>: <?php echo $info->Directory; ?></li>
			<li><strong>Country</strong>: <img src="includes/locations/<?php echo get_country($_POST['ip_address']); ?>.png" alt="country"/>
		</ul><br />

		<input type="hidden" name="name" value="<?php echo $_POST['nametest']; ?>" />
		<input type="hidden" name="ip" value="<?php echo $_POST['ip_address']; ?>" />
		<input type="hidden" name="port" value ="<?php echo $_POST['connection_port']; ?>" />
		<input type="hidden" name="game" value ="<?php echo $info->Directory; ?>" />
		<input type="hidden" name="Description" value="<?php echo $info->Description; ?>" />


		<input class="btn btn-primary btn-large span3" type="submit" name="add_server" value="Add server" />
		
<?php } ?>
</form>
<?php include 'includes/overall/footer.php'; ?>