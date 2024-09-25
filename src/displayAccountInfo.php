<?php

$stmt = $pdo->prepare("SELECT email, displayName FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
$currentEmail = $row['email'];
$currentDisplayName = $row['displayName'];

echo '        <form method="post" autocomplete="off" action="' . $domain . '/settings">' . PHP_EOL;
echo '            <table class="register">' . PHP_EOL;
echo '                <caption>View/Edit Account Info</caption>' . PHP_EOL;
echo '                <tbody>' . PHP_EOL;
echo '                    <tr><th colspan="2">Change Email or Display Name</th></tr>' . PHP_EOL;
echo '                    <tr><td><label for="email">Email: </label></td><td><input type="text" id="email" name="email" value ="' . $currentEmail . '" required /></td></tr>' . PHP_EOL;
echo '                    <tr><td><label for="displayName">Display Name: </label></td><td><input type="text" id="displayName" name="displayName" value="' . $currentDisplayName . '" required /></td></tr>' . PHP_EOL;
echo '                    <tr><th colspan="2">Change Password</th></tr>' . PHP_EOL;
echo '                    <tr><td><label for="password1">New Password: </label></td><td><input type="password" id="password1" name="password1" /></td></tr>' . PHP_EOL;
echo '                    <tr><td><label for="password2">Confirm New Password: </label></td><td><input type="password" id="password2" name="password2" /></td></tr>' . PHP_EOL;
echo '                    <tr><td class="submitButton"><input type="Submit" class="submitButton" value="Update Info" /></td><td class="submitButton"><a href="' . $domain . '/account' . '" class="fakeButton">Go Back</a></td>' . PHP_EOL;
echo '                </tbody>' . PHP_EOL;
echo '            </table>' . PHP_EOL;
echo '        </form>' . PHP_EOL;