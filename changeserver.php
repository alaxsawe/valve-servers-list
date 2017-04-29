<?php
include 'core/init.php';
protect_page();
include 'includes/overall/header.php'; 

@$sid  = (INT)$_GET['id'];
$data = $database->query("SELECT `ip`, `port`, `name` FROM `servers` WHERE `id` = '$sid' AND `user_id` = '$session_user_id'")->fetch_assoc();
if($data == false){?>
	<div class="alert alert-error">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Error!</strong> This server does not belong to you!
	</div>

<?php
	include 'includes/overall/footer.php'; 
	die();
}

if(empty($_POST) == false) {
	
	$_POST['name']= htmlspecialchars($_POST['name'], ENT_QUOTES);	
	
	if(empty($errors) == true) {
		if(empty($_POST['name']) == true) {
			$errors[] = 'Name field should not be empty!';
		}
	}
}

?>
<h2>Server Settings</h2>
<?php
if(empty($_POST) == false && empty($errors) == true) {
	$name    = $_POST['name'];
	
	$database->query("UPDATE `servers` SET `name` = '$name' WHERE `id` = '$sid'");
	header('Location: my_servers.php?successUpdate');
} else if (empty($errors) == false) {
	echo output_errors($errors);
}
?>
<form action="" method="post">
	<input type="text" name="ip_port" value="<?php echo $data['ip'].":".$data['port']; ?>" disabled/>
	<br />
	
	<label>Name</label>
	<input type="text" name="name" value="<?php echo $data['name']; ?>" />
	
	<br />
	<input class="btn btn-primary" type="submit" value="Submit Settings" />
</form>
	
<?php include 'includes/overall/footer.php'; ?>