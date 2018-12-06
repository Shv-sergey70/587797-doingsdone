<?php
require_once('functions.php');

$mysqli = new mysqli("localhost", "root", "root", "DOINGSDONE");

if ($mysqli->connect_error) {
    die('Ошибка подключения ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
}

//$city = $mysqli->real_escape_string($city);
$user_id = intval(1);
//Запрашиваем проекты пользователя по его ID

$projects_list_query = "SELECT name AS NAME FROM projects WHERE author_id = $user_id";
if (!$result = $mysqli->query($projects_list_query)) {
    die('Ошибка в запросе '.$projects_list_query.' - '.$mysqli->error);
}
$projects_items = [];
while ($res = $result->fetch_assoc()) {
    $projects_items[] = $res;
}

//Запрашиваем задачи пользователя по его ID
$tasks_list_query = "SELECT 
tasks.name AS TASK_NAME,
tasks.deadline_datetime AS TASK_DEADLINE,
tasks.status AS TASK_STATUS,
projects.name AS PROJECT_NAME
FROM tasks 
JOIN projects
ON tasks.project_id = projects.id
WHERE tasks.author_id = $user_id";

if (!$result = $mysqli->query($tasks_list_query)) {
    die('Ошибка в запросе '.$tasks_list_query.' - '.$mysqli->error);
}
$tasks_items = [];
while ($res = $result->fetch_assoc()) {
    $tasks_items[] = $res;
}
//echo "<pre>";
//var_dump($tasks_items);
//echo "</pre>";

$mysqli->close();
//Считаем задачи в проектах
foreach ($projects_items as $key => $projects_item) {
    $projects_items[$key]['TASKS_COUNT'] = countTasksInProject($tasks_items, $projects_item['NAME'], 'PROJECT_NAME');
}

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$content = include_template('index.php', [
    'tasks_items' => $tasks_items,
    'show_complete_tasks' => $show_complete_tasks
]);

echo include_template('layout.php', [
    'menu_items' => $projects_items,
    'title' => 'Дела в порядке',
    'content' => $content
]);
