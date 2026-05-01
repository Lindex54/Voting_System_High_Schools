<?php
	include 'includes/session.php';

	if(isset($_POST['id'])){
		if(!require_csrf()){
			echo json_encode(array('error'=>true, 'message'=>'Security check failed.'));
			exit();
		}

		$id = (int) $_POST['id'];

		$sql = "SELECT * FROM positions";
		$pquery = $conn->query($sql);

		$output = array('error'=>false);

		$stmt = $conn->prepare("SELECT * FROM positions WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$query = $stmt->get_result();
		$row = $query->fetch_assoc();
		$stmt->close();

		$priority = $row['priority'] + 1;

		if($priority > $pquery->num_rows){
			$output['error'] = true;
			$output['message'] = 'This position is already at the bottom';
		}
		else{
			$stmt = $conn->prepare("UPDATE positions SET priority = priority - 1 WHERE priority = ?");
			$stmt->bind_param("i", $priority);
			$stmt->execute();
			$stmt->close();

			$stmt = $conn->prepare("UPDATE positions SET priority = ? WHERE id = ?");
			$stmt->bind_param("ii", $priority, $id);
			$stmt->execute();
			$stmt->close();
		}

		echo json_encode($output);

	}
	
?>
