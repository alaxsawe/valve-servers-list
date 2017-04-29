<?php
	include 'core/init.php';
	$result = array('xhr' => 'error');

	if ($_POST['action'] == 'vote_server')
	{
		if(logged_in() == false) { 
			$result['xhr'] = 'not_logged_in';
		} else {

			$sid = (int)$_POST['sid'];
			$ip = $_SERVER['REMOTE_ADDR'];
			$time = time();
			$dbTime = @mysql_result($database->query("SELECT `timestamp` FROM `votes` WHERE `server_id` = '$sid' AND `ip` = '$ip' ORDER BY `timestamp` DESC LIMIT 1"), 0);
			$timeDiff = $time - $dbTime;

			if($timeDiff >= 86400){
				$database->query("INSERT INTO `votes` (`ip`, `server_id`, `timestamp`) VALUES ('".$ip."', $sid, $time)");
				$database->query("UPDATE `servers` SET `votes` = votes +1 WHERE `id` = $sid");
				$result['xhr'] = 'success';
			}  else { $result['xhr'] = 'voted_already';  }
			
		}
	}
	
	echo json_encode($result);
	
?>