<?php 
include 'core/init.php';
include 'includes/overall/header.php'; 

$server_id  = (INT)$_GET['id'];
$user_id	= id_to_user_id($server_id);
$addedBy 	= username_from_user_id($user_id);

if(empty($_GET['id']) == true || $addedBy == false){
	echo "<h2>Server not found.</h2>";
	include 'includes/overall/footer.php';
	die();
}

$result  = $database->query("SELECT `id`, `game`, `country`, `user_id`, `ip`, `port`, `vip`, `status`, `cache_time`, `votes` FROM `servers` WHERE `id` = '$server_id'");
$server_data = $result->fetch_assoc();

$last_update = time() - $server_data['cache_time'];
$last_updateM = intval($last_update/60);
	
try {
	$Query = new LiveStats($server_data['ip'], $server_data['port']);
	$info = $Query->GetServer();
}
catch (LSError $e) {}


if(empty($e)){ $status = 1; } else { $status = 0; }


if($status == 1) {
	$database->query("UPDATE `servers` SET `map` = '{$info->Map}', `players` = '{$info->PlayerCount}', `maxPlayers` = '{$info->MaxPlayers}' WHERE `id` = '$server_id'");
}

$database->query("UPDATE `servers` SET `cache_time` = unix_timestamp(), `status` = '$status' WHERE `id` = '$server_id'");

?>

