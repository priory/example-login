<?php
require_once __DIR__.'/../app/pdo.php';

function log_login_attempt($user, $success) {
    $log = new SplFileObject(__DIR__.'\..\login_attempts.log', 'a');
    $dateTime = new DateTime('now');

    $log->fwrite(
        $dateTime->format('[Y-m-d H:i:s]').' '.
        ($success ? 'Successful login from' : 'Failed login attempt from').' '.
        'IP-address: '.get_ip_address().' '.
        'with name: '.$user.' '.
        chr(10)
    );

    $log = null;
}

function get_ip_address() {
    return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
}

function login_attempt_ip() {
    global $pdo;
    $ip = get_ip_address();

    $sth = $pdo->prepare('SELECT `ip`, `last_attempt`, `attempts` FROM `login_attempts_ip` WHERE `ip` = :ip');
    $sth->bindValue(':ip', $ip, PDO::PARAM_STR);
    $sth->execute();

    if ($sth->errorInfo()[1]) {
        trigger_error('MySQL error', E_USER_ERROR);
        log_mysql_error($sth);
        die;
    }

    $result = $sth->fetchAll(PDO::FETCH_OBJ);

    if (count($result) === 0) {
        $sth = $pdo->prepare('INSERT INTO `login_attempts_ip`(`ip`, `last_attempt`, `attempts`) VALUES (:ip, :last_attempt, :attempts)');
        $sth->bindValue(':ip', $ip, PDO::PARAM_STR);
        $sth->bindValue(':last_attempt', (new DateTime('now'))->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $sth->bindValue(':attempts', 1, PDO::PARAM_INT);
        $sth->execute();

        if ($sth->errorInfo()[1]) {
            trigger_error('MySQL error', E_USER_ERROR);
            log_mysql_error($sth);
            die;
        }
    } else {
        $ip = $result[0]->ip;
        $attempts = $result[0]->attempts;
        $last_attempt = new DateTime($result[0]->last_attempt);
        $date = new DateTime('now');
        $diff = $date->getTimestamp() - $last_attempt->getTimestamp();

        if ($diff < 900) {
            $attempts += 1;
        } else {
            $attempts = 1;
        }

        $sth = $pdo->prepare('UPDATE `login_attempts_ip` SET `last_attempt` = :last_attempt, `attempts` = :attempts WHERE `ip` = :ip');
        $sth->bindValue(':ip', $ip, PDO::PARAM_STR);
        $sth->bindValue(':last_attempt', $date->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $sth->bindValue(':attempts', $attempts, PDO::PARAM_INT);
        $sth->execute();

        if ($sth->errorInfo()[1]) {
            trigger_error('MySQL error', E_USER_ERROR);
            log_mysql_error($sth);
            die;
        }
    }
}

function get_login_attempts_ip() {
    global $pdo;
    $ip = get_ip_address();

    $sth = $pdo->prepare('SELECT `ip`, `last_attempt`, `attempts` FROM `login_attempts_ip` WHERE `ip` = :ip');
    $sth->bindValue(':ip', $ip, PDO::PARAM_STR);
    $sth->execute();

    if ($sth->errorInfo()[1]) {
        trigger_error('MySQL error', E_USER_ERROR);
        log_mysql_error($sth);
        die;
    }

    $result = $sth->fetchAll(PDO::FETCH_OBJ);

    if (count($result) === 1) {
        $ip = $result[0]->ip;
        $last_attempt = new DateTime($result[0]->last_attempt);
        $date = new DateTime('now');
        $diff = $date->getTimestamp() - $last_attempt->getTimestamp();

        if ($diff >= 900) {
            $sth = $pdo->prepare('UPDATE `login_attempts_ip` SET `last_attempt` = :last_attempt, `attempts` = :attempts WHERE `ip` = :ip');
            $sth->bindValue(':ip', $ip, PDO::PARAM_STR);
            $sth->bindValue(':last_attempt', $date->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $sth->bindValue(':attempts', 1, PDO::PARAM_INT);
            $sth->execute();
    
            if ($sth->errorInfo()[1]) {
                trigger_error('MySQL error', E_USER_ERROR);
                log_mysql_error($sth);
                die;
            }

            return 1;
        }

        return $result[0]->attempts;
    }

    return 0;
}