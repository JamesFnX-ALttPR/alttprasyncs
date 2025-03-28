<!DOCTYPE html>
<html lang="en-US">
    <head>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="<?= $domain ?>/includes/styles.css" />
        <title>ALttPR Asyncs - <?= $pageTitle ?></title>
        <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
        <script src="<?= $domain ?>/sorttable.js"></script>
        <meta charset="UTF-8">
    </head>
    <body>
        <div class="topline"><a class="toplinks" href="<?= $domain ?>">Search</a><?php $stmt = $pdo->prepare("SELECT id FROM series"); $stmt->execute(); $chk = $stmt->fetchColumn(); if ($chk) {     echo '<a class="toplinks" href="' . $domain . '/series">Series</a>'; } echo '<a class="toplinks" href="' . $domain . '/discord" target="_blank">Discord</a>'; if (isset($_SESSION['userid'])) {     echo '<a class="toplinks" href="' . $domain . '/account">Account</a><a class="toplinks" href="' . $domain . '/logout">Logout</a>'; } else {     echo '<a class="toplinks" href="' . $domain . '/login">Login</a>'; } ?></div>
        <hr />
        <h1>ALttPR Asyncs</h1>
