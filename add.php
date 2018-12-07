<?php
require_once('functions.php');


$mysqli = new mysqli("localhost", "root", "root", "DOINGSDONE");
if ($mysqli->connect_error) {
    die('Ошибка подключения ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
}

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



$content = include_template('add_task.php', [
]);

echo include_template('layout.php', [
    'menu_items' => $menu_items,
    'title' => 'Дела в порядке',
    'content' => $content
]);
