<?php
include 'core/init.php';
protect_page();


if(empty($_POST) == false) {
	$fields = array('name', 'email');
	foreach($_POST as $key=>$value) {
		if(empty($value) && in_array($key, $fields) == true){
			$errors[] = 'All fields are required';
			break 1;
		}
	}
	
	$_POST['name']      = htmlspecialchars($_POST['name'], ENT_QUOTES);
	$_POST['email']     = htmlspecialchars($_POST['email'], ENT_QUOTES);
	$avatar             = (empty($_FILES['avatar']['name']) == false) ? true : false;

	
	
	if(empty($errors) == true) {
		if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
			$errors[] = 'A valid email adress is required';
		}
		if(email_exists($_POST['email']) === true && $_POST['email'] !== $user_data['email']) {
			$errors[] = 'Sorry, the email \'' . $_POST['email'] . '\' is already in use';
		}
		if($avatar == true) {
			$allowed_extensions = array("png", "jpg", "jpeg");
			$file_name          = $_FILES['avatar']['name'];
				$file_extension = explode(".", $file_name);
				$file_extension = strtolower(end($file_extension));
			$file_temp          = $_FILES['avatar']['tmp_name'];
			
			if(in_array($file_extension, $allowed_extensions) !== true) {
				$errors[] = "Incorrect file type.";
			}
			
			list($avatar_width, $avatar_height) = getimagesize($file_temp); 
			if($avatar_width > 100 || $avatar_height > 100) {
				$errors[] = "Your image is too big!";
			}
		}
	}

	
}

include 'includes/overall/header.php'; 
?>
<h2>Profile Settings</h2>
<?php
if(isset($_GET['success']) && empty($_GET['success'])) {
	echo '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Congratulations!</strong> The profile settings were successfully updated ! <a href="changesettings.php"> Back </a></div>';
} else {
if(empty($_POST) == false && empty($errors) == true) {
	if($avatar == true) {
		send_avatar($file_temp, $file_extension);
		$update_data = array(
			'name'   => $_POST['name'],
			'email'  => $_POST['email'],
			'avatar' => $file_path
		);
		unlink($user_data['avatar']);
	} else {
		$update_data = array(
			'name'   => $_POST['name'],
			'email'  => $_POST['email']
		);
	}
	update_user($_SESSION['user_id'], $update_data);
	header('Location: changesettings.php?success');
} else if (empty($errors) == false) {
	echo output_errors($errors);
}
?>
<form action="" method="post" enctype="multipart/form-data">
	<label>Avatar</label>
	<?php if(empty($user_data['avatar']) == false) {
		echo "<img src='" . $user_data['avatar'] . "' alt='" . $user_data['name'] . "' />";
	}
	?><br />
	<input type="file" name="avatar" />

	<label>Name</label>
	<input type="text" name="name" value="<?php echo $user_data['name']; ?>" />

	<label>Email</label>
	<input type="text" name="email" value="<?php echo $user_data['email']; ?>" />
	<br />
	<input class="btn btn-primary" type="submit" value="Submit Settings" />
</form>
	
<?php
}
include 'includes/overall/footer.php';
?>