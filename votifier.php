<?php
include 'core/init.php';
include 'core/MinecraftVotifier.php';
include 'includes/overall/header.php'; 
if(empty($_GET['id']) == true){
	echo "<h2>Server not found.</h2>";
	include 'includes/overall/footer.php';
	die();
}

	
if(empty($_POST) == false) {
	$fields = array('username', 'captchavar');
	foreach($_POST as $key=>$value) {
		if(empty($value) && in_array($key, $fields) == true){
			$errors[] = 'All fields are required';
			break 1;
		}
	}
	
//captcha
include_once "core/functions/securimage.php";
$securimage = new Securimage();
$valid = $securimage->check($_POST['captchavar']);
//-------

	$_POST['username'] = htmlspecialchars($_POST['username'], ENT_QUOTES);
	$sid = (int)$_GET['id'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$time = time();
	$dbTime = @mysql_result($database->query("SELECT `timestamp` FROM `votes` WHERE `server_id` = '$sid' AND `ip` = '$ip' ORDER BY `timestamp` DESC LIMIT 1"), 0);
	$timeDiff = $time - $dbTime;
	
	if(empty($errors) == true) {
		if($valid == false) {
			$errors[] = 'Please enter the correct captcha code!';
		}
		
		if($timeDiff <= 86400){
			$errors[] = 'You already voted the server today!';
		}
	}
	
	if(empty($errors) == false) {
		echo output_errors($errors);
	} else {
		$result  = $database->query("SELECT `id`, `ip`, `votifier_key`, `votifier_port` FROM `servers` WHERE `id` = '$sid'");
		$server_data = mysql_fetch_array($result, MYSQL_ASSOC);
		
		if(Votifier($server_data['votifier_key'], $server_data['ip'], $server_data['votifier_port'], $_POST['username']) !== false){
			$database->query("INSERT INTO `votes` (`ip`, `server_id`, `timestamp`) VALUES ('".$ip."', $sid, $time)");
			$database->query("UPDATE `servers` SET `votes` = votes +1 WHERE `id` = $sid");
			echo "<font color='green'>Successfully voted!</font>";
		} else {
			echo "<font color='red'>We had some problems sending the vote...</font>";
		}
	}
}
?>

<h2>Vote for ingame rewards</h2>
<form action="" method="POST">
	<label>InGame Username</label>
	<input type="text" name="username" class="span3"/><br />
	
	<img class="img-polaroid" id="captcha" title="Captcha" src="core/functions/securimage_show.php" alt="CAPTCHA Image" />&nbsp;
	<input type="text" style="text-transform:uppercase;width: 70px;" name="captchavar" id="captchavar" maxlength="4" placeholder="captcha" />
	<br /><br />
	
	<input class="btn btn-primary span3" type="submit" value="Vote" />
</form>

<?php include 'includes/overall/footer.php'; ?>