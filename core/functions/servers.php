<?php
function server_exists2($ip, $port) {
	$ip = sanitize($ip);
	$query = $database->query("SELECT COUNT(`ip`) FROM `servers` WHERE `ip` = '$ip' AND `port` = '$port'");
	return (mysql_result($query, 0) == 1) ? true : false;
}
function server_exists($ip) {
	$ip = sanitize($ip);
	$query = $database->query("SELECT COUNT(`ip`) FROM `servers` WHERE `ip` = '$ip'");
	return (mysql_result($query, 0) == 1) ? true : false;
}

function get_country($ip) {
	if(!is_numeric($ip)){
		$ip = gethostbyname($ip);
	}
	$current_dir    = explode("/" ,$_SERVER['REQUEST_URI']);
	$current_dir    = array_slice($current_dir, 0, -1);
	$current_dir    = implode("/", $current_dir);
	$link 			= "http://api.wipmania.com/" . $ip;
	@$country 		= (file_get_contents($link)) ? file_get_contents($link) : "XX";
	//$icon   		= "http://" . $_SERVER['SERVER_NAME'] . $current_dir . "/includes/locations/" . $country . ".png";
	return $country ;
}



function server_vip($server_id) {
    global $database;

    $server_id = (INT)$server_id;
	return $database->query("SELECT `vip` FROM `servers` WHERE `id` = $server_id")->fetch_object()->vip;
}
function servers_count() {
    global $database;

    return $database->query("SELECT COUNT(`user_id`) AS `count` FROM `servers` WHERE `disabled` = 0")->fetch_object()->count;
}
function disabled_servers_count() {
    global $database;

    return $database->query("SELECT COUNT(`user_id`) AS `count` FROM `servers` WHERE `disabled` = 1")->fetch_object()->count;
}

function HexToRGB($hex) {
		$hex = str_replace("#", "", $hex);
		$color = array();
 
		if(strlen($hex) == 3) {
			$color['r'] = hexdec(substr($hex, 0, 1) . $r);
			$color['g'] = hexdec(substr($hex, 1, 1) . $g);
			$color['b'] = hexdec(substr($hex, 2, 1) . $b);
		}
		else if(strlen($hex) == 6) {
			$color['r'] = hexdec(substr($hex, 0, 2));
			$color['g'] = hexdec(substr($hex, 2, 2));
			$color['b'] = hexdec(substr($hex, 4, 2));
		}
		return $color;
}
	
	
function id_to_user_id($id) {
    global $database;

	$id = sanitize($id);
	$query 	= $database->query("SELECT `user_id` FROM `servers` WHERE `id` = '$id'");
	$data = mysql_fetch_assoc($query);
	return $data['user_id'];
}


?>