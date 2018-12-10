<?php
require_once('functions.php');

session_start();
$USER = isset($_SESSION['USER'])?$_SESSION['USER']:null;
isAuth($USER);

$mysqli = new mysqli("localhost", "root", "root", "DOINGSDONE");
if ($mysqli->connect_error) {
    die('Ошибка подключения ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
}


//Запрашиваем проекты и задачи пользователя по его ID
$all_items_query = "SELECT
projects.id AS ID,
projects.name AS NAME,
COUNT(tasks.id) AS TASKS_COUNT
FROM projects
LEFT JOIN tasks
ON projects.id = tasks.project_id
WHERE projects.author_id = '".$USER['id']."'
GROUP BY projects.id";
if (!$result = $mysqli->query($all_items_query)) {
    die('Ошибка в запросе '.$all_items_query.' - '.$mysqli->error);
}
while ($res = $result->fetch_assoc()) {
    $menu_items[] = $res;
}

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
        $project_query = "SELECT name AS NAME FROM projects WHERE author_id = '".$USER['id']."'";
        if (!$result = $mysqli->query($project_query)) {
            die('Ошибка в запросе '.$project_query.' - '.$mysqli->error);
        }
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
        if (!$result = $mysqli->query($insert_project_query)) {
            die('Ошибка в запросе '.$insert_project_query.' - '.$mysqli->error);
        }
        $content = include_template('add_project.php', [
        ]);
    }
} else {
    $content = include_template('add_project.php', [
    ]);
}



$mysqli->close();
echo include_template('layout.php', [
    'menu_items' => $menu_items,
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
