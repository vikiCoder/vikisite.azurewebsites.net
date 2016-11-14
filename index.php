<?php

$email;

if(isset($_GET['email']))
	$email = $_GET['email'];
else
	$email = '';

$login_page = <<<_END
<html>
	<head><title>Login to mysite</title></head>

	<body>

		<div align='center'>
			<h1>My Site<h1>
		</div>
	
		<div align='center' style='width:40%; margin-left:auto; margin-right:auto; margin-top:70px'>
	
			<h3 align='center'>Login</h3>
	
			<form method='post' action='user.php'>
	
				<table>
					<tr>
						<td>Email: </td>
						<td><input type='txt' name='email' required='required' value=$email></td>
					</tr>
	
					<tr>
						<td>Password: </td>
						<td><input type='password' required='required' name='pass'></td>
					</tr>	
				</table>
	
				<br>
				<input type='submit' value='Login'>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='button' value='Sign up' onclick="location.href='signup.php';">
	
			</form>
	
		</div>
	
	</body>
</html>
_END;

echo $login_page;

if($_GET != NULL){
	$msg = $_GET['msg'];
	echo '<html><body><h5 style="color:red; text-align:center;">'.$msg.'</h5></body></html>';
}

?>