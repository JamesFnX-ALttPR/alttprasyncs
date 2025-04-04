<?php // Generate team information in temp table - getting $temp_table_hash from page

// Create table for results
require_once ('../includes/functions.php');
require_once ('../config/settings.php');
$sql = "CREATE TABLE IF NOT EXISTS temp_" . $temp_table_hash . " (teamName varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, averageTime mediumint DEFAULT NULL, averageIGT mediumint DEFAULT NULL, averageCR smallint DEFAULT NULL, teamForfeit varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Determine if this is a Duality race (where we'll sort by last finish instead of average finish)
$duality_regex = '/.*\/duality$/';
if (preg_match($duality_regex, $race_mode)) {
    $duality = 'y';
} else {
    $duality = 'n';
}
// Get list of teams in a race
$stmt = $pdo->prepare("SELECT DISTINCT racerTeam FROM results WHERE raceSlug = ?");
$stmt->execute([$race_slug]);

// Determine if anyone on the team forfeitted
while($row = $stmt->fetch()) {
    $racerTeam = $row['racerTeam'];
    $teamForfeit = 'n';
    $stmt2 = $pdo->prepare("SELECT racerForfeit FROM results WHERE raceSlug = ? AND racerTeam = ?");
    $stmt2->execute([$race_slug, $racerTeam]);
    while($row2 = $stmt2->fetch()) {
        if($row2['racerForfeit'] == 'y') {
            $teamForfeit = 'y';
        }
    }
    if($teamForfeit == 'y') { // Mark team as forfeitted if any player did
        $sql3 = "INSERT INTO temp_" . $temp_table_hash . " (teamName, teamForfeit) VALUES (?, 'y')";
        $stmt3 = $pdo->prepare($sql3);
        $stmt3->execute([$racerTeam]);
    } else { // Get average times or last times and collection rate if no player forfeitted
        if ($duality == 'n') {
            $stmt3 = $pdo->prepare("SELECT AVG(racerRealTime) FROM results WHERE raceSlug = ? AND racerTeam = ?");
            $stmt3->execute([$race_slug, $racerTeam]);
            $teamAverage = $stmt3->fetchColumn();
            $sqlTemp = "INSERT INTO temp_" . $temp_table_hash . " (teamForfeit, teamName, averageTime";
            $variableCount = 2;
            $crGather = 'n';
            if($check_count > 0) {
                $stmt3 = $pdo->prepare("SELECT AVG(racerCheckCount) FROM results WHERE raceSlug = ? AND racerTeam = ? AND racerCheckCount IS NOT NULL");
                $stmt3->execute([$race_slug, $racerTeam]);
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
            }
            else {
                $sqlTemp = $sqlTemp . ", ?)";
                $stmt3 = $pdo->prepare($sqlTemp);
                $stmt3->execute([$racerTeam, $teamAverage, $teamCRAverage]);
            }
        } else {
            $stmt3 = $pdo->prepare("SELECT racerRealTime FROM results WHERE raceSlug = ? AND racerTeam = ? ORDER BY racerRealTime DESC LIMIT 1");
            $stmt3->execute([$race_slug, $racerTeam]);
            $teamAverage = $stmt3->fetchColumn();
            $sqlTemp = "INSERT INTO temp_" . $temp_table_hash . " (teamForfeit, teamName, averageTime";
            $variableCount = 2;
            $crGather = 'n';
            if($check_count > 0) {
                $stmt3 = $pdo->prepare("SELECT AVG(racerCheckCount) FROM results WHERE raceSlug = ? AND racerTeam = ? AND racerCheckCount IS NOT NULL");
                $stmt3->execute([$race_slug, $racerTeam]);
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
            }
            else {
                $sqlTemp = $sqlTemp . ", ?)";
                $stmt3 = $pdo->prepare($sqlTemp);
                $stmt3->execute([$racerTeam, $teamAverage, $teamCRAverage]);
            }
        }
    }
}
