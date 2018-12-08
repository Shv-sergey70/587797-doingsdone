<?php
require_once('functions.php');
$USER = null;

$mysqli = new mysqli("localhost", "root", "root", "DOINGSDONE");
if ($mysqli->connect_error) {
    die('Ошибка подключения ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = $_POST;
    $required_fields = ['email', 'password'];
    $dict = ['email' => 'E-mail', 'password' => 'Пароль', 'access' => 'Доступ'];
    $errors = [];
    foreach ($required_fields as $required_field) {
        if (empty($auth[$required_field])) {
            $errors[$required_field] = 'Это поле нужно заполнить';
        }
    }
    //Проверка email на валидность
    if (!empty($auth['email'])) {
        if (!filter_var($auth['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите валидный email';
        }
    }
    //Запрашиваем email в бд если ошибок нет
    if (!count($errors)) {
        $safe_email = $mysqli->real_escape_string($auth['email']);
        $auth_query = "SELECT *
        FROM users
        WHERE
        email = '$safe_email'";
        if (!$result = $mysqli->query($auth_query)) {
            die('Ошибка в запросе '.$auth_query.' - '.$mysqli->error);
        }
        if (!$result->num_rows) {
            $errors['access'] = 'Вы ввели неверный email/пароль';
        } else {
            $DB_result = $result->fetch_assoc();
            if (!password_verify($auth['password'], $DB_result['password'])) {
                $errors['access'] = 'Вы ввели неверный email/пароль';
            } else {
                session_start();
                $_SESSION['USER'] = $DB_result;
                header('Location: /');
                die();
            }
        }
    }
    if (count($errors)) {
        $content = include_template('login.php', [
            'errors' => $errors,
            'dict' => $dict
        ]);
    }
} else {
    $content = include_template('login.php', [
    ]);
}


echo include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
