<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		if(!require_csrf()){
			header('location: voters.php');
			exit();
		}

		$firstname = clean_input($_POST['firstname']);
		$lastname = clean_input($_POST['lastname']);
		$student_number = clean_input($_POST['student_number'] ?? '');
		$class = clean_input($_POST['class'] ?? '');
		$stream = clean_input($_POST['stream'] ?? '');
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$filename = '';
		try{
			$filename = upload_photo($_FILES['photo'], '../images');
		}
		catch(RuntimeException $e){
			$_SESSION['error'] = $e->getMessage();
			header('location: voters.php');
			exit();
		}

		$voter = generate_voter_id($conn);

		$stmt = $conn->prepare("INSERT INTO voters (voters_id, student_number, password, firstname, lastname, class, stream, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssssss", $voter, $student_number, $password, $firstname, $lastname, $class, $stream, $filename);
		if($stmt->execute()){
			$_SESSION['success'] = 'Voter added successfully. Voter ID: '.$voter;
		}
		else{
			$_SESSION['error'] = $stmt->error;
		}
		$stmt->close();

	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: voters.php');
?>
