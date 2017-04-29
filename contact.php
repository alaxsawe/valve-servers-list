<?php 
	include 'core/init.php';
	include 'includes/overall/header.php'; 
if(empty($_POST) == false) {
	$fields = array('subject', 'name', 'email', 'message', 'captchavar');
	foreach($_POST as $key=>$value) {
		if(empty($value) && in_array($key, $fields) == true){
			$errors[] = 'All fields are required';
			break 1;
		}
	}
	
//captcha
include_once "core/functions/securimage.php";
$securimage = new Securimage();
$valid = $securimage->check($_POST['captchavar']);
//-------

	$_POST['subject']  = htmlspecialchars($_POST['subject'], ENT_QUOTES);
	$_POST['name']     = htmlspecialchars($_POST['name'], ENT_QUOTES);
	$_POST['email']    = htmlspecialchars($_POST['email'], ENT_QUOTES);
	$_POST['message']  = htmlspecialchars($_POST['message'], ENT_QUOTES);
	
	if(empty($errors) == true) {
		if($valid == false) {
			$errors[] = 'Please enter the correct captcha code!';
		}
		if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
			$errors[] = 'A valid email adress is required';
		}
	}
}
?>
<h2>Contact</h2>
<?php
if(isset($_GET['success']) && empty($_GET['success'])) {
	echo '<font color=\'green\'>Your message has been sent successfully!</font><br>Please allow 24-48 hour for a response!';
} else {
	if(empty($_POST) == false && empty($errors) == true){
		mail($settings['contact_email'],  $_POST['subject'], $_POST['message'], 'From: ' . $_POST['name']);
		header('Location:contact.php?success');
	} elseif(empty($errors) == false) {
		echo output_errors($errors);
	}
}
?>
<form action="" method="post">

	<input class="span4" type="text" name="subject" placeholder="Title"/><br />
	<input class="span4" type="text" name="name" placeholder="Your Name"/><br />
	<input class="span4" type="text" name="email" placeholder="Your Email"/><br />
	<textarea class="span4" rows="6" name="message">Message</textarea><br />

	<img id="captcha" title="Captcha" src="core/functions/securimage_show.php" alt="CAPTCHA Image" class="img-polaroid" />&nbsp;
	<input class="span2" type="text" style="text-transform:uppercase;" name="captchavar" id="captchavar" maxlength="4" placeholder="captcha" /><br /><br />
		
	<input class="btn btn-primary btn-large span4" type="submit" value="Send mail"/>
</form>
<?php include 'includes/overall/footer.php'; ?>