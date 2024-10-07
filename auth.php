<?php
session_start();
require_once 'db_connect.php';

function login($documento, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE documento = ?");
    $stmt->execute([$documento]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_acceso'] = json_decode($user['acceso'], true);
        return true;
    }
    return false;
}

function logout() {
    session_unset();
    session_destroy();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

function has_access($module) {
    return in_array($module, $_SESSION['user_acceso']);
}