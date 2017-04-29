<?php
function string_resize($string, $maxchar) {
	$length = strlen($string);
	if($length > $maxchar) {
		$cutsize = -($length-$maxchar);
		$string  = substr($string, 0, $cutsize);
		$string  = $string . "..";
	}
	return $string;
}

function settings_data($id) {
    global $database;

    $data = array();
	$user_id = (int)$id;
	
	$func_num_args = func_num_args();
	$func_get_args = func_get_args();
	
	if($func_num_args > 0) {
		unset($func_get_args[0]);
		$fields = '`' . implode('`, `', $func_get_args) . '`';
		$data = $database->query("SELECT $fields FROM `settings` WHERE `id` = '$id'")->fetch_assoc();
		
		return $data;
	}
}

function current_page_name() {
	return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}

function sendmail($to, $subject, $body) {
	mail($to, $subject, $body, 'From: No-Reply!');
}

function logged_in_redirect() {
	if(logged_in() == true) {
		header('Location: index.php');
	}
}

function protect_page() {
	if(logged_in() == false) {
		header('Location: access.php');
		exit();
	}
}

function admin_page() {
	global $user_data;
	if(is_admin($user_data['user_id']) == false) {
		header('Location: index.php');
		exit();
	}


}


function sanitize($data) {
    global $database;

    return $database->escape_string($data);
}

function output_errors($errors) {
	return '
	<div class="alert alert-error" style="padding-top:15px;">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<ul><li>' . implode('</li><li>', $errors) . '</li></ul>
	</div>
	'; 
}
?>