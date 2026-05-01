<?php
	include 'includes/session.php';

	if(isset($_POST['import'])){
		if(!require_csrf()){
			header('location: voters.php');
			exit();
		}

		if(empty($_FILES['csv']['name']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK){
			$_SESSION['error'] = 'Please choose a valid CSV file.';
			header('location: voters.php');
			exit();
		}

		$ext = strtolower(pathinfo($_FILES['csv']['name'], PATHINFO_EXTENSION));
		if($ext !== 'csv'){
			$_SESSION['error'] = 'Only CSV files are allowed.';
			header('location: voters.php');
			exit();
		}

		$handle = fopen($_FILES['csv']['tmp_name'], 'r');
		if(!$handle){
			$_SESSION['error'] = 'Could not read CSV file.';
			header('location: voters.php');
			exit();
		}

		$added = 0;
		$skipped = 0;
		$rowNumber = 0;
		$stmt = $conn->prepare("INSERT INTO voters (voters_id, student_number, password, firstname, lastname, class, stream, photo) VALUES (?, ?, ?, ?, ?, ?, ?, '')");

		while(($row = fgetcsv($handle)) !== false){
			$rowNumber++;
			if($rowNumber === 1 && isset($row[1]) && strtolower(trim($row[1])) === 'firstname'){
				continue;
			}

			$student_number = clean_input($row[0] ?? '');
			$firstname = clean_input($row[1] ?? '');
			$lastname = clean_input($row[2] ?? '');
			$class = clean_input($row[3] ?? '');
			$stream = clean_input($row[4] ?? '');
			$plain_password = clean_input($row[5] ?? '');

			if($firstname === '' || $lastname === ''){
				$skipped++;
				continue;
			}

			if($plain_password === ''){
				$plain_password = $student_number !== '' ? $student_number : 'voter123';
			}

			$voter_id = generate_voter_id($conn);
			$password = password_hash($plain_password, PASSWORD_DEFAULT);
			$stmt->bind_param("sssssss", $voter_id, $student_number, $password, $firstname, $lastname, $class, $stream);

			if($stmt->execute()){
				$added++;
			}
			else{
				$skipped++;
			}
		}

		$stmt->close();
		fclose($handle);

		$_SESSION['success'] = "CSV import completed. Added: $added. Skipped: $skipped.";
	}
	else{
		$_SESSION['error'] = 'Choose a CSV file first.';
	}

	header('location: voters.php');
?>
