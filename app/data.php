<?php

require_once ('../includes/bootstrap.php');
$httpStatus = 200;
ob_start();
ob_clean();
header_remove();
header('Content-Type: application/json; charset=utf-8');
http_response_code($httpStatus);
$jsonArray = array();
if (isset($_GET['raceID'])) {
    $raceID = $_GET['raceID'];
    $jsonArray['id'] = $raceID;
    $stmt = $pdo->prepare("SELECT * FROM races WHERE id = :id");
    $stmt->bindParam(':id', $raceID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    $raceSlug = $row['raceSlug'];
    $jsonArray['slug'] = $raceSlug;
    $raceMode = $row['raceMode'];
    $jsonArray['mode'] = $raceMode;
    $raceSeed = $row['raceSeed'];
    $jsonArray['seed'] = $raceSeed;
    $raceHash = $row['raceHash'];
    $jsonArray['hash'] = $raceHash;
    $raceIsTeam = $row['raceIsTeam'];
    $jsonArray['team_race'] = $raceIsTeam;
    $raceIsSpoiler = $row['raceIsSpoiler'];
    $jsonArray['spoiler_race'] = $raceIsSpoiler;
    if ($raceIsSpoiler == 'y') {
        $SpoierLink = $row['raceSpoilerLink'];
        $jsonArray['spoiler'] = $spoilerLink;
    }
    if ($raceIsTeam == 'n') { //Sort the list of racers by time with forfeits on the bottom and form the rest of the JSON
        $stmt = $pdo->prepare("SELECT * FROM results WHERE raceSlug = :slug AND racerForfeit = 'n' ORDER BY racerRealTime");
        $stmt->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $racerID = $row['racerRacetimeID'];
            $stmt2 = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = :racerID");
            $stmt2->bindParam(':racerID', $racerID, PDO::PARAM_STR);
            $stmt2->execute();
            $racerName = $stmt2->fetchColumn();
            if (! $racerName) {
                echo json_encode($jsonArray);
                $dieString = 'Racer ' . $racerID . ' not found';
                die ($dieString);
            }
            $racerTime = $row['racerRealTime'];
            $racerCR = $row['racerCheckCount'];
            $racerComment = $row['racerComment'];
            $racerVOD = $row['racerVODLink'];
            $jsonArray['participants'][] = [ 'racer_name' => $racerName , 'time' => $racerTime , 'collection_rate' => $racerCR , 'vod_link' => $racerVOD , 'comments' => $racerComment ];
        }
        $stmt = $pdo->prepare("SELECT * FROM results WHERE raceSlug = :slug AND racerForfeit = 'y'");
        $stmt->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $racerID = $row['racerRacetimeID'];
            $stmt2 = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = :racerID");
            $stmt2->bindParam(':racerID', $racerID, PDO::PARAM_STR);
            $stmt2->execute();
            $racerName = $stmt2->fetchColumn();
            if (! $racerName) {
                echo json_encode($jsonArray);
                $dieString = 'Racer ' . $racerID . ' not found';
                die ($dieString);
            }
            $racerTime = 'Forfeit';
            $racerComment = $row['racerComment'];
            $racerVOD = $row['racerVODLink'];
            $jsonArray['participants'][] = [ 'racer_name' => $racerName , 'time' => $racerTime , 'vod_link' => $racerVOD , 'comments' => $racerComment ];
        }
    } else { //For team races, there's a few extra steps
        $stmt = $pdo->prepare("TRUNCATE TABLE results_temp"); //Clear out the temp table
        $stmt->execute();
        $stmt = $pdo->prepare("SELECT DISTINCT(racerTeam) FROM results WHERE raceSlug = :slug"); //Find all teams for this race
        $stmt->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $team = $row['racerTeam'];
            $stmt2 = $pdo->prepare("SELECT racerRealTime FROM results WHERE raceSlug = :slug AND racerTeam = :team AND racerForfeit = 'y'"); //Determine if there are any forfeits for this team
            $stmt2->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
            $stmt2->bindParam(':team', $team, PDO::PARAM_STR);
            $stmt2->execute();
            $rslt = $stmt2->fetchAll(PDO::FETCH_COLUMN);
            if (count($rslt) > 0) { //If there's a forfeit on the team, input the team as a forfeit in the temp table
                $stmt2 = $pdo->prepare("INSERT INTO results_temp (teamName, teamForfeit) VALUES (:team, 'y')");
                $stmt2->bindParam(':team', $team, PDO::PARAM_STR);
                $stmt2->execute();
            } else { //Get the average time for everyone on the team and insert it into the temp table
                $stmt2 = $pdo->prepare("SELECT AVG(racerRealTime) FROM results WHERE raceSlug = :slug AND racerTeam = :team AND racerForfeit = 'n'");
                $stmt2->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
                $stmt2->bindParam(':team', $team, PDO::PARAM_STR);
                $stmt2->execute();
                $avgTime = round($stmt2->fetchColumn());
                $stmt2 = $pdo->prepare("INSERT INTO results_temp (teamName, averageTime, teamForfeit) VALUES (:team, :averagetime, 'n')");
                $stmt2->bindParam(':team', $team, PDO::PARAM_STR);
                $stmt2->bindParam(':averagetime', $avgTime, PDO::PARAM_INT);
                $stmt2->execute();
                unset ($avgTime);
            }
            unset ($team);
        }
        //Now that the temp table is complete, let's gather individual results and form the JSON
        $rowCount = 0;
        $stmt = $pdo->prepare("SELECT teamName, averageTime FROM results_temp WHERE teamForfeit = 'n' ORDER BY averageTime");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $team = $row['teamName'];
            $avgTime = $row['averageTime'];
            $jsonArray['teams'][$rowCount] = [ 'team_name' => $team , 'average_time' => $avgTime, 'forfeit' => 'n' ];
            $stmt2 = $pdo->prepare("SELECT racerRacetimeID, racerRealTime, racerComment, racerCheckCount, racerVODLink FROM results WHERE raceSlug = :slug AND racerTeam = :team ORDER BY racerRealTime");
            $stmt2->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
            $stmt2->bindParam(':team', $team, PDO::PARAM_STR);
            $stmt2->execute();
            while ($row2 = $stmt2->fetch()) {
                $racerID = $row2['racerRacetimeID'];
                $stmt3 = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = :racerID");
                $stmt3->bindParam(':racerID', $racerID, PDO::PARAM_STR);
                $stmt3->execute();
                $racerName = $stmt3->fetchColumn();
                if (! $racerName) {
                    echo json_encode($jsonArray);
                    $dieString = 'Racer ' . $racerID . ' not found';
                    die ($dieString);
                }
                $racerTime = $row2['racerRealTime'];
                $racerCR = $row2['racerCheckCount'];
                $racerComment = $row2['racerComment'];
                $racerVOD = $row2['racerVODLink'];
                $jsonArray['teams'][$rowCount]['members'][] = [ 'racer_name' => $racerName , 'time' => $racerTime , 'collection_rate' => $racerCR , 'vod_link' => $racerVOD , 'comments' => $racerComment ];
            }
            $rowCount++;
        }
        $stmt = $pdo->prepare("SELECT teamName, averageTime FROM results_temp WHERE teamForfeit = 'y'");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $team = $row['teamName'];
            $jsonArray['teams'][$rowCount] = [ 'team_name' => $team, 'forfeit' => 'y' ];
            $stmt2 = $pdo->prepare("SELECT racerRacetimeID, racerComment, racerVODLink FROM results WHERE raceSlug = :slug AND racerTeam = :team");
            $stmt2->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
            $stmt2->bindParam(':team', $team, PDO::PARAM_STR);
            $stmt2->execute();
            while ($row2 = $stmt2->fetch()) {
                $racerID = $row2['racerRacetimeID'];
                $stmt3 = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = :racerID");
                $stmt3->bindParam(':racerID', $racerID, PDO::PARAM_STR);
                $stmt3->execute();
                $racerName = $stmt3->fetchColumn();
                if (! $racerName) {
                    echo json_encode($jsonArray);
                    $dieString = 'Racer ' . $racerID . ' not found';
                    die ($dieString);
                }
                $racerTime = 'Forfeit';
                $racerComment = $row2['racerComment'];
                $racerVOD = $row2['racerVODLink'];
                $jsonArray['teams'][$rowCount]['members'][] = [ 'racer_name' => $racerName , 'time' => $racerTime , 'vod_link' => $racerVOD , 'comments' => $racerComment ];
            }
            $rowCount++;
        }
    }
    echo json_encode ($jsonArray);
    exit();
} else {
    die ('No race found');
}
