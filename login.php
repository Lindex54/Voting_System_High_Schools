<?php
	session_start();
	include 'includes/conn.php';
	include 'includes/functions.php';

	if(isset($_POST['login'])){
		$voter = clean_input($_POST['voter']);
		$password = $_POST['password'];

		$stmt = $conn->prepare("SELECT * FROM voters WHERE voters_id = ?");
		$stmt->bind_param("s", $voter);
		$stmt->execute();
		$query = $stmt->get_result();

		if($query->num_rows < 1){
			$_SESSION['error'] = 'Cannot find voter with the ID';
		}
		else{
			$row = $query->fetch_assoc();
			if(password_verify($password, $row['password'])){
				$_SESSION['voter'] = $row['id'];
			}
			else{
				$_SESSION['error'] = 'Incorrect password';
			}
		}
		$stmt->close();
		
	}
	else{
		$_SESSION['error'] = 'Input voter credentials first';
	}

	header('location: index.php');

?>
