<?php
	include 'includes/session.php';
	include 'includes/slugify.php';

	if(isset($_POST['vote'])){
		if(!require_csrf()){
			header('location: home.php');
			exit();
		}

		if(!election_is_open($conn)){
			$_SESSION['error'][] = 'Voting is not open at this time.';
		}
		elseif(count($_POST) <= 2){
			$_SESSION['error'][] = 'Please vote atleast one candidate';
		}
		else{
			$stmt = $conn->prepare("SELECT id FROM votes WHERE voters_id = ? LIMIT 1");
			$stmt->bind_param("i", $voter['id']);
			$stmt->execute();
			$vquery = $stmt->get_result();
			$stmt->close();

			if($vquery->num_rows > 0){
				$_SESSION['error'][] = 'You have already voted for this election.';
				header('location: home.php');
				exit();
			}

			$_SESSION['post'] = $_POST;
			$sql = "SELECT * FROM positions";
			$query = $conn->query($sql);
			$error = false;
			$votes = array();
			while($row = $query->fetch_assoc()){
				$position = slugify($row['description']);
				$pos_id = $row['id'];
				if(isset($_POST[$position])){
					if($row['max_vote'] > 1){
						if(count($_POST[$position]) > $row['max_vote']){
							$error = true;
							$_SESSION['error'][] = 'You can only choose '.$row['max_vote'].' candidates for '.$row['description'];
						}
						else{
							foreach($_POST[$position] as $key => $values){
								$candidate = (int) $values;
								$stmt = $conn->prepare("SELECT id FROM candidates WHERE id = ? AND position_id = ?");
								$stmt->bind_param("ii", $candidate, $pos_id);
								$stmt->execute();
								$cquery = $stmt->get_result();
								$stmt->close();
								if($cquery->num_rows > 0){
									$votes[] = array($voter['id'], $candidate, $pos_id);
								}
							}

						}
						
					}
					else{
						$candidate = (int) $_POST[$position];
						$stmt = $conn->prepare("SELECT id FROM candidates WHERE id = ? AND position_id = ?");
						$stmt->bind_param("ii", $candidate, $pos_id);
						$stmt->execute();
						$cquery = $stmt->get_result();
						$stmt->close();
						if($cquery->num_rows > 0){
							$votes[] = array($voter['id'], $candidate, $pos_id);
						}
					}

				}
				
			}

			if(!$error){
				$stmt = $conn->prepare("INSERT INTO votes (voters_id, candidate_id, position_id) VALUES (?, ?, ?)");
				foreach($votes as $vote_row){
					$stmt->bind_param("iii", $vote_row[0], $vote_row[1], $vote_row[2]);
					$stmt->execute();
				}
				$stmt->close();

				unset($_SESSION['post']);
				$_SESSION['success'] = 'Ballot Submitted';

			}

		}

	}
	else{
		$_SESSION['error'][] = 'Select candidates to vote first';
	}

	header('location: home.php');

?>
