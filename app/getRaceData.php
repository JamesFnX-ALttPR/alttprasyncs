<?php

require('../includes/functions.php');
require('../config/settings.php');
// Create DB connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $options);

$slugList = array('');
$url = 'https://racetime.gg/alttpr/races/data';
$urlData = curlData($url);
$urlJson = json_decode($urlData, true);
$numPages = $urlJson['num_pages'];
unset($url); unset($urlData); unset($urlJson);
for($i=1;$i<=5;$i++) {
    $url = 'https://racetime.gg/alttpr/races/data?page=' . $i;
    $urlData = curlData($url);
    $urlJson = json_decode($urlData, true);
    $numRaces = count($urlJson['races']);
    for($j=0;$j<$numRaces;$j++) {
        $raceName = substr($urlJson['races'][$j]['name'], 7);
        array_unshift($slugList, $raceName);
    }
    unset($url); unset($urlData); unset($urlJson);
}
array_pop($slugList);
$slugCount = count($slugList);
for($i=0;$i<$slugCount;$i++) {
    $url = 'https://racetime.gg/alttpr/' . $slugList[$i] . '/data';
    $urlData = curlData($url);
    $urlJson = json_decode($urlData, true);
    $raceInfoBot = $urlJson['info_bot'];
    $raceInfoUser = $urlJson['info_user'];
    if($urlJson['team_race'] == false) {
        $raceIsTeam = 'n';
    } else {
        $raceIsTeam = 'y';
    }
    $raceStart = convertTimestamp($urlJson['opened_at']);
    if(alttprValidateInfoBot($raceInfoBot) == 'y') {
        $raceInfoBotArray = alttprParseInfoBot($raceInfoBot);
        $raceMode = $raceInfoBotArray[0];
        $raceSeed = $raceInfoBotArray[1];
        $raceHash = $raceInfoBotArray[2];
    } else {
        $raceMode = '';
        $raceSeed = '';
        $raceHash = '';
    }
    if($raceSeed != '') {
        $stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = ?");
        $stmt->execute([$slugList[$i]]);
        $raceExists = $stmt->fetchColumn();
        if(! $raceExists) {
            if(substr($raceMode, 0, 7) == 'spoiler' || substr($raceMode, 0, 7) == 'Spoiler') {
                $chatLog = 'https://racetime.gg/alttpr/' . $slugList[$i] . '.txt';
                preg_match('/https:\/\/.+(s|S)poiler.+\.(txt|json)/', curlData($chatLog), $matches);
                $spoilerLink = $matches[0];
                $raceIsSpoiler = 'y';
            } else {
                $spoilerLink = '';
                $raceIsSpoiler = 'n';
            }
            $sql = "INSERT INTO races (raceSlug, raceStart, raceMode, raceSeed, raceHash, raceDescription, raceIsTeam, raceFromRacetime, raceIsSpoiler, raceSpoilerLink) VALUES (?, ?, ?, ?, ?, ?, ?, 'y', ?, ?)";
            $pdo->prepare($sql)->execute([$slugList[$i], $raceStart, $raceMode, $raceSeed, $raceHash, $raceInfoUser, $raceIsTeam, $raceIsSpoiler, $spoilerLink]);
            $racePlayerCount = count($urlJson['entrants']);
            for($j=0;$j<$racePlayerCount;$j++) {
                $playerRacetimeID = $urlJson['entrants'][$j]['user']['id'];
                $playerName = $urlJson['entrants'][$j]['user']['name'];
                $playerDiscriminator = $urlJson['entrants'][$j]['user']['discriminator'];
                if($urlJson['team_race'] == true) {
                    $playerTeam = $urlJson['entrants'][$j]['team']['name'];
                } else {
                    $playerTeam = '';
                }
                $playerRealTime = $urlJson['entrants'][$j]['finish_time'];
                if($playerRealTime == '') {
                    $playerRealTime = 20000;
                    $playerIsForfeit = 'y';
                } else {
                    $playerRealTime = convertFinishTime($playerRealTime);
                    $playerIsForfeit = 'n';
                }
                $playerComment = $urlJson['entrants'][$j]['comment'];
                if($playerComment == null) {
                    $playerComment = '';
                }
                $stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = ? AND racerRacetimeID = ?");
                $stmt->execute([$slugList[$i], $playerRacetimeID]);
                $resultExists = $stmt->fetchColumn();
                if(! $resultExists) {
                    $sql = "INSERT INTO results (raceSlug, racerRacetimeID, racerTeam, racerRealTime, racerComment, racerForfeit, racerFromRacetime) VALUES (?, ?, ?, ?, ?, ?, 'y')";
                    $pdo->prepare($sql)->execute([$slugList[$i], $playerRacetimeID, $playerTeam, $playerRealTime, $playerComment, $playerIsForfeit]);
                    $stmt = $pdo->prepare("SELECT id FROM racerinfo WHERE racetimeID = ?");
                    $stmt->execute([$playerRacetimeID]);
                    $racetimeIDExists = $stmt->fetchColumn();
                    if(! $racetimeIDExists) {
                        $sql = "INSERT INTO racerinfo (racetimeID, rtgg_name, rtgg_discriminator) VALUES (?, ?, ?)";
                        $pdo->prepare($sql)->execute([$playerRacetimeID, $playerName, $playerDiscriminator]);
                    } else {
                        $sql = "UPDATE racerinfo SET rtgg_name = ?, rtgg_discriminator = ? WHERE racetimeID = ?";
                        $pdo->prepare($sql)->execute([$playerName, $playerDiscriminator, $playerRacetimeID]);
                    }
                }
            }
        }
    }
}
