<?php 
include 'core/init.php';
protect_page();
admin_page();
include 'includes/overall/header.php'; 

echo "<center>Congratulations! The votes were reset.</center>";
$database->query("DELETE FROM `votes`");
$database->query("UPDATE `servers` SET `votes` = '0'");

include 'includes/overall/footer.php'; 
?>