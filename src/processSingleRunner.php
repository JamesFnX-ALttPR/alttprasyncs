<?php
// Process the form submission for an individual runner
$racerName = $_POST['racer1Name'];
if(!isset($_POST['racer1Forfeit'])) { // Check if the forfeit box was left unchecked
    $racerForfeit = 'n';
    if($_POST['racer1RTHours'] != '' && $_POST['racer1RTMinutes'] != '' && $_POST['racer1RTSeconds'] != '') { // Validate time entry is correct (all three boxes filled out)
        $racerRealTime = ( 3600 * intval($_POST['racer1RTHours']) ) + ( 60 * intval($_POST['racer1RTMinutes']) ) + intval($_POST['racer1RTSeconds']);
    } else {
        $errorCondition = 'Real Time not input correctly'; // Set error condition if time does not validate
    }
    if($_POST['racer1IGTHours'] != '' && $_POST['racer1IGTMinutes'] != '' && $_POST['racer1IGTSeconds']!= '') { // Validate IGT entry is correct (all three boxes filled out)
        $racerIGT = ( 3600 * intval($_POST['racer1IGTHours']) ) + ( 60 * intval($_POST['racer1IGTMinutes']) ) + intval($_POST['racer1IGTSeconds']);
    } elseif($_POST['racer1IGTHours'] == '' && $_POST['racer1IGTMinutes'] == '' && $_POST['racer1IGTSeconds'] == '') { // If IGT doesn't validate, all boxes empty is still acceptable
        $racerIGT = null;
    } else {
        if($errorCondition == null) { // Set error condition if IGT does not validate
            $errorCondition = 'In Game Time not input correctly';
        } else {
            $errorCondition = $errorCondition . '<br />' . PHP_EOL . 'In Game Time not input correctly';
        }
    }
    if($_POST['racer1CR'] != '') { // Check if there's a CR and output null if not
        $racerCR = $_POST['racer1CR'];
    } else {
        $racerCR = null;
    }
    if($_POST['racer1Comments'] != '') { // Check if there are comments and output null if not
        $racerComment = $_POST['racer1Comments'];
    } else {
        $racerComment = null;
    }
    if($_POST['racer1VOD'] != '') { // Check if there is a VOD and validate the link is in a proper format
        if(substr($_POST['racer1VOD'], 0, 8) == 'https://' || substr($_POST['racer1VOD'], 0, 7) == 'http://') {
            $racerVOD = $_POST['racer1VOD'];
        } else {
            if($errorCondition == null) { // Set error condition if VOD is not in correct format
                $errorCondition = 'VOD Link not input correctly (Did you start with http:// or https://?)';
            } else {
                $errorCondition = $errorCondition . '<br />' . PHP_EOL . 'VOD Link not input correctly (Did you start with http:// or https://?)';
            } 
        }
    } else {
        $racerVOD = null;
    }
} else { // If the forfeit box is checked, this sets the interesting boxes.
    $racerForfeit = 'y';
    $racerRealTime = 35940;
    $racerIGT = null;
    $racerCR = null;
    if($_POST['racer1Comments'] != '') { // Check if there are comments and output null if not
        $racerComment = $_POST['racer1Comments'];
    } else {
        $racerComment = null;
    }
    if($_POST['racer1VOD'] != '') { // Check if there is a VOD and validate the link is in a proper format
        if(substr($_POST['racer1VOD'], 0, 8) == 'https://' || substr($_POST['racer1VOD'], 0, 7) == 'http://') {
            $racerVOD = $_POST['racer1VOD'];
        } else {
            if($errorCondition == null) { // Set error condition if VOD is not in crrect format
                $errorCondition = 'VOD Link not input correctly (Did you start with http:// or https://?)';
            } else {
                $errorCondition = $errorCondition . '<br />' . PHP_EOL . 'VOD Link not input correctly (Did you start with http:// or https://?)';
            } 
        }
    }  else {
        $racerVOD = null;
    }       
}
if (isset($_POST['enteredBy'])) {
    $enteredBy = $_POST['enteredBy'];
}