<div id="sid" data-sid="<?php echo $server_data['id']; ?>" style='margin:auto;'>
<?php 
if($status == 0){
	echo "<h2>" . $server_data['ip'] . " is offline!<h2>";
} else { ?>	
	<center><h2><?php echo $server_data['ip']; ?></h2></center>

<ul class="nav nav-pills" id="tabs" >
	<li class="active"><a href="#info"><i class="icon-signal"></i> Server Informations</a></li>
	<li><a href="#banners"><i class="icon-picture"></i> Banners</a></li>
	<li><a href="#rules"><i class="icon-tag"></i> Rules</a></li>
	<li><a href="#players"><i class="icon-user"></i> Players</a></li>

</ul>
<div class="tab-content" >
	<div class="tab-pane active" id="info">
		<table class="table table-striped" style="background:white;">
			<tr>
				<td><strong>Status</strong></td>
				<td><span class="badge badge-success"><i class="icon-ok icon-white"></i></span></td>
			</tr>
			<tr>
				<td><strong>Hostname</strong></td>
				<td><?php echo $info->Hostname; ?></td>
			</tr>
			<tr>
				<td><strong>IP Address</strong></td>
				<td><?php echo gethostbyname($server_data['ip']); ?></td>
			</tr>
			<tr>
				<td><strong>Port</strong></td>
				<td><?php echo $server_data['port']; ?></td>
			</tr>
			<tr>
				<td><strong>Online Players</strong></td>
				<td><?php echo $info->PlayerCount . "/" . $info->MaxPlayers; ?></td>
			</tr>
			<?php if($server_data['game'] !== "samp") { ?>
			<tr>
				<td><strong>Protocol</strong></td>
				<td><?php echo $info->ProtocolVersion; ?></td>
			</tr>
			<tr>
				<td><strong>Game</strong></td>
				<td><?php echo $info->Description; ?></td>
			</tr>
			<tr>
				<td><strong>Bots</strong></td>
				<td><?php echo $info->BotCount; ?></td>
			</tr>
			<tr>
				<td><strong>OS</strong></td>
				<td><?php echo $info->OS; ?></td>
			</tr>
			<tr>
				<td><strong>Secured</strong></td>
				<td><?php echo ($info->Secured == 1) ? "Yes":"No"; ?></td>
			</tr>
			<tr>
				<td><strong>Connect</strong></td>
				<td><a href="steam://connect/<?php echo $server_data['ip'] . ":" . $server_data['port']; ?>"><img src="includes/img/steam.png" alt="Connect" /> Steam</a></td>
			</tr>
			<?php } ?>
			<tr>
				<td><strong>Map</strong></td>
				<td><?php echo $info->Map; ?></td>
			</tr>
			<tr>
				<td><strong>Country</strong></td>
				<td><img src="includes/locations/<?php echo $server_data['country']; ?>.png" title="<?php echo $server_data['country']; ?>" alt="country"/></td>
			</tr>
			<tr>
				<td><strong>Added by</strong></td>
				<td><a href="profile-<?php echo $addedBy; ?>"><?php echo $addedBy; ?></a></td>
			</tr>
			<tr>
				<td><strong>Last update</strong></td>
				<td><?php echo $last_updateM; ?> minutes ago</td>
			</tr>
			<tr>
				<td><strong>Votes</strong></td>
				<td>
					<div id="votes" style="display:inline;"><?php echo $server_data['votes']; ?></div> <input type="button" class="btn" id="vote" value="Vote server !">
				</td>
			</tr>
			
		</table>
	</div>
	
	<div class="tab-pane" id="banners">
		<?php 
		$domain = $_SERVER['HTTP_HOST'];
		$folder = basename(dirname(__FILE__));
		$link   = "http://" . $domain ."/".$folder;
		?>
		<img src="dynamic_image.php?s=<?php echo $server_id; ?>&type=background" />
		<h3>BB/HTML Code </h3>
		<textarea id="bb_small_code" rows="3" style="width: 95%;">[url=<?php echo $link; ?>/server.php?id=<?php echo $id; ?>][img]<?php echo $link; ?>/dynamic_image.php?s=<?php echo $id; ?>&type=background[/img][/url]</textarea>
		<textarea id="html_small_code" rows="3" style="width: 95%;"><a href="<?php echo $link; ?>/server.php?id=<?php echo $id; ?>"><img src="<?php echo $link; ?>/dynamic_image.php?s=<?php echo $id; ?>&type=background"></a></textarea>
	
	</div>

	<div class="tab-pane" id="rules">
		<table class="table table-bordered" style="background:white;">
			<thead>
				<th>Name</th>
				<th>Value</th>
			</thead>
			<tbody>
			<?php 
				foreach($info->Rules as $key => $value) {
					echo "<tr><td><strong>" . $key . "</strong></td><td>" . $value . "</td></tr>";
				}
			?>
			</tbody>
		</table>
	</div>

	<div class="tab-pane" id="players">
		<table class="table table-condensed">
			<thead>
				<th>Name</th>
				<th>Score</th>
				<th>Time</th>
			</thead>
			<tbody>
				<?php
					foreach($info->Players as $player){
						echo "
							<tr>
								<td>{$player->Name}</td>
								<td>{$player->Score}</td>
								<td>{$player->TimePlayed}</td>
							</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>



<?php }  ?>
</div>
<h2>Comments</h2>
<?php
$query = $database->query("SELECT * FROM `comments` WHERE `server_id` = '$server_id'");
if($query->num_rows){
	echo "<p>This server doesn't have any comments added</p>";
}
while($row = $query->fetch_assoc()){
$comment_user_id  = $row['user_id'];
@$comment_added_by = $database->query("SELECT `username` FROM `users` WHERE `user_id` = '$comment_user_id'")->fetch_object()->username ? $database->query("SELECT `username` FROM `users` WHERE `user_id` = '$comment_user_id'")->fetch_object()->username : "Unknown User";
?>
<table class="table table-bordered" style="background:white;">
	<tr>
		<td>
			<strong>Comment by:</strong> <?php echo $comment_added_by; ?>
			<?php if(logged_in() == true && is_admin($session_user_id)){ ?>
			<div class="pull-right">
				<a href="server.php?id=<?php echo $server_id; ?>&delete=<?php echo $row['id']; ?>">
					<img alt='' src='includes/img/delete.png' title='Delete comment' />
				</a>
			</div>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td><?php echo $row['comment']; ?></td>
	</tr>
</table>
<?php } ?>

<br /><br />
<?php
if(empty($_POST) == false){
	//captcha
	include_once "core/functions/securimage.php";
	$securimage = new Securimage();
	$valid = $securimage->check($_POST['captchavar']);
	//-------
	
	if($valid == false) {
		$errors[] = 'Please enter the correct captcha code!';
	}
	
	if(strlen($_POST['comment']) > 254){
		$errors[] = 'Comment too long, maximum 255 characters!';
	}
	
	if(strlen(trim($_POST['comment'])) < 10){
		$errors[] = 'Comment too short, minimum 10 characters!';
	}
	
	if(empty($errors) == true){
		$comment	= htmlspecialchars($_POST['comment'], ENT_QUOTES);
		$database->query("INSERT INTO `comments` (`server_id`, `user_id`, `comment`) VALUES ('$server_id', '$session_user_id', '$comment')");
		echo "Congratulations, your comment was submitted !";
	}else{
		echo output_errors($errors);
	}
}
?>
<?php if(logged_in() == true){ ?>
<form action="" method="post">
	<textarea style="width:100%;height:100px;" name="comment"></textarea><br />
	<img class="img-polaroid" id="captcha" title="Captcha" src="core/functions/securimage_show.php" alt="CAPTCHA Image" />&nbsp;
	<input class="span2" type="text" style="text-transform:uppercase;" name="captchavar" id="captchavar" size="11" maxlength="4" placeholder="captcha" /><br /><br />
	
	<input type="submit" class="btn btn-primary" value="Add comment" />
</form>
<?php } ?>

<?php
if(empty($_GET['delete']) == false && logged_in() && is_admin($session_user_id)){
	$comment_id = (INT)$_GET['delete'];
	$database->query("DELETE FROM `comments` WHERE `id` = '$comment_id'");
	header('Location: server.php?id=' . $server_id);
}

?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#vote').click(function(){
			$.ajax({
				url : 'vote_updater.php',
				type : 'POST',
				data : {
					action : 'vote_server',
					sid : $('#sid').data('sid')
				},
				dataType : 'JSON',
				success : function(result) {
					if (result.xhr == 'success') {
						$('#vote').attr('disabled','true');
						$('#votes').html(parseInt($('#votes').html()) + 1);
						alert('Succesfully voted !');
					} else if (result.xhr == 'voted_already')
						alert('You already voted this server !')
					else if (result.xhr == 'not_logged_in')
						alert('You must be logged in to vote !')
				}
			});
		});
	});
	
	$('#tabs a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	})
</script>

<?php include 'includes/overall/footer.php'; ?>