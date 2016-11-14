<?php
extract($_POST);

login($email, $pass);

/*function to load the login page with error message*/
function invalidDetails($email, $msg){
	$url = '/index.php?'.'msg='.$msg.'&email='.$email;
	header("Location: ".$url);
}

/*function to logg the user in*/
function login($email, $pass){
	require 'DB_info.php';
	
	$conn = new mysqli($DB_host, $DB_user, $DB_password, $DB_database);
	$query = $conn->prepare('SELECT * FROM '.$DB_table.' WHERE EMAIL=?');
	$query->bind_param('s', $email);
	$query->execute();
	$result = $query->get_result();	
	
	if($result->num_rows < 1)
		invalidDetails($email, 'Email id not registered');
	else{
		$result->data_seek(0);
		$result = $result->fetch_array(MYSQLI_ASSOC);
				
		if($result['PASS'] != $pass)
			invalidDetails($email, 'Wrong password');
		else
			print_user($result);
	}
}

/*function to display the user information*/
function print_user($result){
	extract($result);
	$img_src = '"data:image/*;base64,'.base64_encode($PHOTO).'"';
	echo <<<_END
	<html><body>
		<table style='margin-top:5%'>
			<tr>
				<td>
					<img src=$img_src height='400px' width='400px' />
				</td>
				<td>
					<table>
						<tr>
							<td><h4>First name: </h4></td>
							<td><h5>$FNAME</h5></td>
						</tr>
						
						<tr>
							<td><h4>Last name: </h4></td>
							<td><h5>$LNAME</h5></td>
						</tr>
						
						<tr>
							<td><h4>Nick name: </h4></td>
							<td><h5>$NICK</h5></td>
						</tr>
						
						<tr>
							<td><h4>Phone number:&nbsp;&nbsp;&nbsp;&nbsp;</h4></td>
							<td><h5>$PHN</h5></td>
						</tr>
						
						<tr>
							<td><h4>Email address: </h4></td>
							<td><h5>$EMAIL</h5></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body></html>
_END;
}
?>