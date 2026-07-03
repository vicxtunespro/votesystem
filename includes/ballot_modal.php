<style>
	#preview_modal .modal-content,
	#platform .modal-content,
	#view .modal-content {
		border-radius: 10px;
		border: none;
		overflow: hidden;
	}

	#preview_modal .modal-header,
	#platform .modal-header,
	#view .modal-header {
		background: var(--blue, #2455D9);
		border-bottom: none;
		padding: 16px 20px;
	}

	#preview_modal .modal-title,
	#platform .modal-title,
	#view .modal-title {
		color: #fff;
		font-weight: 600;
		font-size: 16px;
	}

	#preview_modal .close,
	#platform .close,
	#view .close {
		color: #fff;
		opacity: 0.85;
		text-shadow: none;
	}
	#preview_modal .close:hover,
	#platform .close:hover,
	#view .close:hover { opacity: 1; color: #fff; }

	#preview_modal .modal-body,
	#platform .modal-body,
	#view .modal-body {
		padding: 20px;
	}

	#preview_modal .modal-footer,
	#platform .modal-footer,
	#view .modal-footer {
		border-top: 1px solid #EDF0F5;
		padding: 14px 20px;
	}

	#platform #plat_view {
		color: #5B6478;
		line-height: 1.6;
		margin: 0;
	}

	.modal-empty {
		text-align: center;
		color: #9BA1AF;
		font-size: 13px;
		padding: 20px 0;
	}
</style>

<!-- Preview -->
<div class="modal fade" id="preview_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Vote Preview</h4>
			</div>
			<div class="modal-body">
				<div id="preview_body"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<!-- Platform -->
<div class="modal fade" id="platform">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><span class="candidate"></span></h4>
			</div>
			<div class="modal-body">
				<p id="plat_view"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<!-- View Ballot -->
<div class="modal fade" id="view">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Your Votes</h4>
			</div>
			<div class="modal-body">
				<?php
					$viewStmt = $conn->prepare("
						SELECT positions.description AS position_desc,
						       candidates.firstname AS canfirst,
						       candidates.lastname AS canlast
						FROM votes
						LEFT JOIN candidates ON candidates.id = votes.candidate_id
						LEFT JOIN positions ON positions.id = votes.position_id
						WHERE votes.voters_id = ?
						ORDER BY positions.priority ASC
					");
					$viewStmt->bind_param("s", $voter['id']);
					$viewStmt->execute();
					$viewResult = $viewStmt->get_result();

					if($viewResult->num_rows > 0){
						while($row = $viewResult->fetch_assoc()){
							echo "
								<div class='votelist'>
									<span class='votelist-position'>".htmlspecialchars($row['position_desc'])."</span>
									<span class='votelist-candidate'>".htmlspecialchars($row['canfirst'].' '.$row['canlast'])."</span>
								</div>
							";
						}
					}
					else{
						echo "<p class='modal-empty'>No votes found for this ballot.</p>";
					}
					$viewStmt->close();
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
			</div>
		</div>
	</div>
</div>