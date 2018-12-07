<?php
require_once('functions.php');

$mysqli = new mysqli("localhost", "root", "root", "DOINGSDONE");
if ($mysqli->connect_error) {
    die('Ошибка подключения ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
}
$USER = true;
//$city = $mysqli->real_escape_string($city);
$user_id = intval(1);


//Запрашиваем проекты пользователя по его ID
$menu_items_query = "SELECT id AS ID, name AS NAME FROM projects WHERE author_id = $user_id";
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
    tasks.author_id = $user_id";
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


//Запрашиваем задачи в выбранном проекте
$selected_menu_isset = false;
$current_tasks_items = [];
if (!empty($_GET['id'])) {
    $selected_menu_item_id = intval($_GET['id']);
    foreach ($menu_items as $menu_item) {
        if ((int)$menu_item['ID'] === $selected_menu_item_id) {
            $selected_menu_isset = true;
        }
    }
    if (!$selected_menu_isset) {
        header("HTTP/1.x 404 Not Found");
        die();
    }
} else {
    $current_tasks_items = $tasks_items;
}
if ($selected_menu_isset) {
    //Запрашиваем задачи пользователя по его ID и ID проекта для вывода задач
    $current_tasks_list_query = "SELECT 
        tasks.name AS TASK_NAME,
        tasks.deadline_datetime AS TASK_DEADLINE,
        tasks.status AS TASK_STATUS,
        projects.name AS PROJECT_NAME
        FROM tasks 
        JOIN projects
        ON tasks.project_id = projects.id
        WHERE
        tasks.project_id = $selected_menu_item_id AND
        tasks.author_id = $user_id";
    if (!$result = $mysqli->query($current_tasks_list_query)) {
        die('Ошибка в запросе '.$current_tasks_list_query.' - '.$mysqli->error);
    }
    while ($res = $result->fetch_assoc()) {
        $current_tasks_items[] = $res;
    }
}
//echo "<pre>";
//var_dump($tasks_items);
//echo "</pre>";


$mysqli->close();


// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);


$content = include_template('index.php', [
    'current_tasks_items' => $current_tasks_items,
    'show_complete_tasks' => $show_complete_tasks
]);

echo include_template('layout.php', [
    'menu_items' => $menu_items,
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
