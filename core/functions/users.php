<?php
function send_avatar($file_temp, $file_extension) {
	global $file_path;
	$file_path = 'avatars/' . substr(md5(time()), 0, 10) . '.' . $file_extension;
	move_uploaded_file($file_temp, $file_path);
}
function online_users() {
    global $database;

    $online_users = $database->query("SELECT `user_id` FROM `users` WHERE `last_activity` > unix_timestamp() - 30")->num_rows;//in seconds
	echo $online_users;
}

function update_user($user_id, $update_data) {
    global $database;

    $update = array();
	array_walk($update_data, 'array_sanitize');
	
	foreach($update_data as $field=>$data) {
		$update[] = '`' . $field . '` = \'' . $data .'\'';
	}
		
	$database->query("UPDATE `users` SET " . implode(', ', $update) . " WHERE `user_id` = $user_id ");
}

function is_admin($user_id) {
    global $database;

    $user_id = (INT)$user_id;
	return ($database->query("SELECT COUNT(`user_id`) FROM `users` WHERE `user_id` = {$user_id} AND `type` = 1")->num_rows);
}

function activate($email, $email_code) {
    global $database;

    $email		= $database->escape_string($email);
	$email_code = $database->escape_string($email_code);
	
	if($database->query("SELECT COUNT(`user_id`) FROM `users` WHERE `email` = '$email' AND `email_code` = '$email_code' AND `active` = 0")->num_rows) {
		$database->query("UPDATE `users` SET `active` = 1 WHERE `email` = '$email'");

        return true;
	} else {
		return false;
	}
}

function change_password($user_id, $password) {
    global $database;

	$user_id = (int)$user_id;
	$password = md5($password);
	
	$database->query("UPDATE `users` SET `password` = '$password' WHERE `user_id` = $user_id");
}

function register_user($register_data) {
    global $database;

    array_walk($register_data, 'array_sanitize');
	$register_data['password'] = md5($register_data['password']);
	$active = $register_data['active'];
	$fields = '`' . implode('`, `', array_keys($register_data)) . '`';
	$data = '\'' . implode('\', \'', $register_data) . '\'';
	
	$database->query("INSERT INTO `users` ($fields) VALUES ($data)");
	if($active == '0'){
		sendmail($register_data['email'], 'Activate your account', "
			Hello " . $register_data['name'] . ",\n\n
			To activate your account, access the link below:\n\n
			http://changeme.com/activate.php?email=" . $register_data['email'] . "&email_code=" . $register_data['email_code'] . " \n\n
		");
	}
}
function user_count() {
    global $database;

    return $database->query("SELECT COUNT(`user_id`) AS `count` FROM `users` WHERE `active` = 1")->fetch_object()->count;
}
function disabled_users_count() {
    global $database;

    return $database->query("SELECT COUNT(`user_id`) AS `count` FROM `users` WHERE `active` = 0")->fetch_object()->count;
}

function user_data($user_id) {
    global $database;

    $data = array();
	$user_id = (int)$user_id;
	
	$func_num_args = func_num_args();
	$func_get_args = func_get_args();
	
	if($func_num_args > 0) {
		unset($func_get_args[0]);
		$fields = '`' . implode('`, `', $func_get_args) . '`';
		$data = $database->query("SELECT $fields FROM `users` WHERE `user_id` = '$user_id'")->fetch_assoc();
		
		return $data;
	}
}

function logged_in() {
	return (isset($_SESSION['user_id'])) ? true : false;
}

function email_exists($email) {
    global $database;

    $email = sanitize($email);

    return $database->query("SELECT COUNT(`user_id`) FROM `users` WHERE `email` = '{$email}'")->num_rows;
}

function user_exists($username) {
    global $database;

    $username = sanitize($username);
	return $database->query("SELECT COUNT(`user_id`) FROM `users` WHERE `username` = '{$username}'")->num_rows;
}

function user_active($username) {
    global $database;

    $username = sanitize($username);
	return $database->query("SELECT COUNT(`user_id`) FROM `users` WHERE `username` = '$username' AND `active` ='1'")->num_rows;
}

function user_id_from_username($username) {
    global $database;

	$username = sanitize($username);
	$query = $database->query("SELECT `user_id` FROM `users` WHERE `username` = '{$username}'");
	return mysql_result($query, 0, 'user_id');
}
function username_from_user_id($user_id) {
    global $database;

    $username = sanitize($user_id);
	return $database->query("SELECT `username` FROM `users` WHERE `user_id` = '{$user_id}'")->fetch_object()->username;
}
function login($username, $password) {
    global $database;

    $user_id = user_id_from_username($username);
	$username = sanitize($username);
	$password = md5($password);
	return $database->query("SELECT COUNT(`user_id`) FROM `users` WHERE `username` = '$username' AND `password` = '$password'")->num_rows;
}
?>