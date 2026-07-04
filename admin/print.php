<?php
include 'includes/session.php';

require '../vendor/autoload.php';

use Dompdf\Dompdf;

function generateRows($conn) {
    $html = '';
    $totalVotesCast = 0;

    // Get total votes cast
    $totalSql = "SELECT COUNT(DISTINCT voters_id) as total FROM votes";
    $totalQuery = $conn->query($totalSql);
    $totalRow = $totalQuery->fetch_assoc();
    $totalVoters = $totalRow['total'];

    $sql = "SELECT * FROM positions ORDER BY priority ASC";
    $query = $conn->query($sql);

    while ($position = $query->fetch_assoc()) {
        $positionId = $position['id'];
        $maxVote = $position['max_vote'];
        
        $candidates = [];
        $totalVotesForPosition = 0;

        // Get candidates with their vote counts
        $sql = "SELECT * FROM candidates WHERE position_id = '$positionId' ORDER BY lastname ASC";
        $cquery = $conn->query($sql);

        while ($crow = $cquery->fetch_assoc()) {
            $vsql = "SELECT COUNT(*) as total FROM votes WHERE candidate_id = '".$crow['id']."'";
            $vquery = $conn->query($vsql);
            $vrow = $vquery->fetch_assoc();
            
            $voteCount = (int)$vrow['total'];
            $totalVotesForPosition += $voteCount;

            $candidates[] = [
                'id' => $crow['id'],
                'name' => $crow['firstname'] . ' ' . $crow['lastname'],
                'photo' => $crow['photo'],
                'votes' => $voteCount
            ];
        }

        // Sort by votes descending
        usort($candidates, fn($a, $b) => $b['votes'] - $a['votes']);

        // Calculate max votes for percentage bars
        $maxVotes = !empty($candidates) ? max(array_column($candidates, 'votes')) : 0;
        
        // Determine winner(s) - handle ties
        $winners = [];
        if (!empty($candidates) && $candidates[0]['votes'] > 0) {
            $topVote = $candidates[0]['votes'];
            foreach ($candidates as $c) {
                if ($c['votes'] == $topVote) {
                    $winners[] = $c['name'];
                }
            }
        }

        $html .= "
        <div class='position'>
            <div class='position-header'>
                <h2>{$position['description']}</h2>
                <span class='badge'>" . ($maxVote > 1 ? "Select {$maxVote}" : "Single") . "</span>
            </div>
            
            <div class='stats-row'>
                <div class='stat-item'>
                    <span class='stat-label'>Total Votes</span>
                    <span class='stat-value'>{$totalVotesForPosition}</span>
                </div>
                <div class='stat-item'>
                    <span class='stat-label'>Candidates</span>
                    <span class='stat-value'>" . count($candidates) . "</span>
                </div>
                " . (!empty($winners) ? "
                <div class='stat-item'>
                    <span class='stat-label'>Winner" . (count($winners) > 1 ? 's' : '') . "</span>
                    <span class='stat-value winner-name'>" . implode(' & ', $winners) . "</span>
                </div>" : "") . "
            </div>

            <table>
                <thead>
                    <tr>
                        <th class='rank-col'>Rank</th>
                        <th class='candidate-col'>Candidate</th>
                        <th class='votes-col'>Votes</th>
                        <th class='bar-col'>Progress</th>
                        <th class='status-col'>Status</th>
                    </tr>
                </thead>
                <tbody>
        ";

        $rank = 1;
        $totalRows = count($candidates);

        foreach ($candidates as $c) {
            $voteCount = $c['votes'];
            $percentage = $maxVotes > 0 ? round(($voteCount / $maxVotes) * 100) : 0;
            
            // Determine status
            $status = '';
            $statusClass = '';
            $rankDisplay = $rank;
            
            if ($rank == 1 && $voteCount > 0) {
                $status = 'WINNER';
                $statusClass = 'winner';
                $rankDisplay = '1st';
            } elseif ($rank == 2 && $voteCount > 0) {
                $status = 'Runner-up';
                $statusClass = 'runner-up';
                $rankDisplay = '2nd';
            } elseif ($rank == 3 && $voteCount > 0) {
                $status = '3rd Place';
                $statusClass = 'third';
                $rankDisplay = '3rd';
            } elseif ($voteCount == 0) {
                $status = 'No Votes';
                $statusClass = 'no-votes';
                $rankDisplay = $rank . 'th';
            } else {
                $status = 'Candidate';
                $statusClass = 'candidate';
                $rankDisplay = $rank . 'th';
            }

            // Bar color based on position
            $barColor = '#3498db';
            if ($rank == 1 && $voteCount > 0) {
                $barColor = '#27ae60';
            } elseif ($rank == 2 && $voteCount > 0) {
                $barColor = '#f39c12';
            } elseif ($rank == 3 && $voteCount > 0) {
                $barColor = '#e67e22';
            }

            $html .= "
                <tr class='" . ($rank == 1 && $voteCount > 0 ? 'winner-row' : '') . "'>
                    <td class='rank-col'><strong>{$rankDisplay}</strong></td>
                    <td class='candidate-col'>
                        <span class='candidate-name'>{$c['name']}</span>
                    </td>
                    <td class='votes-col'><strong>{$voteCount}</strong></td>
                    <td class='bar-col'>
                        <div class='bar-container'>
                            <div class='bar' style='width: {$percentage}%; background-color: {$barColor};'>
                                <span class='bar-label'>{$percentage}%</span>
                            </div>
                        </div>
                    </td>
                    <td class='status-col'>
                        <span class='status-badge {$statusClass}'>{$status}</span>
                    </td>
                </tr>
            ";

            $rank++;
        }

        $html .= "
                </tbody>
            </table>
        </div>
        ";
    }

    return $html;
}

