<?php

/*set the values of field if previously inserted*/
$fname; $lname; $nick; $phn; $email; $pass;

if(isset($_GET['fname'])) $fname = $_GET['fname'];
else $fname = '';
if(isset($_GET['lname'])) $lname = $_GET['lname'];
else $lname = '';
if(isset($_GET['nick'])) $nick = $_GET['nick'];
else $nick = '';
if(isset($_GET['phn'])) $phn = $_GET['phn'];
else $phn = '';
if(isset($_GET['email'])) $email = $_GET['email'];
else $email = '';

/*layout of signup page*/
$signup_page = <<<_END
<html>
	<head><title>Sign up to mysite</title></head>

	<body>

		<div align='center'>
			<h1>My Site<h1>
		</div>

		<div align='center' style='width:60%; margin-left:auto; margin-right:auto; margin-top:70px'>

			<h3 align='center'>Sign up</h3>

			<form method='post' action='signup.php' enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
				<table>
					<tr>
						<td>First name: </td>
						<td><input type='txt' name='fname' required='required' value=$fname></td>
					</tr>
		
					<tr>
						<td>Last name: </td>
						<td><input type='txt' name='lname' required='required' value=$lname></td>
					</tr>
			
					<tr>
						<td>Nick name: </td>
						<td><input type='txt' name='nick' value=$nick></td>
					</tr>
			
					<tr>
						<td>Profile picture: </td>
						<td><input type='file' accept='image/*' required='required' name='photo'></td>
					</tr>
			
					<tr>
						<td>Phone number: </td>
						<td><input type='txt' name='phn' value=$phn></td>
					</tr>
			
					<tr>
						<td>Email: </td>
						<td><input type='txt' name='email' required='required' value=$email></td>
					</tr>
			
					<tr>
						<td>Password: </td>
						<td><input type='password' required='required' name='pass'></td>
					</tr>
			
					<tr>
						<td>Confirm password: </td>
						<td><input type='password' required='required' name='confirm_pass'></td>
					</tr>
				</table>
			
				<br>
				<input type='submit' value='Sign up'>
			
			</form>
		
		</div>
		
	</body>
</html>
_END;

if($_POST == NULL){
	echo $signup_page;
}else{
	
	/*validating the form fields*/
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$nick = $_POST['nick'];
	$phn = $_POST['phn'];
	$email = $_POST['email'];
	$pass = $_POST['pass'];
	$confirm_pass = $_POST['confirm_pass'];
		
	if($pass != $confirm_pass)
		invalidDetails($fname, $lname, $nick, $phn, $email, 'Passwords did not match');
	else if($_FILES["photo"]["error"] > 0){
		switch($_FILES["photo"]["error"]){
			case 1:
			case 2: 
				invalidDetails($fname, $lname, $nick, $phn, $email, $_FILES["photo"]["error"].'Image should be less then '.ini_get('upload_max_filesize')); 
				break;
			case 3:
				invalidDetails($fname, $lname, $nick, $phn, $email, 'Error uploading photo');
				break;
			case 4:
				invalidDetails($fname, $lname, $nick, $phn, $email, 'Please select a profile picture');
				break;
			default:
				invalidDetails($fname, $lname, $nick, $phn, $email, 'Something went wrong, please try again');
				break;
		}
	}
	else
		signup($fname, $lname, $nick, $phn, $email, $pass);
}

/*displaying the error message*/
if($_GET != NULL){
	$msg = $_GET['msg'];
	echo '<html><body><h5 style="color:red; text-align:center;">'.$msg.'</h5></body></html>';
}

/*function to load the same page with error message*/
function invalidDetails($fname, $lname, $nick, $phn, $email, $msg){
	$url = '/signup.php?'.'msg='.$msg.'&fname='.$fname.'&lname='.$lname.'&nick='.$nick.'&phn='.$phn.'&email='.$email;
	header("Location: ".$url);
}

/*function to assure that the given email id is not already registered*/
function check_for_duplicate_email($email){
	require 'DB_info.php';
	
	$conn = new mysqli($DB_host, $DB_user, $DB_password, $DB_database);
	$query = $conn->prepare('SELECT FNAME FROM '.$DB_table.' WHERE EMAIL=?');
	$query->bind_param('s', $email);
	$query->execute();
	$result = $query->get_result();	
	
	if($result->num_rows > 0)
		return false;
	else
		return true;
}

/*function to add user in database*/
function signup($fname, $lname, $nick, $phn, $email, $pass){
	require 'DB_info.php';
	
	if(!check_for_duplicate_email($email))
		invalidDetails($fname, $lname, $nick, $phn, $email, 'This email id is already registered');
	else{
		$conn = new mysqli($DB_host, $DB_user, $DB_password, $DB_database);
		$query = $conn->prepare('INSERT INTO '.$DB_table.'(FNAME,LNAME,NICK,PHN,EMAIL,PASS,PHOTO) VALUES(?,?,?,?,?,?,?)');
		$imgblob = file_get_contents($_FILES['photo']['tmp_name']);
		$query->bind_param('sssisss', $fname, $lname, $nick, $phn, $email, $pass, $imgblob );
		$query->execute();
		//printf("%d Row inserted.\n", $query->affected_rows);
		$query->close();
		$conn->close();
	
		echo <<<_END
		<html>
			<body>
				<h5 style="color:green; text-align:center; margin-top:70px">Signed up successfully</h5>
				<br>
				<center><a href="index.php" style="color:blue; text-align:center;">Log in</a></center>
			</body>
		</html>
_END;
	}
}
?>