<div class="span2">
	<?php if(current_page_name() == "index.php"){ ?>
	<ul class="nav nav-pills nav-stacked">
		<?php if(!empty($prepend)) { ?>
		<li class="active">
		    <a href="index.php">Reset Filters</a>
		</li>
		<?php } ?>

		<li class="dropdown active">
		    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Country<b class="caret"></b></a>
		    <ul class="dropdown-menu">
		    	<?php 
		    	$prependC = (isset($_GET['country'])) ? "" : $prepend;

				$result = $database->query("SELECT DISTINCT `country` FROM `servers`");
				while($row = $result->fetch_assoc()){
					echo "<li><a href='index.php?" . $prependC . "country=" . $row['country'] . "'>" . $row['country'] . "</a></li>";
				}
				?>
		    </ul>
		</li>


		<li class="dropdown active">
		    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Map<b class="caret"></b></a>
		    <ul class="dropdown-menu">
		    	<?php 
		    	$prependM = (isset($_GET['map'])) ? "" : $prepend;

				$result = $database->query("SELECT DISTINCT `map` FROM `servers`");
				while($row = $result->fetch_assoc()){
					echo "<li><a href='index.php?" . $prependM . "map=" . $row['map'] . "'>" . $row['map'] . "</a></li>";
				}
				?>
		    </ul>
		</li>

		<?php $prependS = (isset($_GET['sort'])) ? "" : $prepend; ?>
		<li class="dropdown active">
		    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Sort by<b class="caret"></b></a>
		    <ul class="dropdown-menu">
		    	<li><a href="index.php?<?php echo $prependS; ?>sort=players">Players</a></li>
		    	<li><a href="index.php?<?php echo $prependS; ?>sort=votes">Votes</a></li>
		    	<li><a href="index.php?<?php echo $prependS; ?>sort=status">Status</a></li>
		    </ul>
		</li>
	</ul>
	<?php } ?>
	<table class="table table-hover table-striped table-bordered">
		<tbody>
			<tr>
				<th>
					<i class="icon-random"></i> Latest Servers
				</th>
			</tr>
			<?php
				$latest_s = $database->query("SELECT `id`, `ip`, `game`, `name` FROM `servers` ORDER BY `id` DESC LIMIT 5");
				while($row = $latest_s->fetch_assoc()){
				echo "<tr><td>";
				
				if(file_exists("includes/game_icons/" . $row['game'] . ".gif"))
				echo "<img src='includes/game_icons/" . $row['game'] . ".gif' title='" . $row['game'] . "'/>&nbsp;";
				
				echo "<a href='server.php?id={$row['id']}'>{$row['ip']}</a></td></tr>";
				}
			?>
		</tbody>
	</table>
	<table class="table table-hover table-striped table-bordered">
		<tbody>
			<tr>
				<th>
					<i class="icon-random"></i> Latest Users
				</th>
			</tr>
			<?php
				$latest_u = $database->query("SELECT `username`, `name` FROM `users` ORDER BY `user_id` DESC LIMIT 5");
				while($row = $latest_u->fetch_assoc()){
				echo "<tr><td><a href='profile-{$row['username']}'>{$row['name']}</a></td></tr>";
				}
			?>
		</tbody>
	</table>
	<table class="table table-hover table-striped table-bordered">
		<tbody>
			<tr>
				<th>
					<i class="icon-th"></i> Statistics
				</th>
			</tr>
			<tr><td>Servers: <?php echo servers_count(); ?></td></tr>
			<tr><td>Users: <?php echo user_count(); ?></td></tr>
			<tr><td>Online Users: <a href="online_users.php"><?php echo online_users(); ?></a></td></tr>

		</tbody>
	</table>
</div>