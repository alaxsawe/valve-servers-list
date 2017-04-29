<div class="navbar navbar-static-top">
	<div class="navbar-inner">
		<div class="container">
			<script type="text/javascript">  
			$('.dropdown-toggle').dropdown()
			</script>

			<a class="brand" style="margin-left:0px;" href="index.php"><?php echo $settings['title']; ?></a>
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div class="nav-collapse collapse">
				<ul class="nav" style="margin:0;">
					<li><a href="index.php">Home</a></li>
					<?php if(logged_in() == false) { ?>
					<li><a href="login.php">Login</a></li>
					<li><a href="register.php">Register</a></li>
					<?php } else { ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">My account<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="add_server.php">Add Server</a></li>
							<li><a href="my_servers.php">My Servers</a></li>
							<li><a href="profile-<?php echo $user_data['username']; ?>">My Profile</a></li>
							<li><a href="changesettings.php">Profile Settings</a></li>
							<li><a href="changepassword.php">Change Password</a></li>
							<li><a href="logout.php">Logout</a></li>
						</ul>
					</li>
					
					
					<?php if(is_admin($session_user_id) == true) { ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="adm_general_settings.php">Website Settings</a></li>
							<li>
								<a href="adm_servers_management.php">Servers Management
								<?php if(disabled_servers_count() > 0){ ?>
								<span class="badge badge-important"><?php echo disabled_servers_count(); ?></span>
								<?php } ?>
								</a>
							</li>
							<li>
								<a href="adm_users_management.php">Users Management
								<?php if(disabled_users_count() > 0){ ?>
								<span class="badge badge-important"><?php echo disabled_users_count(); ?></span>
								<?php } ?>
								</a>
							</li>
							<li>
								<a href="deleteVotes.php">Reset Votes</a></li>
							</li>
						</ul>
					</li>
					<?php } } ?>
					<li><a href="contact.php">Contact</a></li>
				</ul>
					
				<form method="get" action="search.php" class="navbar-search pull-right">
					<input name="term" type="text" class="search-query" placeholder="Search...">
				</form>
			</div>
		</div>
	</div>
</div>
