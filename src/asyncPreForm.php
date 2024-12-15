<?php
$stmt = $pdo->prepare("SELECT count(1) FROM results WHERE raceSlug = :slug");
$stmt->bindParam(':slug', $raceSlup, PDO::PARAM_STR);
$stmt->execute();
$racerCount = $stmt->fetchColumn();
$url = 'https://sahasrahbotapi.synack.live/presets/api/alttpr?preset=' . $raceMode;
$data = curlData($url);
$raceModeDesc = parseSynackAPI($data);
$stmt = $pdo->prepare("SELECT name, description FROM modes WHERE name = :mode");
$stmt->bindParam(':mode', $raceMode, PDO::PARAM_STR);
$stmt->execute();
$rslt = $stmt->fetch();
if(! $rslt) {
    $stmt2 = $pdo->prepare("INSERT INTO modes (name, description) VALUES (:mode, :desc)");
    $stmt2->bindParam(':mode', $raceMode, PDO::PARAM_STR);
    $stmt2->bindParam(':desc', $raceModeDesc, PDO::PARAM_STR);
    $stmt2->execute();
} else {
    $stmt2 = $pdo->prepare("UPDATE modes SET description = :desc WHERE name = :mode");
    $stmt2->bindParam(':desc', $raceModeDesc, PDO::PARAM_STR);
    $stmt2->bindParam(':mode', $raceMode, PDO::PARAM_STR);
    $stmt2->execute();
}
?>
        <div class="asyncTopRow">Submit Async for <?php if($raceFromRacetime == 'y') { echo '<a target="_blank" href="https://racetime.gg/alttpr/' . $raceSlug . '">' . $raceSlug . '</a>'; } else { echo $raceSlug; } ?></div><br />
        <div class="asyncMiddle">Mode: <?= $raceMode ?><br />
<?php
if($raceIsTeam == 'y') {
    if ($raceDescription == '') {
        $raceDescription = 'CO-OP/TEAM';
    } else {
        $raceDescription = 'CO-OP/TEAM - ' . $raceDescription;
    }
}
if($raceIsSpoiler == 'y') {
    if($raceDescription == '') {
        $raceDescription = '<a target="_blank" href="' . $raceSpoilerLink . '">Download Spoiler Log</a>';
    } else {
        $raceDescription = $raceDescription . ' - <a target="_blank" href="' . $raceSpoilerLink . '">Download Spoiler Log</a>';
    }
}
?>
        <?php if ($raceDescription != '') { echo '<br />'; } ?>
        Seed Link - <a target="_blank" href="<?= $raceSeed ?>"><?= $raceSeed ?></a> - Hash: <?php echo hashToImages($raceHash); ?><br />
<?php $stmt = $pdo->prepare('SELECT racetimeName FROM racerinfo WHERE racetimeID IN (SELECT racerRacetimeID FROM results WHERE raceSlug = ?) ORDER BY racetimeName');
$stmt->execute([$raceSlug]);
$racerList = '';
while($row = $stmt->fetch()) {
    $racerList = $racerList . $row['racetimeName'] . ', ';
}
$racerList = substr($racerList, 0, -2);
?>
        Participants: <?= $racerList ?></div>
        <hr />
        <form action="<?= $domain ?>/async/<?= $raceID ?>" method="post" autocomplete="off">
            <table class="submitAsync">
                <caption>Submit Your Time
