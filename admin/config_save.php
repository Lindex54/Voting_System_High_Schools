<?php
	include 'includes/session.php';

	$return = 'home.php';
	if(isset($_GET['return'])){
		$return = basename($_GET['return']);
	}

	if(isset($_POST['save'])){
		if(!require_csrf()){
			header('location: '.$return);
			exit();
		}

		$title = clean_input($_POST['title']);
		$status = in_array($_POST['status'] ?? 'closed', array('draft', 'open', 'closed')) ? $_POST['status'] : 'closed';
		$start_at = clean_input($_POST['start_at'] ?? '');
		$end_at = clean_input($_POST['end_at'] ?? '');
		$start_at_time = $start_at === '' ? false : strtotime($start_at);
		$end_at_time = $end_at === '' ? false : strtotime($end_at);
		$start_at = $start_at_time === false ? null : date('Y-m-d H:i:s', $start_at_time);
		$end_at = $end_at_time === false ? null : date('Y-m-d H:i:s', $end_at_time);

		$file = 'config.ini';
		$content = 'election_title = '.$title;

		file_put_contents($file, $content);

		$stmt = $conn->prepare("INSERT INTO election_settings (id, title, status, start_at, end_at) VALUES (1, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), status = VALUES(status), start_at = VALUES(start_at), end_at = VALUES(end_at)");
		$stmt->bind_param("ssss", $title, $status, $start_at, $end_at);
		if($stmt->execute()){
			$_SESSION['success'] = 'Election settings updated successfully';
		}
		else{
			$_SESSION['error'] = $stmt->error;
		}
		$stmt->close();
		
	}
	else{
		$_SESSION['error'] = "Fill up config form first";
	}

	header('location: '.$return);

?>
