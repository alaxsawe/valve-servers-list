<?php 
include 'core/init.php';
logged_in_redirect();
include 'includes/overall/header.php'; 

if(isset($_GET['success']) == true && empty($_GET['success']) == true) {
?>
	<h2>Your account has been activated!</h2>
	<p>You are free to login into your account!</p>
<?php
}else if(isset($_GET['email'], $_GET['email_code']) == true) {
	$email = trim($_GET['email']);
	$email_code = trim($_GET['email_code']);

	if(email_exists($email) == false) {
		$errors[] = 'We couln\'t find your email adress !';
	}else if(activate($email, $email_code) == false) {
		$errors[] = 'We had problems activating your account...';
	}
	
	if(empty($errors) == false) {
	?>
		<h2>Oops...</h2>
	<?php
		echo output_errors($errors);
	} else {
		header('Location: activate.php?success');
	}
} else {
	header('Location: index.php');
	exit();
}

include 'includes/overall/footer.php';
?>