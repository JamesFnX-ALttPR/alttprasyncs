<?php

require_once ('../includes/bootstrap.php');
$pageTitle = 'Create/View/Edit Your Series';

require_once ('../includes/header.php');
if (isset($_SESSION['userid'])) {
    require('../src/displayUserSeries.php');
} else {
    echo '        <div class="error">You must log in to see this page.</div><br />' . PHP_EOL;
    require('../src/loginForm.php');
}
require_once ('../includes/footer.php');