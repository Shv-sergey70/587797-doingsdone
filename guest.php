<?php
require_once('functions.php');
$USER = null;

$content = include_template('guest.php', [
]);


//$mysqli->close();
echo include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
