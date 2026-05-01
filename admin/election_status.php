<?php
	include 'includes/session.php';

	if(isset($_POST['set_status'])){
		if(!require_csrf()){
			header('location: home.php');
			exit();
		}

		$status = in_array($_POST['status'] ?? 'closed', array('draft', 'open', 'closed')) ? $_POST['status'] : 'closed';
		$stmt = $conn->prepare("UPDATE election_settings SET status = ? WHERE id = 1");
		$stmt->bind_param("s", $status);

		if($stmt->execute()){
			$statusLabel = array('draft' => 'Not Started', 'open' => 'Open', 'closed' => 'Closed');
			$_SESSION['success'] = 'Election status set to '.$statusLabel[$status].'.';
		}
		else{
			$_SESSION['error'] = $stmt->error;
		}

		$stmt->close();
	}
	else{
		$_SESSION['error'] = 'Choose an election status first.';
	}

	header('location: home.php');
?>
