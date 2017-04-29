<div class="hero-unit">
	<div class="pull-right">
	<?php
	if($settings['facebook'] !== 'false'){
	echo '<a href="http://facebook.com/' . $settings['facebook'] . '" class="btn btn-primary btn-small">Follow us on Facebook <i class="icon-white icon-share"></i></a>';
	}
	if($settings['twitter'] !== 'false'){
	echo '&nbsp;<a href="http://twitter.com/' . $settings['twitter'] . '" class="btn btn-info btn-small">Follow us on Twitter <i class="icon-white icon-share-alt"></i></a>';
	}
	?>
	</div>
	<h1>Welcome</h1>
	<p style="font-size:16px;">
		We currently have <?php echo servers_count(); ?> servers added in our database by a total of <?php echo user_count(); ?> successfully registered users.
		<br />On the website there are currently <a href="online_users.php"><?php echo online_users(); ?></a> users online, take your time to register on this beautiful website.
	</p>

	<div>
		<?php
		$prependG = (isset($_GET['game'])) ? "" : $prepend;

		//Selecting the existing games that are into the database
		$games = array();
		$query = $database->query("SELECT DISTINCT `game` FROM `servers` WHERE `disabled` = '0'");
		while($row = $query->fetch_assoc()){
			$games[$row['game']] = $row['game'];
		}
		
		//Setting up some names for some games
		$values = array("css" => "Counter Strike: Source", "cstrike" => "Counter Strike", "czero" => "Counter Strike : Zero", "csgo" => "CS : Global Offensive", "tf" => "Team Fortress", "left4dead" => "Left4Dead", "left4dead2" => "Left4Dead 2");
		foreach($values as $key=>$value){
			if(array_key_exists($key, $games)) $games[$key] = $value;
		}
		
		//displaying the games in form of buttons
		foreach($games as $game=>$gameName){
			echo '<a href="index.php?' . $prependG . 'game=' . $game . '"><button class="btn">' . $gameName . '</button></a>&nbsp;';
		}
		?>
	

	</div>
	
</div>