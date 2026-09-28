<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function generarTokenCsrf() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];

function validarTokenCsrf($tokenEnviado) {
    if (!isset($_SESSION['csrf_token']) || empty($tokenEnviado)) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $tokenEnviado);
}