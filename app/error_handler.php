<?php
function error_handler($errno, $errstr, $errfile, $errline) {
    $log = new SplFileObject(__DIR__.'\..\errors.log', 'a');
    $dateTime = new DateTime('now');

    $log->fwrite(
        $dateTime->format('[Y-m-d H:i:s]').' '.
        'errno: '.$errno.' '.
        'errstr: '.$errstr.' '.
        'errfile: '.$errfile.' '.
        'errline: '.$errline.' '.
        chr(10)
    );

    $log = null;

    echo '</br><strong>Something went wrong. Please contact the administrators.</strong></br>';
}

set_error_handler('error_handler');

function log_mysql_error($sth) {
    $log = new SplFileObject(__DIR__.'\..\errors.log', 'a');
    $dateTime = new DateTime('now');
    $ste = $sth->errorInfo();

    $log->fwrite(
        $dateTime->format('[Y-m-d H:i:s]').' '.
        'SQLSTATE: '.$ste[0].' '.
        'error code: '.$ste[1].' '.
        'error message: '.$ste[2].' '.
        chr(10)
    );

    $log = null;
}