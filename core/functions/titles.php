<?php
$bigTitle  = $settings['title'];
$pageTitle = current_page_name();

switch ($pageTitle) {
    case 'index.php':
        $pageTitle = "Home";
        break;
    case 'servers_list.php':
        $pageTitle = "Servers List";
        break;
    case 'add_server.php':
        $pageTitle = "Submit Server";
        break;
	case 'activate.php':
        $pageTitle = "Account Activation";
        break;
	case 'login.php':
        $pageTitle = "Login";
        break;
	case 'logout.php':
        $pageTitle = "Logout";
        break;
	case 'register.php':
        $pageTitle = "Register a new account";
        break;
	case 'my_servers.php':
        $pageTitle = "My Servers";
        break;
	case 'changepassword.php':
        $pageTitle = "Change Password";
        break;
	case 'customize_server.php':
        if(isset($_GET['id']) && !empty($_GET['id'])){
			$id 		= (INT)$_GET['id'];
			$query 		= $database->query("SELECT `ip` FROM `servers` WHERE `id` = '$id'");
			$fetch 		= mysql_fetch_array($query);
			$pageTitle 	= "Customize " . $fetch['ip'];
			
		} else {
			$pageTitle = "Customize Server";
		}
        break;
	case 'access.php':
        $pageTitle = "Access Denied";
        break;
	case 'server.php':
		if(isset($_GET['id']) && !empty($_GET['id'])){
			$id 		= (INT)$_GET['id'];
			$query 		= $database->query("SELECT `ip` FROM `servers` WHERE `id` = '$id'");
			$fetch 		= mysql_fetch_array($query);
			$pageTitle 	= $fetch['ip'];
			
		} else {
			$pageTitle = "Server Details";
		}
        break;
	case 'adm_servers_management.php':
			$pageTitle = "Servers Management";
        break;
	case 'adm_general_settings.php':
			$pageTitle = "General Settings";
        break;
	case 'adm_users_management.php':
			$pageTitle = "Users Management";
        break;
	case 'profile.php':
		if(isset($_GET['username']) && !empty($_GET['username'])){
			$username	= sanitize($_GET['username']);
			$query 		= $database->query("SELECT `name` FROM `users` WHERE `username` = '$username'");
			$fetch 		= mysql_fetch_array($query);
			$pageTitle 	= $fetch['name']. "'s profile";
			
		} else {
			$pageTitle = "Profile";
		}
        break;
	case 'changesettings.php':
		$pageTitle = "Profile Settings";
        break;
	case 'search.php':
		$pageTitle = "Search";
        break;
	case 'contact.php':
		$pageTitle = "Contact";
        break;
	case 'index.php':
		$pageTitle = "Home";
        break;
	case 'index.php':
		$pageTitle = "Home";
        break;
	case 'index.php':
		$pageTitle = "Home";
        break;
	case 'index.php':
		$pageTitle = "Home";
        break;

}
$pageTitle = $bigTitle . " &raquo; " . $pageTitle;
?>