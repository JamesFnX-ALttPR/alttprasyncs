<?php

$racer1Name = $_POST['racer1Name'];
$racer2Name = $_POST['racer2Name'];
$teamName = $_POST['teamName'];
if(!isset($_POST['racer1Forfeit'])) { // Check if the forfeit box was left unchecked
    $teamForfeit = 'n';
    if($_POST['racer1RTHours'] != '' && $_POST['racer1RTMinutes'] != '' && $_POST['racer1RTSeconds'] != '') { // Validate time entry is correct (all three boxes filled out)
        $racer1RealTime = ( 3600 * intval($_POST['racer1RTHours']) ) + ( 60 * intval($_POST['racer1RTMinutes']) ) + intval($_POST['racer1RTSeconds']);
    } else {
        $errorCondition = 'Player 1 Real Time not input correctly'; // Set error condition if time does not validate
    }
    if($_POST['racer2RTHours'] != '' && $_POST['racer2RTMinutes'] != '' && $_POST['racer2RTSeconds'] != '') { // Validate time entry is correct (all three boxes filled out)
        $racer2RealTime = ( 3600 * intval($_POST['racer2RTHours']) ) + ( 60 * intval($_POST['racer2RTMinutes']) ) + intval($_POST['racer2RTSeconds']);
    } else {
        if($errorCondition == null) {
            $errorCondition = 'Player 2 Real Time not input correctly'; // Set error condition if time does not validate
        } else {
            $errorCondition = $errorCondition . '<br />' . PHP_EOL . 'Player 2 Real Time not input correctly';
        }
    }
    if($_POST['racer1IGTHours'] != '' && $_POST['racer1IGTMinutes'] != '' && $_POST['racer1IGTSeconds']!= '') { // Validate IGT entry is correct (all three boxes filled out)
        $racer1IGT = ( 3600 * intval($_POST['racer1IGTHours']) ) + ( 60 * intval($_POST['racer1IGTMinutes']) ) + intval($_POST['racer1IGTSeconds']);
    } elseif($_POST['racer1IGTHours'] == '' && $_POST['racer1IGTMinutes'] == '' && $_POST['racer1IGTSeconds'] == '') { // If IGT doesn't validate, all boxes empty is still acceptable
        $racer1IGT = null;
    } else {
        if($errorCondition == null) { // Set error condition if IGT does not validate
            $errorCondition = 'Player 1 In Game Time not input correctly';
        } else {
            $errorCondition = $errorCondition . '<br />' . PHP_EOL . 'Player 1 In Game Time not input correctly';
        }
    }
    if($_POST['racer2IGTHours'] != '' && $_POST['racer2IGTMinutes'] != '' && $_POST['racer2IGTSeconds']!= '') { // Validate IGT entry is correct (all three boxes filled out)
        $racer2IGT = ( 3600 * intval($_POST['racer2IGTHours']) ) + ( 60 * intval($_POST['racer2IGTMinutes']) ) + intval($_POST['racer2IGTSeconds']);
    } elseif($_POST['racer2IGTHours'] == '' && $_POST['racer2IGTMinutes'] == '' && $_POST['racer2IGTSeconds'] == '') { // If IGT doesn't validate, all boxes empty is still acceptable
        $racer2IGT = null;
    } else {
        if($errorCondition == null) { // Set error condition if IGT does not validate
            $errorCondition = 'Player 2 In Game Time not input correctly';
        } else {
            $errorCondition = $errorCondition . '<br />' . PHP_EOL . 'Player 2 In Game Time not input correctly';
        }
    }
    if($_POST['racer1CR'] != '') { // Check if there's a CR and output null if not
        $racer1CR = $_POST['racer1CR'];
    } else {
        $racer1CR = null;
    }
    if($_POST['racer2CR'] != '') { // Check if there's a CR and output null if not
        $racer2CR = $_POST['racer2CR'];
    } else {
        $racer2CR = null;
    }
    if($_POST['racer1Comments'] != '') { // Check if there are comments and output null if not
        $racer1Comment = $_POST['racer1Comments'];
    } else {
        $racer1Comment = null;
    }
    if($_POST['racer2Comments'] != '') { // Check if there are comments and output null if not
        $racer2Comment = $_POST['racer2Comments'];
    } else {
        $racer2Comment = null;
    }
    if($_POST['racer1VOD'] != '') { // Check if there is a VOD and validate the link is in a proper format
        if(substr($_POST['racer1VOD'], 0, 8) == 'https://' || substr($_POST['racer1VOD'], 0, 7) == 'http://') {
            $racer1VOD = $_POST['racer1VOD'];
        } else {
            if($errorCondition == null) { // Set error condition if VOD is not in correct format
                $errorCondition = 'Player 1 VOD Link not input correctly (Did you start with http:// or https://?)';
            } else {
                $errorCondition = $errorCondition . '<br />' . PHP_EOL . 'Player 1 VOD Link not input correctly (Did you start with http:// or https://?)';
            } 
        }
    } else {
        $racer1VOD = null;
    }
    if($_POST['racer2VOD'] != '') { // Check if there is a VOD and validate the link is in a proper format
        if(substr($_POST['racer2VOD'], 0, 8) == 'https://' || substr($_POST['racer2VOD'], 0, 7) == 'http://') {
            $racer2VOD = $_POST['racer2VOD'];
        } else {
            if($errorCondition == null) { // Set error condition if VOD is not in correct format
                $errorCondition = 'Player 2 VOD Link not input correctly (Did you start with http:// or https://?)';
            } else {
                $errorCondition = $errorCondition . '<br />' . PHP_EOL . 'Player 2 VOD Link not input correctly (Did you start with http:// or https://?)';
            } 
        }
    } else {
        $racer2VOD = null;
    }
} else { // If the forfeit box is checked, this sets the interesting boxes.
    $teamForfeit = 'y';
    $racer1RealTime = 35940;
    $racer2RealTime = 35940;
    $racer1IGT = null;
    $racer2IGT = null;
    $racer1CR = null;
    $racer2CR = null;
    if($_POST['racer1Comments'] != '') { // Check if there are comments and output null if not
        $racer1Comment = $_POST['racer1Comments'];
    } else {
        $racer1Comment = null;
    }
    if($_POST['racer2Comments'] != '') { // Check if there are comments and output null if not
        $racer2Comment = $_POST['racer2Comments'];
    } else {
        $racer2Comment = null;
    }
    if($_POST['racer1VOD'] != '') { // Check if there is a VOD and validate the link is in a proper format
        if(substr($_POST['racer1VOD'], 0, 8) == 'https://' || substr($_POST['racer1VOD'], 0, 7) == 'http://') {
            $racer1VOD = $_POST['racer1VOD'];
        } else {
            if($errorCondition == null) { // Set error condition if VOD is not in crrect format
                $errorCondition = 'Player 1 VOD Link not input correctly (Did you start with http:// or https://?)';
            } else {
                $errorCondition = $errorCondition . '<br />' . PHP_EOL . 'Player 1 VOD Link not input correctly (Did you start with http:// or https://?)';
            } 
        }
    } else {
        $racer1VOD = null;
    }
    if($_POST['racer2VOD'] != '') { // Check if there is a VOD and validate the link is in a proper format
        if(substr($_POST['racer2VOD'], 0, 8) == 'https://' || substr($_POST['racer2VOD'], 0, 7) == 'http://') {
            $racer2VOD = $_POST['racer2VOD'];
        } else {
            if($errorCondition == null) { // Set error condition if VOD is not in crrect format
                $errorCondition = 'Player 2 VOD Link not input correctly (Did you start with http:// or https://?)';
            } else {
                $errorCondition = $errorCondition . '<br />' . PHP_EOL . 'Player 2 VOD Link not input correctly (Did you start with http:// or https://?)';
            } 
        }
    } else {
        $racer2VOD = null;
    }
}
if (isset($_POST['enteredBy'])) {
    $enteredBy = $_POST['enteredBy'];
}