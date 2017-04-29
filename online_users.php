<?php 
	include 'core/init.php';
	include 'includes/overall/header.php';
	protect_page();
	
echo "<h2>Online Users List</h2>";
echo "<p>Online users list based on the last 30 seconds of activity !</p>";

echo "<ul>";
$query = $database->query("SELECT `username`, `type` FROM `users` WHERE `last_activity` > unix_timestamp() - 30 ORDER BY `type` DESC");//in seconds
while($row = mysql_fetch_array($query, MYSQL_ASSOC)){
	if($row['type'] == 1){
		echo "<li><font color='red'>" . $row['username'] . "</font></li>";
	} else {
		echo "<li>" . $row['username'] . "</li>";
	}
}
echo "</ul>";
	include 'includes/overall/footer.php'; 
?>