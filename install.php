<?php
ob_start();
error_reporting(0);
?>
<!DOCTYPE html>
<html>
<head>
<title>Complex Servers List Install</title>
	<style>
		@import url('css/reset.css');
		
		@import url('css/main.css');
	</style>
</head>
<br />

<div style="width: 70%;background: white;margin-left:auto;margin-right:auto;padding: 25px;border-radius:5px;">
<?php
$message = '';
if(isset($_POST['install'])){
	if(empty($_POST['db_host']) || empty($_POST['db_name']) || empty($_POST['db_user'])){
		$message = '<center><font color="red"><b>You did not fill the necesary spaces</b></font></center>';
	} else {
		$db_host = htmlentities($_POST['db_host']);
		$db_name = htmlentities($_POST['db_name']);
		$db_user = htmlentities($_POST['db_user']);
		$db_pass = htmlentities($_POST['db_pass']);

		$status_connection = 1;

		/* Connection parameters */
		$database_connection = new StdClass();

		$database_connection->server = 'localhost';
		$database_connection->username = 'root';
		$database_connection->password = 'admin';
		$database_connection->name = 'phpMyServersList';


		/* Establishing the connection */
		$database = new mysqli($database_connection->server, $database_connection->username, $database_connection->password, $database_connection->name);

		/* Debugging */
		if($database->connect_error) {
			die('The connection to the database failed ! Please edit the "core/database/connect.php" file and make sure your database connection details are correct!');

		} else {
$text = <<<PHP
<?php
\$database = array();
\$database['host']     = '$db_host';
\$database['user']     = '$db_user';
\$database['password'] = '$db_pass';
\$database['table']    = '$db_name';

\$connect_error = 'Sorry, there are some connection problems.';
mysql_connect(\$database['host'], \$database['user'], \$database['password']) or die(\$connect_error);
mysql_select_db(\$database['table']) or die(\$connect_error);

?>
PHP;
		$file = "core/database/connect.php";
		$command = fopen($file, w);
		fwrite($command, $text);
		fclose($command);
		
		$database->query("
		CREATE TABLE IF NOT EXISTS `guests` (
		  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `last_activity` varchar(65) COLLATE utf8_unicode_ci NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;", $connection);
		
		$database->query("
		CREATE TABLE IF NOT EXISTS `comments` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `server_id` int(11) NOT NULL,
		  `user_id` int(11) NOT NULL,
		  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		", $connection);
		
		$database->query("
		CREATE TABLE IF NOT EXISTS `servers` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) NOT NULL,
		  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `game` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `vip` tinyint(1) NOT NULL DEFAULT '0',
		  `port` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
		  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `country` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'XX',
		  `votes` int(11) NOT NULL DEFAULT '0',
		  `disabled` tinyint(1) NOT NULL DEFAULT '0',
		  `map` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `players` int(11) NOT NULL,
		  `maxPlayers` int(11) NOT NULL,
		  `status` tinyint(1) NOT NULL DEFAULT '0',
		  `cache_time` text COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		", $connection);
		
		$database->query("
		CREATE TABLE IF NOT EXISTS `settings` (
		  `id` int(11) NOT NULL DEFAULT '1',
		  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Comples Servers List',
		  `facebook` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
		  `twitter` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
		  `contact_email` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `pagination` int(11) NOT NULL DEFAULT '15',
		  `register` int(11) NOT NULL DEFAULT '1',
		  `disable_index_querying` int(11) NOT NULL DEFAULT '0',
		  `vote_login_restriction` int(11) NOT NULL DEFAULT '0',
		  `server_cache` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '600',
		  `show_offline_servers` int(11) NOT NULL DEFAULT '0',
		  `email_confirmation` int(11) NOT NULL DEFAULT '1',
		  `server_confirmation` int(11) NOT NULL DEFAULT '1',
		  `advertise_top` varchar(2555) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
		  `advertise_bottom` varchar(2555) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;", $connection);
		
		$database->query("
		CREATE TABLE IF NOT EXISTS `users` (
		  `user_id` int(11) NOT NULL AUTO_INCREMENT,
		  `username` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `email` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `email_code` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `avatar` varchar(65) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `active` int(11) NOT NULL DEFAULT '0',
		  `type` int(1) NOT NULL DEFAULT '0',
		  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0',
		  `date` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `last_activity` varchar(65) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;", $connection);
		
		$database->query("
		CREATE TABLE IF NOT EXISTS `votes` (
		  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `server_id` int(11) NOT NULL,
		  `timestamp` int(11) NOT NULL,
		  KEY `server_id` (`server_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;", $connection);
		
		$database->query("
		INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `email_code`, `name`, `avatar`, `active`, `type`, `ip`, `date`, `last_activity`) VALUES
		(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'email', '', 'Admin', 'avatars/7963843a08.png', 1, 1, '0.0.0.0', 'beginning of time', '')", $connection);
		
		$database->query("
			INSERT INTO `settings` (`id`, `title`, `twitter`, `facebook`, `contact_email`, `pagination`, `register`, `server_cache`, `show_offline_servers`, `email_confirmation`, `server_confirmation`, `advertise_top`, `advertise_bottom`) VALUES
			(1, 'Complex Valve Servers List', 'false', 'false', 'ContactEmail', 15, 1, '600', 0, 0, 0, 'false', 'false');

		", $connection);
		
		header('Location: install.php?success');
		}
	}		
}
if(isset($_GET['success']) && empty($_GET['success'])) {
	echo '<center>
		<font color=\'green\'>The mysql query and the database configuration is successfully updated!</font>
		<br />Please delete this file now(install.php), for security reasons!<br /><br />
		<br />Login with admin right with the next account<br />
		<b>Username:</b> admin<br />
		<b>Password:</b> admin
		</center>
	';
}

?>
			
	<center>
	<?php echo $message; ?>
		<form name="install" method="post" action="">
		<h3>Database Connection</h3>
			<p>
				<table>
					<tr>
						<td><label>Database Host</label></td>
						<td><input  name="db_host" type="text" size="30" value="localhost" /></td>
					</tr>
					<tr>
						<td><label>Database Name</label></td>
						<td><input  name="db_name" type="text" size="30" value="<?php echo $_POST['db_name']; ?>" /></td>
					</tr>
					<tr>
						<td><label>Database User</label></td>
						<td><input  name="db_user" type="text" size="30" value="<?php echo $_POST['db_user']; ?>" /></td>
					</tr>
					<tr>
						<td><label>Database Password</label></td>
						<td><input  name="db_pass" type="text" size="30" value="<?php echo $_POST['db_pass']; ?>" /></td>
					</tr>
					<tr>
						<td><br />
							<input  type="submit" name="install" value="Install" />		
						</td>
					</tr>
				</table>
			</p>
		</form>
	</center>
</div>
<?php
ob_flush();
?>