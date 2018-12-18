<?php
use Doingsdone\MySQL as MySQL;
use Doingsdone\Tasks as Tasks;


require_once('functions.php');
require_once('vendor/autoload.php');
session_start();
$USER = isset($_SESSION['USER'])?$_SESSION['USER']:null;
isAuth($USER);

$mysql = new MySQL("localhost", "root", "root", "DOINGSDONE");
$mysqli = $mysql->getConnection();
$tasks = new Tasks($mysql);

//Для формы добавления задачи
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project = $_POST;
    $required_items = ['name'];
    $dict = ['name' => 'Название'];
    $errors = [];
    foreach ($required_items as $required_item) {
        if (empty($project[$required_item])) {
            $errors[$required_item] = 'Это поле нужно заполнить';
        }
    }
    if (!empty($project['name'])) {
        $project_query = "SELECT name AS NAME FROM projects WHERE author_id = ".$USER['id'];
        $result = $mysql->makeQuery($project_query);
        while ($res = $result->fetch_assoc()) {
            if (mb_strtolower($res['NAME']) === mb_strtolower($project['name'])) {
                $errors['name'] = 'Проект с таким именем уже существует';
            }
        }
    }
    if (count($errors)) {
        $content = include_template('add_project.php', [
            'errors' => $errors,
            'dict' => $dict
        ]);
    } else {
        $safe_project_name = $mysqli->real_escape_string($project['name']);
        $insert_project_query = "INSERT INTO projects SET
        name = '$safe_project_name',
        author_id = '".$USER['id']."'";
        $result = $mysql->makeQuery($insert_project_query);
        $content = include_template('add_project.php', [
        ]);
    }
} else {
    $content = include_template('add_project.php', [
    ]);
}


//Запрашиваем проекты и задачи пользователя по его ID - меню
$menu_items = $tasks->getMenu($USER['id']);

$mysqli->close();
echo include_template('layout.php', [
    'menu_items' => $menu_items,
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
