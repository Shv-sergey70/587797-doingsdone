<?php
use Doingsdone\MySQL as MySQL;
use Respect\Validation\Validator as V;


require_once('dbconn.php');
require_once('functions.php');
require_once('vendor/autoload.php');
session_start();
$USER = isset($_SESSION['USER'])?$_SESSION['USER']:null;
if ($USER) {
    header('Location: /');
    die();
}

$mysql = new MySQL($DB['host'], $DB['username'], $DB['password'], $DB['dbname']);
$mysqli = $mysql->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = $_POST;
    $requiredValidator = V::notEmpty();
    $emailValidator = V::email();
    $required_fields = ['email', 'password'];
    $dict = ['email' => 'E-mail', 'password' => 'Пароль', 'access' => 'Доступ'];
    $errors = [];
    //Проверка email на валидность
    if (!$emailValidator->validate($auth['email'])) {
        $errors['email'] = 'Введите валидный email';
    }
    foreach ($required_fields as $required_field) {
        if (!$requiredValidator->validate($auth[$required_field])) {
            $errors[$required_field] = 'Это поле нужно заполнить';
        }
    }
    //Запрашиваем email в бд если ошибок нет
    if (!count($errors)) {
        $safe_email = $mysqli->real_escape_string($auth['email']);
        $auth_query = "SELECT *
        FROM users
        WHERE
        email = '$safe_email'";
        $result = $mysql->makeQuery($auth_query);
        $DB_result = $result->fetch_assoc();
        if (empty($DB_result) || (!password_verify($auth['password'], $DB_result['password']))) {
            $errors['access'] = 'Вы ввели неверный email/пароль';
        } else {
            $_SESSION['USER'] = $DB_result;
            header('Location: /');
            die();
        }
    }
}


$content = include_template('login.php', [
    'errors' => $errors??NULL,
    'dict' => $dict??NULL
]);


echo include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
