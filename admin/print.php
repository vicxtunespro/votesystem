<?php
include 'includes/session.php';

require '../vendor/autoload.php';

use Dompdf\Dompdf;

function generateRows($conn) {
    $html = '';

    $sql = "SELECT * FROM positions ORDER BY priority ASC";
    $query = $conn->query($sql);

    while ($position = $query->fetch_assoc()) {

        $positionId = $position['id'];

        $html .= "
        <div class='position'>
            <h2>{$position['description']}</h2>

            <table>
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Votes</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
        ";

        $candidates = [];

        $sql = "SELECT * FROM candidates WHERE position_id = '$positionId' ORDER BY lastname ASC";
        $cquery = $conn->query($sql);

        while ($crow = $cquery->fetch_assoc()) {
            $vsql = "SELECT COUNT(*) as total FROM votes WHERE candidate_id = '".$crow['id']."'";
            $vquery = $conn->query($vsql);
            $vrow = $vquery->fetch_assoc();

            $candidates[] = [
                'name' => $crow['lastname'] . ', ' . $crow['firstname'],
                'votes' => $vrow['total']
            ];
        }

        usort($candidates, fn($a, $b) => $b['votes'] - $a['votes']);

        $rank = 1;

        foreach ($candidates as $c) {

            $status = ($rank == 1) ? "WINNER" : "Candidate";

            $html .= "
                <tr>
                    <td>{$c['name']}</td>
                    <td>{$c['votes']}</td>
                    <td>{$status}</td>
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
        font-family: Arial, sans-serif;
        margin: 20px;
        color: #333;
    }

    .header {
        text-align: center;
        margin-bottom: 30px;
    }

    .header h1 {
        margin: 0;
        font-size: 24px;
        letter-spacing: 1px;
    }

    .header h3 {
        margin: 5px 0;
        font-weight: normal;
        color: #666;
    }

    .position {
        margin-bottom: 30px;
        page-break-inside: avoid;
    }

    .position h2 {
        font-size: 18px;
        border-left: 5px solid #2c3e50;
        padding-left: 10px;
        margin-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th {
        background: #2c3e50;
        color: white;
        padding: 10px;
        text-align: left;
        font-size: 13px;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        font-size: 13px;
    }

    tr:nth-child(even) {
        background: #f9f9f9;
    }

    .winner {
        font-weight: bold;
        color: #27ae60;
    }
</style>
</head>

<body>

<div class='header'>
    <h1>{$title}</h1>
    <h3>Election Results Report</h3>
</div>

" . generateRows($conn) . "

</body>
</html>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("election_results.pdf", ["Attachment" => false]);
?>