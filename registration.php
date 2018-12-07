<?php
require_once('functions.php');

$USER = null;

$mysqli = new mysqli("localhost", "root", "root", "DOINGSDONE");
if ($mysqli->connect_error) {
    die('Ошибка подключения ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_user = $_POST;
    $safe_user_email = $mysqli->real_escape_string($new_user['email']);
    $required_fields = ['email', 'password', 'name'];
    $dict = ['email' => 'E-mail', 'password' => 'Пароль', 'name' => 'Имя'];
    $errors = [];
    foreach ($required_fields as $required_field) {
        if (empty($new_user[$required_field])) {
            $errors[$required_field] = 'Это поле нужно заполнить';
        }
    }


    if (!empty($new_user['email'])) {
        //Проверка email на валидность
        if (!filter_var($new_user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите валидный E-mail';
        } else {
            //Проверка email на занятость
            $email_query = "SELECT 
email AS EMAIL 
FROM users 
WHERE email = '$safe_user_email'";
            if (!$result = $mysqli->query($email_query)) {
                die('Ошибка в запросе '.$email_query.' - '.$mysqli->error);
            }
            if ($result->num_rows) {
                $errors['email'] = 'Такой email уже зарегистрирован';
            }
        }
    }


    if (count($errors)) {
        $content = include_template('registration.php', [
            'errors' => $errors,
            'dict' => $dict
        ]);
    } else {
        $user_password_hash = password_hash($new_user['password'], PASSWORD_DEFAULT);
        $safe_user_password_hash = $mysqli->real_escape_string($user_password_hash);
        $register_query = "INSERT INTO users
SET email = '$safe_user_email',
name = '".$new_user['name']."',
password = '$safe_user_password_hash'";
        if (!$result = $mysqli->query($register_query)) {
            die('Ошибка в запросе '.$register_query.' - '.$mysqli->error);
        }
        header('Location: /');
        die();
    }
} else {
    $content = include_template('registration.php', [
    ]);
}


$mysqli->close();
echo include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
