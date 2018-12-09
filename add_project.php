<?php
require_once('functions.php');

session_start();
$USER = isset($_SESSION['USER'])?$_SESSION['USER']:null;
isAuth($USER);

$mysqli = new mysqli("localhost", "root", "root", "DOINGSDONE");
if ($mysqli->connect_error) {
    die('Ошибка подключения ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
}

//$city = $mysqli->real_escape_string($city);
$user_id = intval(1);


//Запрашиваем проекты пользователя по его ID
$menu_items_query = "SELECT id AS ID, name AS NAME FROM projects WHERE author_id = '".$USER['id']."'";
if (!$result = $mysqli->query($menu_items_query)) {
    die('Ошибка в запросе '.$menu_items_query.' - '.$mysqli->error);
}
$menu_items = [];
while ($res = $result->fetch_assoc()) {
    $menu_items[] = $res;
}
//Запрашиваем задачи пользователя по его ID для меню
$tasks_list_query = "SELECT 
    tasks.name AS TASK_NAME,
    tasks.deadline_datetime AS TASK_DEADLINE,
    tasks.status AS TASK_STATUS,
    projects.name AS PROJECT_NAME
    FROM tasks 
    JOIN projects
    ON tasks.project_id = projects.id
    WHERE 
    tasks.author_id = '".$USER['id']."'";
if (!$result = $mysqli->query($tasks_list_query)) {
    die('Ошибка в запросе '.$tasks_list_query.' - '.$mysqli->error);
}
$tasks_items = [];
while ($res = $result->fetch_assoc()) {
    $tasks_items[] = $res;
}

//Считаем задачи в проектах
foreach ($menu_items as $key => $menu_item) {
    $menu_items[$key]['TASKS_COUNT'] = countTasksInProject($tasks_items, $menu_item['NAME'], 'PROJECT_NAME');
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
