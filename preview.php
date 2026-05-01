<?php
	
	include 'includes/session.php';
	include 'includes/slugify.php';

	$output = array('error'=>false,'list'=>'');

	$sql = "SELECT * FROM positions";
	$query = $conn->query($sql);

	while($row = $query->fetch_assoc()){
		$position = slugify($row['description']);
		$pos_id = $row['id'];
		if(isset($_POST[$position])){
			if($row['max_vote'] > 1){
				if(count($_POST[$position]) > $row['max_vote']){
					$output['error'] = true;
					$output['message'][] = '<li>You can only choose '.$row['max_vote'].' candidates for '.$row['description'].'</li>';
				}
				else{
					foreach($_POST[$position] as $key => $values){
						$candidate = (int) $values;
						$stmt = $conn->prepare("SELECT * FROM candidates WHERE id = ? AND position_id = ?");
						$stmt->bind_param("ii", $candidate, $pos_id);
						$stmt->execute();
						$cmquery = $stmt->get_result();
						$cmrow = $cmquery->fetch_assoc();
						$stmt->close();
						if($cmrow){
							$output['list'] .= "
								<div class='row votelist'>
			                      	<span class='vote-position'>".e($row['description'])."</span>
			                      	<span class='vote-choice'>".e($cmrow['firstname'])." ".e($cmrow['lastname'])."</span>
			                    </div>
							";
						}
					}

				}
				
			}
			else{
				$candidate = (int) $_POST[$position];
				$stmt = $conn->prepare("SELECT * FROM candidates WHERE id = ? AND position_id = ?");
				$stmt->bind_param("ii", $candidate, $pos_id);
				$stmt->execute();
				$csquery = $stmt->get_result();
				$csrow = $csquery->fetch_assoc();
				$stmt->close();
				if($csrow){
					$output['list'] .= "
						<div class='row votelist'>
	                      	<span class='vote-position'>".e($row['description'])."</span>
	                      	<span class='vote-choice'>".e($csrow['firstname'])." ".e($csrow['lastname'])."</span>
	                    </div>
					";
				}
			}

		}
		
	}

	if(!$output['error'] && $output['list'] == ''){
		$output['error'] = true;
		$output['message'][] = '<li>You must vote atleast one candidate</li>';
	}

	echo json_encode($output);


?>
