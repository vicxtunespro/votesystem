<?php

	include 'includes/session.php';
	include 'includes/slugify.php';

	$output = array('error' => false, 'list' => '');

	function renderVoteRow($position, $candidate){
		return '
			<div class="votelist">
				<span class="votelist-position">'.htmlspecialchars($position).'</span>
				<span class="votelist-candidate">'.htmlspecialchars($candidate).'</span>
			</div>
		';
	}

	$sql = "SELECT * FROM positions ORDER BY priority ASC";
	$query = $conn->query($sql);

	$candidateStmt = $conn->prepare("SELECT firstname, lastname FROM candidates WHERE id = ?");

	while($row = $query->fetch_assoc()){
		$position = slugify($row['description']);

		if(isset($_POST[$position])){
			if($row['max_vote'] > 1){
				$selected = (array) $_POST[$position];
				if(count($selected) > $row['max_vote']){
					$output['error'] = true;
					$output['message'][] = '<li>You can only choose '.$row['max_vote'].' candidates for '.htmlspecialchars($row['description']).'</li>';
					continue;
				}
				foreach($selected as $candidateId){
					$candidateStmt->bind_param("i", $candidateId);
					$candidateStmt->execute();
					$cmrow = $candidateStmt->get_result()->fetch_assoc();
					if($cmrow){
						$output['list'] .= renderVoteRow($row['description'], $cmrow['firstname'].' '.$cmrow['lastname']);
					}
				}
			}
			else{
				$candidateId = $_POST[$position];
				$candidateStmt->bind_param("i", $candidateId);
				$candidateStmt->execute();
				$csrow = $candidateStmt->get_result()->fetch_assoc();
				if($csrow){
					$output['list'] .= renderVoteRow($row['description'], $csrow['firstname'].' '.$csrow['lastname']);
				}
			}
		}
	}
	$candidateStmt->close();

	echo json_encode($output);

?>