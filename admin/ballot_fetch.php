<?php
	include 'includes/session.php';
	include 'includes/slugify.php';

	if(!require_csrf()){
		echo json_encode('');
		exit();
	}

	$sql = "SELECT * FROM positions";
	$pquery = $conn->query($sql);

	$output = '';
	$candidate = '';

	$sql = "SELECT * FROM positions ORDER BY priority ASC";
	$query = $conn->query($sql);
	$num = 1;
	while($row = $query->fetch_assoc()){
		$input = ($row['max_vote'] > 1) ? '<input type="checkbox" class="flat-red ballot-choice '.slugify($row['description']).'" name="'.slugify($row['description'])."[]".'">' : '<input type="radio" class="flat-red ballot-choice '.slugify($row['description']).'" name="'.slugify($row['description']).'">';

		$sql = "SELECT * FROM candidates WHERE position_id='".$row['id']."'";
		$cquery = $conn->query($sql);
		while($crow = $cquery->fetch_assoc()){
			$image = (!empty($crow['photo'])) ? '../images/'.$crow['photo'] : '../images/profile.jpg';
			$candidate .= '
				<li class="candidate-card">
					<label class="candidate-select">
						'.$input.'
						<span class="select-text">Select</span>
					</label>
					<img src="'.$image.'" height="100px" width="100px" class="candidate-photo">
					<div class="candidate-info">
						<span class="cname">'.$crow['firstname'].' '.$crow['lastname'].'</span>
						<button type="button" class="btn btn-primary btn-sm btn-flat platform"><i class="fa fa-search"></i> Platform</button>
					</div>
				</li>
			';
		}

		$instruct = ($row['max_vote'] > 1) ? 'You may select up to '.$row['max_vote'].' candidates' : 'Select only one candidate';
		
		$updisable = ($row['priority'] == 1) ? 'disabled' : '';
		$downdisable = ($row['priority'] == $pquery->num_rows) ? 'disabled' : '';

		$output .= '
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-solid" id="'.$row['id'].'">
						<div class="box-header with-border">
							<h3 class="box-title"><b>'.$row['description'].'</b></h3>
							<div class="pull-right box-tools">
				                <button type="button" class="btn btn-default btn-sm moveup" data-id="'.$row['id'].'" '.$updisable.'><i class="fa fa-arrow-up"></i> </button>
				                <button type="button" class="btn btn-default btn-sm movedown" data-id="'.$row['id'].'" '.$downdisable.'><i class="fa fa-arrow-down"></i></button>
				            </div>
						</div>
						<div class="box-body">
							<p>'.$instruct.'
								<span class="pull-right">
									<button type="button" class="btn btn-success btn-sm btn-flat reset" data-desc="'.slugify($row['description']).'"><i class="fa fa-refresh"></i> Reset</button>
								</span>
							</p>
							<div id="candidate_list">
								<ul>
									'.$candidate.'
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		';

		$position_id = (int) $row['id'];
		$stmt = $conn->prepare("UPDATE positions SET priority = ? WHERE id = ?");
		$stmt->bind_param("ii", $num, $position_id);
		$stmt->execute();
		$stmt->close();

		$num++;
		$candidate = '';
	}

	echo json_encode($output);

?>
