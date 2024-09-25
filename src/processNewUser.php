<?php
 
 $errors = '';
 // Check if email is in valid format and is not already registered
 if ($_POST['email'] != '') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors .= 'Email submitted is <strong>NOT</strong> a valid email address.<br />';
    } else {
        $checkDuplicateEmail = $pdo->prepare('SELECT id FROM asyncusers WHERE email = ?');
        $checkDuplicateEmail->execute([$email]);
        $row = $checkDuplicateEmail->fetchColumn();
        if ($row) {
            $errors .= 'Email submitted is already registered, please try logging in.<br />';
        }
    }
} else {
    $errors .= 'Please enter your email address.<br />';
}

// Check if password is in a valid format
if ($_POST['password1'] != '') {
    if(strlen($_POST['password1']) < 8) {
        $errors .= 'Password does not meet requirements.<br />';
    }
    $pattern = "#.*^(?=.{8,64})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#";
    if (preg_match($pattern, $_POST['password1']) == false && strlen($_POST['password1']) < 16) {
        $errors .= 'Password does not meet requirements.<br />';
    }
} else {
    $errors .= 'Please enter a new password.<br />';
}

// Check if password fields match
if ($_POST['password2'] != '') {
    if ($_POST['password2'] != $_POST['password1']) {
        $errors .= 'Passwords do not match. Please make sure they match and try again.<br />';
    }
} else {
    $errors .= 'Please enter a new password.<br />';
}

$displayName = strip_tags($_POST['displayName']);

// Output errors if needed, otherwise add to database and load login page
if ($errors != '') {
    echo '        <div class="error">' . $errors . ' - Please Try Again</div><br /><hr />' . PHP_EOL;
    require_once ('../src/inputNewUser.php');
} else {
    $login_ip = $_SERVER['REMOTE_ADDR'];
    $sql = "INSERT INTO asyncusers (email, password, is_admin, displayName, registered_ip, registered_date) VALUES (:email, :password, 'n', :displayName, :ip, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', password_hash($_POST['password1'], PASSWORD_BCRYPT));
    $stmt->bindValue(':displayName', $displayName, PDO::PARAM_STR);
    $stmt->bindValue(':ip', $login_ip, PDO::PARAM_STR);
    $stmt->execute();
    require_once ('../src/loginForm.php');
}