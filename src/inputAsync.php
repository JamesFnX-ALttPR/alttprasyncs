<?php
include('../src/selectJS.php');
echo '        <form method="post" autocomplete="off" action="' . $domain . '/createasync">' . PHP_EOL;
echo '            <table class="createAsync">' . PHP_EOL;
echo '                <caption>Create New Async</caption>' . PHP_EOL;
echo '                <thead>' . PHP_EOL;
echo '                    <tr><th><label for="seed" title="Include the entire URL for the seed\'s permalink, starting with https://">Link to Seed</label></th><th><label for="mode" title="If there\'s an existing preset, it will autcomplete.">Mode</label></th></tr>' . PHP_EOL;
echo '                </thead>' . PHP_EOL;
echo '                <tbody>' . PHP_EOL;
echo '                    <tr><td class="centerAlign"><input type="text" size="46" id="seed" name="seed" required /></td><td class="centerAlign"><input size="46" list="modes" id="mode" name="mode" required />' . PHP_EOL;
echo '                        <datalist id="modes">' . PHP_EOL;
$stmt = $pdo->query('SELECT DISTINCT raceMode FROM races ORDER BY raceMode');
foreach($stmt as $row) {
    echo '                            <option value="' . $row['raceMode'] . '"></option>' . PHP_EOL;
}
echo '                        </datalist>' . PHP_EOL;
echo '                    <tr><th colspan="2"><label for="hash1" title="The hash as it appears on the file select screen.">Hash</label></th></tr>' . PHP_EOL;
echo '                    <tr><td colspan="2"><select id="hash1" name="hash1" required>' . PHP_EOL;
createHashDropdown();
echo '                    </select> <select id="hash2" name="hash2" required>' . PHP_EOL;
createHashDropdown();
echo '                    </select> <select id="hash3" name="hash3" required>' . PHP_EOL;
createHashDropdown();
echo '                    </select> <select id="hash4" name="hash4" required>' . PHP_EOL;
createHashDropdown();
echo '                    </select> <select id="hash5" name="hash5" required>' . PHP_EOL;
createHashDropdown();
echo '                    </select></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="description">Description:</label></th><td class="centerAlign"><input type="text" size="46" id="description" name="description" /></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="spoiler" title="Check here if this is a spoiler mode. A field will appear to add a link to the spoiler log.">Spoiler?</label></th><td><input type="checkbox" id="spoiler" name="spoiler" value="y" onclick="if (this.checked) { document.getElementsByClassName(\'spoiler\')[0].style.display = \'table-row\'; } else { document.getElementsByClassName(\'spoiler\')[0].style.display = \'none\'; }" /></td></tr>' . PHP_EOL;
echo '                    <tr class="spoiler"><th class="rightAlign"><label for="spoilerLog" title="Include the entire URL for the seed\'s spoiler log, starting with https://">Link to Spoiler Log:</label> </th><td class="centerAlign"><input type="text" size="46" id="spoilerLog" name="spoilerLog" /></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="team" title="Check here if this is a co-op/team seed meant for two players.">Co-Op/Team?</label></th><td><input type="checkbox" id="team" name="team" value="y" /></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="loginRequired" title="Check here if a user must login to submit a result.">Login Required?</label></th><td><input type="checkbox" id="loginRequired" name="loginRequired" value="y" /></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="vodRequired" title="Check here if a VOD link will be required to submit a result.">VOD Required?</label></th><td><input type="checkbox" id="vodRequired" name="vodRequired" value="y" /></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="editDisallowed" title="Check here if you would like users to not be able to edit their result submissions after entering them.">Disallow Edits?</label></th><td><input type="checkbox" id="editDisallowed" name="editDisallowed" value="y" /></td></tr>' . PHP_EOL;
echo '                    <tr><td colspan="2" class="submitButton"><input type="Submit" class="submitButton" value="Create Async" /></td></tr>' . PHP_EOL;
echo '                </tbody>' . PHP_EOL;
echo '            </table>' . PHP_EOL;
echo '        </form>' . PHP_EOL;