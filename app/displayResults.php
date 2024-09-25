<?php
echo '        <div class="asyncTopRow">Results for ';
if($raceFromRacetime == 'y') {
    echo '<a target="_blank" href="https://racetime.gg/alttpr/' . $raceSlug . '">' . $raceSlug . '</a></div>';
} else {
    echo $raceSlug;
}
echo '        <div class="asyncMiddle">Mode: ' . $raceMode . '<br />' . PHP_EOL;
if($raceIsTeam == 'y') {
    $raceDescription = 'CO-OP/TEAM - ' . $raceDescription;
}
if($raceIsSpoiler == 'y') {
    if($raceDescription == '') {
        $raceDescription = '<a target="_blank" href="' . $raceSpoilerLink . '">Download Spoiler Log</a>';
    } else {
        $raceDescription = $raceDescription . ' - <a target="_blank" href="' . $raceSpoilerLink . '">Download Spoiler Log</a>';
    }
}
echo '        ' . $raceDescription . '<br />' . PHP_EOL;
echo '        Seed Link - <a target="_blank" href="' . $raceSeed . '">' . $raceSeed . '</a> - Hash: ' . hashToImages($raceHash) . '<br />' . PHP_EOL;
if($raceIsTeam == 'n') {
    $stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = ?');
    $stmt->execute([$raceSlug]);
    $racerCount = $stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = ? AND racerForfeit = "n"');
    $stmt->execute([$raceSlug]);
    $finisherCount = $stmt->fetchColumn();
    echo '        Participants: ';
} else {
    $stmt = $pdo->prepare('SELECT count(distinct(racerTeam)) FROM results WHERE raceSlug = ?');
    $stmt->execute([$raceSlug]);
    $racerCount = $stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT count(distinct(racerTeam)) FROM results WHERE raceSlug = ? AND racerForfeit = "y"');
    $stmt->execute([$raceSlug]);
    $forfeitCount = $stmt->fetchColumn();
    $finisherCount = $racerCount - $forfeitCount;
    echo '        Teams: ';
}
$stmt = $pdo->prepare("SELECT count(1) FROM results WHERE raceSlug = ? AND racerInGameTime IS NOT NULL");
$stmt->execute([$raceSlug]);
$igtCount = $stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT count(1) FROM results WHERE raceSlug = ? AND racerCheckCount IS NOT NULL");
$stmt->execute([$raceSlug]);
$checkCount = $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = ? AND racerComment IS NOT NULL');
$stmt->execute([$raceSlug]);
$commentCount = $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = ? AND racerVODLink IS NOT NULL');
$stmt->execute([$raceSlug]);
$vodCount = $stmt->fetchColumn();
echo $racerCount . ' - Finishers: ' . $finisherCount . '</br />'. PHP_EOL;
$stmt = $pdo->prepare("SELECT AVG(racerRealTime) FROM results WHERE raceSlug = ? AND racerForfeit = 'n'");
$stmt->execute([$raceSlug]);
$raceAverage = round($stmt->fetchColumn());
echo '        Average Finish: ' . gmdate('G:i:s', $raceAverage);
if($igtCount > 0) {
    $stmt = $pdo->prepare("SELECT AVG(racerInGameTime) FROM results WHERE raceSlug = ? AND racerInGameTime IS NOT NULL and racerForfeit = 'n'");
    $stmt->execute([$raceSlug]);
    $igtAverage = round($stmt->fetchColumn());
    echo ' - IGT Average: ' . gmdate('G:i:s', $igtAverage);
}
if($checkCount > 0) {
    $stmt = $pdo->prepare("SELECT AVG(racerCheckCount) FROM results WHERE raceSlug = ? AND racerCheckCount IS NOT NULL AND racerForfeit = 'n'");
    $stmt->execute([$raceSlug]);
    $crAverage = round($stmt->fetchColumn());
    echo ' - Average Collection Rate: ' . $crAverage;
}
echo '</div><br />' . PHP_EOL;
echo '        <hr />' . PHP_EOL;
echo '        <table class="raceResults">' . PHP_EOL;
if($raceIsTeam == 'n') {
    $sql = 'SELECT racerRacetimeID, racerRealTime, racerFromRacetime';
    echo '            <thead>' . PHP_EOL;
    echo '                <tr><th>Place</th><th>Name</th><th>Finish Time</th>';
    if($igtCount > 0) {
        echo '<th>In Game Time</th>';
        $sql = $sql . ', racerInGameTime';
    }
    if($commentCount > 0) {
        $sql = $sql . ', racerComment';
    }
    if($checkCount > 0) {
        echo '<th>Collection Rate</th>';
        $sql = $sql . ', racerCheckCount';
    }
    if($vodCount > 0) {
        echo '<th>Link to VOD</th>';
        $sql = $sql . ', racerVODLink';
    }
    echo '</td>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    $sql = $sql . " FROM results WHERE raceSlug = ? AND racerForfeit = 'n' ORDER BY racerRealTime";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$raceSlug]);
    $rowCount = 0;
    while($row = $stmt->fetch()) {
        $stmt2 = $pdo->prepare('SELECT racetimeName FROM racerinfo WHERE racetimeID = ?');
        $stmt2->execute([$row['racerRacetimeID']]);
        $racerName = $stmt2->fetchColumn();
        $rowCount++;
        if($rowCount % 2 == 0) {
            if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                echo '                <tr class="even new">';
            } else {
                echo '                <tr class="even">';
            }
        } else {
            if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                echo '                <tr class="odd new">';
            } else {
                echo '                <tr class="odd">';
            }
        }
        echo '<td class="place' . $rowCount . '">' . $rowCount . '</td><td>' . $racerName;
        if($commentCount > 0) {
            if($row['racerComment'] != null) {
                echo ' <span class="comment" title = "' . $row['racerComment'] . '">[Comment]</span>';
            }
        }
        echo '</td><td>' . gmdate('G:i:s', $row['racerRealTime']) . '</td>';
        if($igtCount > 0) {
            if($row['racerInGameTime'] != null) {
                echo '<td>' . gmdate('G:i:s', $row['racerInGameTime']) . '</td>';
            } else {
                echo '<td>N/A</td>';
            }
        }
        if($checkCount > 0) {
            if($row['racerCheckCount'] != null) {
                echo '<td>' . $row['racerCheckCount'] . '</td>';
            } else {
                echo '<td>N/A</td>';
            }
        }
        if($vodCount > 0) {
            if($row['racerVODLink'] != null) {
                echo '<td><a target="_blank" href="' . $row['racerVODLink'] . '">Link to VOD</a></td>';
            } else {
                echo '<td>N/A</td>';
            }
        }
        echo '</tr>' . PHP_EOL;
    }
    $sql = "SELECT racerRacetimeID, racerComment FROM results WHERE raceSlug = ? AND racerForfeit = 'y'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$raceSlug]);
    while($row = $stmt->fetch()) {
        $stmt2 = $pdo->prepare('SELECT racetimeName FROM racerinfo WHERE racetimeID = ?');
        $stmt2->execute([$row['racerRacetimeID']]);
        $racerName = $stmt2->fetchColumn();
        $rowCount++;
        if($rowCount % 2 == 0) {
            if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                echo '                <tr class="even new">';
            } else {
                echo '                <tr class="even">';
            }
        } else {
            if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                echo '                <tr class="odd new">';
            } else {
                echo '                <tr class="odd">';
            }
        }
        echo '<td class="ff">FF</td><td>' . $racerName;
        if($commentCount > 0) {
            if($row['racerComment'] != null) {
                echo ' <span class="comment" title = "' . $row['racerComment'] . '">[Comment]</span>';
            }
        }
        echo '</td><td>Forfeit</td>';
        if($igtCount > 0) {
            echo '<td>Forfeit</td>';
        }
        if($checkCount > 0) {
            echo '<td>FF</td>';
        }
        if($vodCount > 0) {
            echo '<td>Forfeit</td>';
        }
        echo '</tr>' . PHP_EOL;
    }
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
} else {
    require ('../app/populateTempTable.php');
    echo '            <thead>' . PHP_EOL;
    echo '                <tr><th>Place</th><th>Name</th><th>Real Time</th>';
    $sql = 'SELECT racerRacetimeID, racerRealTime, racerFromRacetime';
    if($igtCount > 0) {
        echo '<th>In Game Time</th>';
        $sql = $sql . ', racerInGameTime';
    }
    if($commentCount > 0) {
        $sql = $sql . ', racerComment';
    }
    if($checkCount > 0) {
        echo '<th>Collection Rate</th>';
        $sql = $sql . ', racerCheckCount';
    }
    if($vodCount > 0) {
        echo '<th>Link to VOD</th>';
        $sql = $sql . ', racerVODLink';
    }
    echo '</td>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    $sql = $sql . " FROM results WHERE raceSlug = ? AND racerTeam = ? ORDER BY racerRealTime";
    $rowCount = 0;
    $sql2 = $pdo->prepare("SELECT teamName, averageTime, averageIGT, averageCR FROM results_temp WHERE teamForfeit = 'n' ORDER BY averageTime");
    $sql2->execute();
    while($teamRow = $sql2->fetch()) {
        $rowCount++;
        $teamName = $teamRow['teamName'];
        $teamAverageTime = round($teamRow['averageTime'], 0);
        if($igtCount > 0) {
            $teamAverageIGT = round($teamRow['averageIGT'], 0);
        }
        if($checkCount > 0) {
            $teamAverageCR = round($teamRow['averageCR'], 0);
        }
        if($rowCount % 2 == 0) {
            echo '                <tr class="team even">';
        } else {
            echo '                <tr class="team odd">';
        }
        echo '<td class="place' . $rowCount . '">' . $rowCount . '</td><td>' . $teamName . '</td><td>' . gmdate('G:i:s', $teamAverageTime) . '</td>';
        if($igtCount > 0) {
            echo '<td>' . gmdate('G:i:s', $teamAverageIGT) . '</td>';
        }
        if($checkCount > 0) {
            echo '<td>' . $teamAverageCR . '</td>';
        }
        if($vodCount > 0) {
            echo '<td></td>';
        }
        echo '</tr>' . PHP_EOL;
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$raceSlug, $teamName]);
        while($row = $stmt->fetch()) {
            $racerSQL = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = ?");
            $racerSQL->execute([$row['racerRacetimeID']]);
            $racerName = $racerSQL->fetchColumn();
            if($rowCount % 2 == 0) {
                if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                    echo '                <tr class="even new">';
                } else {
                    echo '                <tr class="even">';
                }
            } else {
                if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                    echo '                <tr class="odd new">';
                } else {
                    echo '                <tr class="odd">';
                }
            }
            echo '<td class="place' . $rowCount . '"></td><td class="teamRacerName">' . $racerName;
            if($commentCount > 0) {
                if($row['racerComment'] != null) {
                    echo ' <span class="comment" title = "' . $row['racerComment'] . '">[Comment]</span>';
                }
            }
            echo '</td><td class="teamRacerData">' . gmdate('G:i:s', $row['racerRealTime']) . '</td>';
            if($igtCount > 0) {
                if($row['racerInGameTime'] != null) {
                    echo '<td class="teamRacerData">' . gmdate('G:i:s', $row['racerInGameTime']) . '</td>';
                } else {
                    echo '<td class="teamRacerData">N/A</td>';
                }
            }
            if($checkCount > 0) {
                if($row['racerCheckCount'] != null) {
                    echo '<td class="teamRacerData">' . $row['racerCheckCount'] . '</td>';
                } else {
                    echo '<td class="teamRacerData">N/A</td>';
                }
            }
            if($vodCount > 0) {
                if($row['racerVODLink'] != null) {
                    echo '<td><a target=_blank" href="' . $row['racerVODLink'] . '">Link to VOD</a></td>';
                } else {
                    echo '<td>N/A</td>';
                }
            }
            echo '</tr>' . PHP_EOL;
        }
    }
    $sql2 = $pdo->prepare("SELECT teamName FROM results_temp WHERE teamForfeit = 'y' ORDER BY teamName");
    $sql2->execute();
    while($teamRow = $sql2->fetch()) {
        $rowCount++;
        $teamName = $teamRow['teamName'];
        if($rowCount % 2 == 0) {
            echo '                <tr class="team even">';
        } else {
            echo '                <tr class="team odd">';
        }
        echo '<td class="ff">FF</td><td>' . $teamName . '</td><td>Forfeit</td>';
        if($igtCount > 0) {
            echo '<td>Forfeit</td>';
        }
        if($checkCount > 0) {
            echo '<td>FF</td>';
        }
        if($vodCount > 0) {
            echo '<td>Forfeit</td>';
        }
        echo '</tr>' . PHP_EOL;
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$raceSlug, $teamName]);
        while($row = $stmt->fetch()) {
            $racerSQL = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = ?");
            $racerSQL->execute([$row['racerRacetimeID']]);
            $racerName = $racerSQL->fetchColumn();
            if($rowCount % 2 == 0) {
                if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                    echo '                <tr class="even new">';
                } else {
                    echo '                <tr class="even">';
                }
            } else {
                if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                    echo '                <tr class="odd new">';
                } else {
                    echo '                <tr class="odd">';
                }
            }
            echo '<td class="ff"></td><td class="teamRacerName">' . $racerName;
            if($commentCount > 0) {
                if($row['racerComment'] != null) {
                    echo ' <span class="comment" title = "' . $row['racerComment'] . '">[Comment]</span>';
                }
            }
            echo '</td><td class="teamRacerData">Forfeit</td>';
            if($igtCount > 0) {
                echo '<td class="teamRacerData">Forfeit</td>';
            }
            if($checkCount > 0) {
                echo '<td class="teamRacerData">FF</td>';
            }
            if($vodCount > 0) {
                echo '<td>Forfeit</td>';
            }
            echo PHP_EOL;
        }
    }
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
}
