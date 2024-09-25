<?php // Generate team information in temp table
 // Drop old information from table
$sql = "TRUNCATE TABLE results_temp";
$pdo->prepare($sql)->execute();

// Get list of teams in a race
$stmt = $pdo->prepare("SELECT DISTINCT racerTeam FROM results WHERE raceSlug = ?");
$stmt->execute([$raceSlug]);

// Determine if anyone on the team forfeitted
while($row = $stmt->fetch()) {
    $racerTeam = $row['racerTeam'];
    $teamForfeit = 'n';
    $stmt2 = $pdo->prepare("SELECT racerForfeit FROM results WHERE raceSlug = ? AND racerTeam = ?");
    $stmt2->execute([$raceSlug, $racerTeam]);
    while($row2 = $stmt2->fetch()) {
        if($row2['racerForfeit'] == 'y') {
            $teamForfeit = 'y';
        }
    }
    if($teamForfeit == 'y') { // Mark team as forfeitted if any player did
        $stmt3 = $pdo->prepare("INSERT INTO results_temp (teamName, teamForfeit) VALUES (?, 'y')");
        $stmt3->execute([$racerTeam]);
    } else { // Get average times/IGT/collection rate if no player forfeitted
        $stmt3 = $pdo->prepare("SELECT AVG(racerRealTime) FROM results WHERE raceSlug = ? AND racerTeam = ?");
        $stmt3->execute([$raceSlug, $racerTeam]);
        $teamAverage = $stmt3->fetchColumn();
        $sqlTemp = "INSERT INTO results_temp (teamForfeit, teamName, averageTime";
        $variableCount = 2;
        $igtGather = 'n';
        $crGather = 'n';
        if($igtCount > 0) {
            $stmt3 = $pdo->prepare("SELECT AVG(racerInGameTime) FROM results WHERE raceSlug = ? AND racerTeam = ? AND racerInGameTime IS NOT NULL");
            $stmt3->execute([$raceSlug, $racerTeam]);
            $teamIGTAverage = $stmt3->fetchColumn();
            $sqlTemp = $sqlTemp . ", averageIGT";
            $variableCount++;
            $igtGather = 'y';
        }
        if($checkCount > 0) {
            $stmt3 = $pdo->prepare("SELECT AVG(racerCheckCount) FROM results WHERE raceSlug = ? AND racerTeam = ? AND racerCheckCount IS NOT NULL");
            $stmt3->execute([$raceSlug, $racerTeam]);
            $teamCRAverage = $stmt3->fetchColumn();
            $sqlTemp = $sqlTemp . ", averageCR";
            $variableCount++;
            $crGather = 'y';
        }
        $sqlTemp = $sqlTemp . ") VALUES ('n', ?, ?";
        if($variableCount == 2) {
            $sqlTemp = $sqlTemp . ")";
            $stmt3 = $pdo->prepare($sqlTemp);
            $stmt3->execute([$racerTeam, $teamAverage]);
        } elseif($variableCount == 3) {
            $sqlTemp = $sqlTemp . ", ?)";
            $stmt3 = $pdo->prepare($sqlTemp);
            if($igtGather == 'y') {
                $stmt3->execute([$racerTeam, $teamAverage, $teamIGTAverage]);
            } elseif($crGather == 'y') {
                $stmt3->execute([$racerTeam, $teamAverage, $teamCRAverage]);
            }
        } elseif($variableCount == 4) {
            $sqlTemp = $sqlTemp . ', ?, ?)';
            $stmt3 = $pdo->prepare($sqlTemp);
            $stmt3->execute([$racerTeam, $teamAverage, $teamIGTAverage, $teamCRAverage]);
        }
    }
}
