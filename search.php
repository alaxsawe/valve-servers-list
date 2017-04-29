<?php 
include 'core/init.php';
include 'includes/overall/header.php'; 

if(empty($_GET)){
	header('Location: index.php');
}

$term = $database->escape_string($_GET['term']);
$term = htmlspecialchars($term);
$keywords = preg_split('#\s+#', $term);
$c = 0;
foreach($keywords as $keyword){
	if(strlen($keyword) < 3){
		$c++;
	}
}
if($c > 0){
	$errors[] = "One of the keywords you entered is too short.";
}
if(strlen($term) < 4){
	$errors[] = "Search string too short!";
}
if(empty($errors) !== true){
	echo output_errors($errors);
} else {
	echo "<i>Search results for <b>" . $term . "</b> based on the <b>Name</b> of the servers</i><br /><br />";

	$name_where		   = "`name` LIKE '%" . implode("%' OR `name` LIKE '%", $keywords) . "%'";
	$query             = $database->query("SELECT * FROM `servers` WHERE {$name_where} AND `disabled` = 0");
	
?>
<table class="table table-bordered table-stripped" style="background:white;">
	<tbody>
<?php
	while($server_data = $query->fetch_assoc()){
		$last_update		= time() + $server_data['cache_time'];
		$last_updateM 		= date("m", $last_update);
		$status				= $server_data['status'];
?>
		
		<tr>
			<td>
				<?php if($status == 1){	?>
				<span class="badge badge-success"><i class="icon-ok icon-white"></i></span>&nbsp;
				<?php } else { ?>
				<span class="badge badge-important"><i class="icon-remove icon-white"></i></span>&nbsp;
				<?php } ?>
				<?php echo $server_data['ip'] . ":" . $server_data['port']; ?><br />
			</td>
			<td>
				<strong>Name:</strong> <?php echo $server_data['name']; ?><br />
			</td>
			<td>
				<strong>Online players:</strong> <?php echo $server_data['players'] . "/" . $server_data['maxPlayers']; ?><br />
			</td>
			<td>
				<strong>Map:</strong> <?php echo $server_data['map']; ?><br />
			</td>
			<td>
				<strong>Votes:</strong> <?php echo $server_data['votes']; ?>
			</td>
			<td style="vertical-align:middle;"><a href="server.php?id=<?php echo $server_data['id']; ?>"><img src="includes/img/icon_details.gif" title="Server Informations" /></a></td>
		</tr>

<?php } ?>
	</tbody>
</table>
<?php
}


include 'includes/overall/footer.php'; ?>