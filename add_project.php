<?php
use Doingsdone\MySQL as MySQL;
use Doingsdone\Tasks as Tasks;
use Respect\Validation\Validator as V;


require_once('dbconn.php');
require_once('functions.php');
require_once('vendor/autoload.php');
session_start();
$USER = isset($_SESSION['USER'])?$_SESSION['USER']:null;
isAuth($USER);

$mysql = new MySQL($DB['host'], $DB['username'], $DB['password'], $DB['dbname']);
$mysqli = $mysql->getConnection();
$tasks = new Tasks($mysql);

//Для формы добавления задачи
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project = $_POST;
    $requiredValidator = V::notEmpty();
    $required_items = ['name'];
    $dict = ['name' => 'Название'];
    $errors = [];
    foreach ($required_items as $required_item) {
        if (!$requiredValidator->validate($project[$required_item])) {
            $errors[$required_item] = 'Это поле нужно заполнить';
        }
    }
    if (!empty($project['name'])) {
        $project_query = "SELECT name AS NAME FROM projects WHERE author_id = ".$USER['id'];
        $result = $mysql->makeQuery($project_query);
        $project_name_lowercase = mb_strtolower($project['name']);
        while ($res = $result->fetch_assoc()) {
            if (V::equals(mb_strtolower($res['NAME']))->validate($project_name_lowercase)) {
                $errors['name'] = 'Проект с таким именем уже существует';
            }
        }
    }
    if (!count($errors)) {
        $safe_project_name = $mysqli->real_escape_string($project['name']);
        $insert_project_query = "INSERT INTO projects SET
        name = '$safe_project_name',
        author_id = '".$USER['id']."'";
        $result = $mysql->makeQuery($insert_project_query);
    }
}
//Запрашиваем проекты и задачи пользователя по его ID - меню
$menu_items = $tasks->getMenu($USER['id']);
$mysqli->close();

$content = include_template('add_project.php', [
    'errors' => $errors??NULL,
    'dict' => $dict??NULL
]);

echo include_template('layout.php', [
    'all_tasks_count' => $tasks->countAllTasks($USER['id']),
    'menu_items' => $menu_items,
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
