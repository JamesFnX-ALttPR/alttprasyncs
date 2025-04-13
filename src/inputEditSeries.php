<?php

echo '        <table class="searchResults">' . PHP_EOL;
echo '            <tr><td class="rightAlign" colspan="3"><label for="name">Series Name:</label> </td><td colspan="3"><input size="50" type="text" id="name" name="name" form="editSeries" value="' . $series_name . '" required /></td></tr>' . PHP_EOL;
echo '            <tr><td class="rightAlign" colspan="3"><label for="desc">Series Description:</label> </td><td colspan="3"><textarea id="desc" name="desc" form="editSeries" rows="3" cols="49" required>' . $seriesDesc . '</textarea></td></tr>' . PHP_EOL;
echo '            <tr><td class="centerAlign" colspan="6"><input type="submit" class="submitButton" form="editSeries" value="Update Series Info" /></td></tr>' . PHP_EOL;
echo '            <tr><th>Date (UTC)</th><th>Mode</th><th>Description</th><th>Racetime Room</th><th>Hash</th><th><form method="post" action="' . $domain . '/editseries/' . $seriesID . '" id="editSeries"><input type="submit" class="submitButton" form="editSeries" value="Delete Checked Races" /></form></tr>' . PHP_EOL;
$memberArray = explode(', ', $series_members);
$rowCounter = 0;
foreach($memberArray as $raceID) {
    $race_id = intval($raceID);
    require ('../includes/race_info.php');
    $rowCounter++;
    if($rowCounter % 2 == 0) {
        $startOfRow = '                <tr class="even">';
    } else {
        $startOfRow = '                <tr class="odd">';
    }
    echo $startOfRow . '<td>' . $race_date . '</td><td>' . $race_mode . '</td><td>' . $race_description_short . '</td><td>';
    if ($race_from_racetime == 'y' ) {
        echo '<a target="_blank" href="https://racetime.gg/' . $race_slug . '">';
        echo $short_slug;
    } else {
        echo $race_slug;
    }
    if ($race_from_racetime == 'y') {
        echo '</a>';
    }
    echo '</td><td>' . hashToImages($race_hash) . '</td>';
    echo '<td><input type="checkbox" form="editSeries" id="seed_' . $race_id . '" name="seed_' . $race_id . '" /><label for="seed_' . $race_id . '"> Check To Delete</label></td>';
    echo '</tr>' . PHP_EOL;        
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;