$parse = parse_ini_file('config.ini', FALSE, INI_SCANNER_RAW);
$title = $parse['election_title'];

$dompdf = new Dompdf();

$html = "
<html>
<head>
<style>
    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        margin: 20px;
        color: #2c3e50;
        background: #ffffff;
        line-height: 1.6;
    }

    .header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 3px solid #2c3e50;
    }

    .header h1 {
        margin: 0;
        font-size: 28px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #2c3e50;
    }

    .header h3 {
        margin: 8px 0 0 0;
        font-weight: normal;
        color: #7f8c8d;
        font-size: 16px;
        letter-spacing: 1px;
    }

    .header .sub-info {
        margin-top: 10px;
        font-size: 13px;
        color: #95a5a6;
    }

    .position {
        margin-bottom: 35px;
        page-break-inside: avoid;
    }

    .position-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 5px solid #2c3e50;
        padding-left: 12px;
        margin-bottom: 12px;
    }

    .position-header h2 {
        margin: 0;
        font-size: 18px;
        color: #2c3e50;
        font-weight: 600;
    }

    .badge {
        background: #ecf0f1;
        color: #7f8c8d;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-row {
        display: flex;
        gap: 30px;
        margin-bottom: 15px;
        padding: 12px 15px;
        background: #f8f9fa;
        border-radius: 6px;
        font-size: 13px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stat-label {
        color: #7f8c8d;
        font-weight: 500;
    }

    .stat-value {
        font-weight: 700;
        color: #2c3e50;
    }

    .winner-name {
        color: #27ae60;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0;
        font-size: 13px;
    }

    thead {
        background: #2c3e50;
        color: white;
    }

    th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 10px 12px;
        border-bottom: 1px solid #ecf0f1;
    }

    tr:nth-child(even) {
        background: #fafbfc;
    }

    tr.winner-row {
        background: #f0faf4;
    }

    .rank-col {
        width: 60px;
        text-align: center;
        font-weight: 600;
        color: #7f8c8d;
    }

    .candidate-col {
        min-width: 150px;
    }

    .candidate-name {
        font-weight: 500;
    }

    .votes-col {
        width: 70px;
        text-align: center;
        font-weight: 600;
    }

    .bar-col {
        min-width: 150px;
        max-width: 200px;
    }

    .bar-container {
        background: #ecf0f1;
        border-radius: 4px;
        height: 22px;
        overflow: hidden;
        position: relative;
    }

    .bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 6px;
        min-width: 30px;
    }

    .bar-label {
        color: white;
        font-size: 10px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .status-col {
        width: 100px;
        text-align: center;
    }

    .status-badge {
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.winner {
        background: #27ae60;
        color: white;
    }

    .status-badge.runner-up {
        background: #f39c12;
        color: white;
    }

    .status-badge.third {
        background: #e67e22;
        color: white;
    }

    .status-badge.candidate {
        background: #3498db;
        color: white;
    }

    .status-badge.no-votes {
        background: #bdc3c7;
        color: white;
    }

    .footer {
        text-align: center;
        margin-top: 30px;
        padding-top: 15px;
        border-top: 2px solid #ecf0f1;
        font-size: 12px;
        color: #95a5a6;
    }

    .footer span {
        color: #2c3e50;
        font-weight: 600;
    }

    @page {
        margin: 20px;
        size: A4 portrait;
    }
</style>
</head>

<body>

<div class='header'>
    <h1>{$title}</h1>
    <h3>Official Election Results Report</h3>
    <div class='sub-info'>Generated: " . date('F d, Y h:i A') . " | Total Voters: {$totalVoters}</div>
</div>

" . generateRows($conn) . "

<div class='footer'>
    This report is generated automatically and serves as the official record of the election results.<br>
    Powered by <span>Dementa Technologies</span>
</div>

</body>
</html>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("election_results.pdf", ["Attachment" => false]);
?>