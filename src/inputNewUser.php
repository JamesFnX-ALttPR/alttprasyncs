<?php

echo '        <form method="post" autocomplete="off" action="' . $domain . '/register">' . PHP_EOL;
echo '            <table class="register">' . PHP_EOL;
echo '                <thead>' . PHP_EOL;
echo '                    <caption>Register Account</caption>' . PHP_EOL;
echo '                </thead>' . PHP_EOL;
echo '                <tbody>' . PHP_EOL;
echo '                    <tr><td><label for="email">Email: </label></td><td><input type="text" id="email" name="email" placeholder="you@domain.com" required ';
if (isset($_POST['email'])) {
    echo 'value="' . $_POST['email'] . '"';
}
echo '/></td></tr>' . PHP_EOL;
echo '                    <tr><td><label for="displayName">Display Name: </label></td><td><input type="text" id="displayName" name="displayName" required ';
if (isset($_POST['displayName'])) {
    echo 'value="' . $_POST['displayName'] . '"';
}
echo '/></td></tr>' . PHP_EOL;
echo '                    <tr><td><label class="passwordLabel" for="password1" title="Passwords must be at least eight characters. Passwords less than 16 characters must include uppercase, lowercase, numeric, and special characters.">Password: </label></td><td><input type="password" id="password1" name="password1" required /></td></tr>' . PHP_EOL;
echo '                    <tr><td><label for="password2">Confirm Password: </label></td><td><input type="password" id="password2" name="password2" required /></td></tr>' . PHP_EOL;
echo '                    <tr><td colspan="2" class="submitButton"><input type="Submit" class="submitButton" value="Register" />' . PHP_EOL;
echo '                </tbody>' . PHP_EOL;
echo '            </table>' . PHP_EOL;
echo '        </form>' . PHP_EOL;