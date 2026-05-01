<?php
	include 'includes/session.php';

	if(isset($_GET['return'])){
		$return = basename($_GET['return']);
		
	}
	else{
		$return = 'home.php';
	}

	if(isset($_POST['save'])){
		if(!require_csrf()){
			header('location:'.$return);
			exit();
		}

		$curr_password = $_POST['curr_password'];
		$username = clean_input($_POST['username']);
		$password = $_POST['password'];
		$firstname = clean_input($_POST['firstname']);
		$lastname = clean_input($_POST['lastname']);
		if(password_verify($curr_password, $user['password'])){
			if(!empty($_FILES['photo']['name'])){
				try{
					$filename = upload_photo($_FILES['photo'], '../images');
				}
				catch(RuntimeException $e){
					$_SESSION['error'] = $e->getMessage();
					header('location:'.$return);
					exit();
				}
			}
			else{
				$filename = $user['photo'];
			}

			if($password == '' || $password == $user['password']){
				$password = $user['password'];
			}
			else{
				$password = password_hash($password, PASSWORD_DEFAULT);
			}

			$stmt = $conn->prepare("UPDATE admin SET username = ?, password = ?, firstname = ?, lastname = ?, photo = ? WHERE id = ?");
			$stmt->bind_param("sssssi", $username, $password, $firstname, $lastname, $filename, $user['id']);
			if($stmt->execute()){
				$_SESSION['success'] = 'Admin profile updated successfully';
			}
			else{
				$_SESSION['error'] = $stmt->error;
			}
			$stmt->close();
			
		}
		else{
			$_SESSION['error'] = 'Incorrect password';
		}
	}
	else{
		$_SESSION['error'] = 'Fill up required details first';
	}

	header('location:'.$return);

?>
