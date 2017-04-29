<?php
ob_start();
session_start();
//error_reporting(0);
require 'database/connect.php';
require 'functions/general.php';
require 'functions/users.php';
require 'functions/servers.php';
require 'q.php';

if(logged_in() == true){
	$session_user_id = $_SESSION['user_id'];
	$user_data = user_data($session_user_id, 'user_id', 'name', 'username', 'password', 'email', 'type', 'avatar');
	if(user_active($user_data['username']) == false){
		session_destroy();
		header('Location: index.php');
		exit();
	}
	
	$database->query("UPDATE `users` SET `last_activity` = unix_timestamp() WHERE `user_id` = '$session_user_id'");
}


$settings = settings_data(1, 'title', 'vote_login_restriction', 'disable_index_querying', 'facebook', 'twitter', 'contact_email', 'pagination', 'register', 'show_offline_servers', 'server_cache', 'email_confirmation', 'server_confirmation', 'advertise_top', 'advertise_bottom');
require 'functions/titles.php';

/* Generate current GET parameters to prepend to the filters */
$prepend = "";
foreach($_GET as $key => $value) {
	$prepend .= $key . "=" . $value . "&";
}

$errors = array();
?>