<?php 
include 'core/init.php';
protect_page();
admin_page();
include 'includes/overall/header.php'; 
?>

<script type="text/javascript">
	$(document).ready(function(){
		$(".tabContents").hide(); // Hide all tab conten divs by default
		$(".tabContents:first").show(); // Show the first div of tab content by default
		
		$("#tabContainer ul li a").click(function(){ //Fire the click event
			
			var activeTab = $(this).attr("href"); // Catch the click link
			$("#tabContainer ul li a").removeClass("active"); // Remove pre-highlighted link
			$(this).addClass("active"); // set clicked link to highlight state
			$(".tabContents").hide(); // hide currently visible tab content div
			$(activeTab).fadeIn(); // show the target tab content div by matching clicked link.
		});
	});
</script>


<h2>Website Settings</h2>
<?php
if(empty($_POST) == false) {
	$fields = array('title');
	foreach($_POST as $key=>$value) {
		if(empty($value) && in_array($key, $fields) == true){
			$errors[] = 'All fields are required';
			break 1;
		}
	}
	
	if(strlen(urlencode($_POST['advertise_top'])) > 2550){
		$errors[] = "Buddy, less characters on ads-content please! (limit: 2555)";
	}
	if(strlen(urlencode($_POST['advertise_bottom'])) > 2550){
		$errors[] = "Buddy, less characters on ads-content please! (limit: 2555)";
	}
	
	if(!empty($errors)){
		echo output_errors($errors);
	}else{
		$advertise_top        = (!empty($_POST['advertise_top'])) ? urlencode($_POST['advertise_top']) : "false";
		$advertise_bottom     = (!empty($_POST['advertise_bottom'])) ? urlencode($_POST['advertise_bottom']) : "false";
		$title                = $_POST['title'];
		$pagination           = (INT)$_POST['pagination'];
		$register             = (isset($_POST['register']) == true) ? 1 : 0;
		$show_offline_servers = (isset($_POST['show_offline_servers']) == true) ? 1 : 0;
		$email_confirmation   = (isset($_POST['email_confirmation']) == true) ? 1 : 0;
		$server_confirmation  = (isset($_POST['server_confirmation']) == true) ? 1 : 0;
		$twitter			  = (empty($_POST['twitter']) !== true) ? $_POST['twitter'] : "false";
		$facebook			  = (empty($_POST['facebook']) !== true) ? $_POST['facebook'] : "false";
		$contact_email 		  = $_POST['contact_email'];
		$server_cache 		  = $_POST['server_cache'];
		$disable_index_querying  = (isset($_POST['disable_index_querying']) == true) ? 1 : 0;
		$vote_login_restriction  = (isset($_POST['vote_login_restriction']) == true) ? 1 : 0;

		$database->query("UPDATE settings SET `title` = '$title', `disable_index_querying` = '$disable_index_querying', `vote_login_restriction` = '$vote_login_restriction', `facebook` = '$facebook', `twitter` = '$twitter', `contact_email` = '$contact_email', `pagination` = '$pagination', `register` = '$register',  `server_cache` = '$server_cache', `advertise_top` = '$advertise_top', `advertise_bottom` = '$advertise_bottom', `show_offline_servers` = '$show_offline_servers', `email_confirmation` = '$email_confirmation', `server_confirmation` = '$server_confirmation' WHERE `id` = 1");
		header('Location: adm_general_settings.php');
	}
}
?>
<div id="tabContainer">
<ul style="list-style-type:none;margin-left:0;">
	<li style="display:inline;margin-right:5px;"><a class="active" href="#tab1"><button class="btn">Basic Settings</button></a></li>
	<li style="display:inline;margin-right:5px;"><a href="#tab2"><button class="btn">Server Settings</button></a></li>
	<li style="display:inline;margin-right:5px;"><a href="#tab3"><button class="btn">Ads Settings</button></a></li>
</ul>
	
	
	<form action="" method="post" >
		<div id="tab1" class="tabContents">
			<label>Website Title</label>
			<input class="span3" type="text" name="title" value="<?php echo $settings['title']; ?>"/>
	
			<label>Owner Email(for contact form)</label>
			<input class="span3" type="text" name="contact_email" value="<?php echo $settings['contact_email']; ?>" />
			
			<label>Facebook </label>
			<input class="span3" type="text" name="facebook" value="<?php echo $settings['facebook']; ?>" placeholder="Facebook username"/>
			
			<label>Twitter  </label>
			<input class="span3" type="text" name="twitter" value="<?php echo $settings['twitter']; ?>" placeholder="Twitter username"/>
	
			<label class="checkbox">
				Register (enabled / disabled) <input type="checkbox" name="register" value=<?php echo "\"" . $settings['register'] . "\""; if($settings['register'] == 1) echo "checked";?>/>
			</label>
			<label class="checkbox">
				Users need to email confirmate their account?<input type="checkbox" name="email_confirmation" value=<?php echo "\"" . $settings['email_confirmation'] . "\""; if($settings['email_confirmation'] == 1) echo "checked";?>/>
			</label>
			<label class="checkbox">
			Vote Login Restriction (account needed to vote) <input type="checkbox" name="vote_login_restriction" value=<?php echo "\"" . $settings['vote_login_restriction'] . "\""; if($settings['vote_login_restriction'] == 1) echo "checked";?>/>
			</label>
		</div>
		<div id="tab2" class="tabContents">
			<label>Pagination(Showing X servers / page)</label>
			<input class="span3" type="text" name="pagination" value="<?php echo $settings['pagination']; ?>"/>
	
			<label>Servers Cache(in seconds)</label>
			<input class="span3" type="text" name="server_cache" value="<?php echo $settings['server_cache']; ?>"/>
	
			<label class="checkbox">
			Show Offline Servers? <input type="checkbox" name="show_offline_servers" value=<?php echo "\"" . $settings['show_offline_servers'] . "\""; if($settings['show_offline_servers'] == 1) echo "checked";?>/>
			</label>

			<label class="checkbox">
			Disable Index Querying? <input type="checkbox" name="disable_index_querying" value=<?php echo "\"" . $settings['disable_index_querying'] . "\""; if($settings['disable_index_querying'] == 1) echo "checked";?>/>
			</label>


			
			
			<label class="checkbox">
				Admins need to manual activate new servers?<input type="checkbox" name="server_confirmation" value=<?php echo "\"" . $settings['server_confirmation'] . "\""; if($settings['server_confirmation'] == 1) echo "checked";?>/>
			</label>
		</div>
		<div id="tab3" class="tabContents">
			To deactivate the function, only write 'false' in the textarea!<br />
			<h3>Top Ads</h3>
			<textarea style="width:95%;height:200px;" name="advertise_top" ><?php echo urldecode($settings['advertise_top']);?></textarea>
		
			<h3>Bottom Ads</h3>
			<textarea style="width:95%;height:200px;" name="advertise_bottom" ><?php echo urldecode($settings['advertise_bottom']);?></textarea>

		</div>
		<br />
		<input class="btn btn-primary" type="submit" value="Change Settings">
		

	</form>
</div>




	
</form>

<?php
include 'includes/overall/footer.php'; 
?>