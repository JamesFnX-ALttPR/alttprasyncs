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

// Check for new races in the alttpr category
$slug_list = array('');
$url = 'https://racetime.gg/alttpr/races/data';
$url_data = curlData($url);
$url_json = json_decode($url_data, true);
$numPages = $url_json['num_pages'];
unset($url); unset($url_data); unset($url_json);
for($i=1;$i<=5;$i++) {
    $url = 'https://racetime.gg/alttpr/races/data?page=' . $i;
    $url_data = curlData($url);
    $url_json = json_decode($url_data, true);
    $num_races = count($url_json['races']);
    for($j=0;$j<$num_races;$j++) {
        $race_name = substr($url_json['races'][$j]['name'], 7);
        array_unshift($slug_list, $race_name);
    }
    unset($url); unset($url_data); unset($url_json);
}
array_pop($slug_list);
$slug_count = count($slug_list);
for($i=0;$i<$slug_count;$i++) {
    $race_slug = 'alttpr/' . $slug_list[$i];
    $url = 'https://racetime.gg/' . $race_slug . '/data';
    $url_data = curlData($url);
    $url_json = json_decode($url_data, true);
    $info_bot = $url_json['info_bot'];
    $info_user = $url_json['info_user'];
    if($url_json['team_race'] == false) {
        $team_flag = 'n';
    } else {
        $team_flag = 'y';
    }
    $race_start = convertTimestamp($url_json['opened_at']);
    if(alttprValidateInfoBot($info_bot)) {
        $info_bot_array = alttprParseInfoBot($info_bot);
        $race_mode = $info_bot_array[0];
        $race_seed = $info_bot_array[1];
        $race_hash = $info_bot_array[2];
    } else {
        $race_mode = '';
        $race_seed = '';
        $race_hash = '';
    }
    if($race_seed != '') {
        $stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = ?");
        $stmt->execute([$race_slug]);
        $race_exists = $stmt->fetchColumn();
        if(! $race_exists) {
            if(substr($race_mode, 0, 7) == 'spoiler' || substr($race_mode, 0, 7) == 'Spoiler') {
                $chat_log = 'https://racetime.gg/' . $race_slug . '.txt';
                preg_match('/https:\/\/.+(s|S)poiler.+\.(txt|json)/', curlData($chat_log), $matches);
                $spoiler_link = $matches[0];
                $spoiler_flag = 'y';
            } else {
                $spoiler_link = '';
                $spoiler_flag = 'n';
            }
            $sql = "INSERT INTO races (raceSlug, raceStart, raceMode, raceSeed, raceHash, raceDescription, raceIsTeam, raceFromRacetime, raceIsSpoiler, raceSpoilerLink) VALUES (?, ?, ?, ?, ?, ?, ?, 'y', ?, ?)";
            $pdo->prepare($sql)->execute([$race_slug, $race_start, $race_mode, $race_seed, $race_hash, $info_user, $team_flag, $spoiler_flag, $spoiler_link]);
            $racePlayerCount = count($url_json['entrants']);
            for($j=0;$j<$racePlayerCount;$j++) {
                $playerRacetimeID = $url_json['entrants'][$j]['user']['id'];
                $playerName = $url_json['entrants'][$j]['user']['name'];
                $playerDiscriminator = $url_json['entrants'][$j]['user']['discriminator'];
                if($url_json['team_race'] == true) {
                    $playerTeam = $url_json['entrants'][$j]['team']['name'];
                } else {
                    $playerTeam = '';
                }
                $playerRealTime = $url_json['entrants'][$j]['finish_time'];
                if($playerRealTime == '') {
                    $playerRealTime = 20000;
                    $playerIsForfeit = 'y';
                } else {
                    $playerRealTime = convertFinishTime($playerRealTime);
                    $playerIsForfeit = 'n';
                }
                $playerComment = $url_json['entrants'][$j]['comment'];
                if($playerComment == null) {
                    $playerComment = '';
                }
                $stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = ? AND racerRacetimeID = ?");
                $stmt->execute([$race_slug, $playerRacetimeID]);
                $resultExists = $stmt->fetchColumn();
                if(! $resultExists) {
                    $sql = "INSERT INTO results (raceSlug, racerRacetimeID, racerTeam, racerRealTime, racerComment, racerForfeit, racerFromRacetime) VALUES (?, ?, ?, ?, ?, ?, 'y')";
                    $pdo->prepare($sql)->execute([$race_slug, $playerRacetimeID, $playerTeam, $playerRealTime, $playerComment, $playerIsForfeit]);
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

// Check for new races in the alttpr-ladder category
$slug_list = array('');
$url = 'https://racetime.gg/alttpr-ladder/races/data';
$url_data = curlData($url);
$url_json = json_decode($url_data, true);
$numPages = $url_json['num_pages'];
unset($url); unset($url_data); unset($url_json);
for($i=1;$i<=5;$i++) {
    $url = 'https://racetime.gg/alttpr-ladder/races/data?page=' . $i;
    $url_data = curlData($url);
    $url_json = json_decode($url_data, true);
    $num_races = count($url_json['races']);
    for($j=0;$j<$num_races;$j++) {
        $race_name = substr($url_json['races'][$j]['name'], 7);
        array_unshift($slug_list, $race_name);
    }
    unset($url); unset($url_data); unset($url_json);
}
array_pop($slug_list);
$slug_count = count($slug_list);
for($i=0;$i<$slug_count;$i++) {
    $race_slug = 'alttpr-ladder/' . $slug_list[$i];
    $url = 'https://racetime.gg/' . $race_slug . '/data';
    $url_data = curlData($url);
    $url_json = json_decode($url_data, true);
    $info_bot = $url_json['info_bot'];
    $info_user = $url_json['info_user'];
    if($url_json['team_race'] == false) {
        $team_flag = 'n';
    } else {
        $team_flag = 'y';
    }
    $race_start = convertTimestamp($url_json['opened_at']);
    if(alttprValidateInfoBot($info_bot)) {
        $info_bot_array = alttprParseInfoBot($info_bot);
        $race_mode = $info_bot_array[0];
        $race_seed = $info_bot_array[1];
        $race_hash = $info_bot_array[2];
    } else {
        $race_mode = '';
        $race_seed = '';
        $race_hash = '';
    }
    if($race_seed != '') {
        $stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = ?");
        $stmt->execute([$race_slug]);
        $race_exists = $stmt->fetchColumn();
        if(! $race_exists) {
            if(substr($race_mode, 0, 7) == 'spoiler' || substr($race_mode, 0, 7) == 'Spoiler') {
                $chat_log = 'https://racetime.gg/' . $race_slug . '.txt';
                preg_match('/https:\/\/.+(s|S)poiler.+\.(txt|json)/', curlData($chat_log), $matches);
                $spoiler_link = $matches[0];
                $spoiler_flag = 'y';
            } else {
                $spoiler_link = '';
                $spoiler_flag = 'n';
            }
            $sql = "INSERT INTO races (raceSlug, raceStart, raceMode, raceSeed, raceHash, raceDescription, raceIsTeam, raceFromRacetime, raceIsSpoiler, raceSpoilerLink) VALUES (?, ?, ?, ?, ?, ?, ?, 'y', ?, ?)";
            $pdo->prepare($sql)->execute([$race_slug, $race_start, $race_mode, $race_seed, $race_hash, $info_user, $team_flag, $spoiler_flag, $spoiler_link]);
            $racePlayerCount = count($url_json['entrants']);
            for($j=0;$j<$racePlayerCount;$j++) {
                $playerRacetimeID = $url_json['entrants'][$j]['user']['id'];
                $playerName = $url_json['entrants'][$j]['user']['name'];
                $playerDiscriminator = $url_json['entrants'][$j]['user']['discriminator'];
                if($url_json['team_race'] == true) {
                    $playerTeam = $url_json['entrants'][$j]['team']['name'];
                } else {
                    $playerTeam = '';
                }
                $playerRealTime = $url_json['entrants'][$j]['finish_time'];
                if($playerRealTime == '') {
                    $playerRealTime = 20000;
                    $playerIsForfeit = 'y';
                } else {
                    $playerRealTime = convertFinishTime($playerRealTime);
                    $playerIsForfeit = 'n';
                }
                $playerComment = $url_json['entrants'][$j]['comment'];
                if($playerComment == null) {
                    $playerComment = '';
                }
                $stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = ? AND racerRacetimeID = ?");
                $stmt->execute([$race_slug, $playerRacetimeID]);
                $resultExists = $stmt->fetchColumn();
                if(! $resultExists) {
                    $sql = "INSERT INTO results (raceSlug, racerRacetimeID, racerTeam, racerRealTime, racerComment, racerForfeit, racerFromRacetime) VALUES (?, ?, ?, ?, ?, ?, 'y')";
                    $pdo->prepare($sql)->execute([$race_slug, $playerRacetimeID, $playerTeam, $playerRealTime, $playerComment, $playerIsForfeit]);
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