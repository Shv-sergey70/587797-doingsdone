<?php
require_once('functions.php');

session_start();
$USER = isset($_SESSION['USER'])?$_SESSION['USER']:null;
isAuth($USER);

$mysqli = new mysqli("localhost", "root", "root", "DOINGSDONE");
if ($mysqli->connect_error) {
    die('Ошибка подключения ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
}


//Показывать выполненные задачи
if (isset($_GET['show_completed'])) {
    if ($_GET['show_completed'] === '1') {
        $_SESSION['SHOW_COMPLETED_TASKS'] = true;
    } elseif ($_GET['show_completed'] === '0') {
        unset($_SESSION['SHOW_COMPLETED_TASKS']);
    }
}
//Отметить задачу выполненной/невыполненной
if (isset($_GET['task_id']) && isset($_GET['check'])) {
    $task_id = intval($_GET['task_id']);
    $check = intval($_GET['check']);
    if ($check === 1 || $check === 0) {
        $check_task_query = "UPDATE tasks
        SET tasks.status = $check
        WHERE 
        tasks.id = $task_id AND
        tasks.author_id = '".$USER['id']."'";
        if (!$result = $mysqli->query($check_task_query)) {
            die('Ошибка в запросе '.$check_task_query.' - '.$mysqli->error);
        }
    }

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
$menu_items = [];
while ($res = $result->fetch_assoc()) {
    $menu_items[] = $res;
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
    $current_tasks_list_query = "SELECT
        tasks.id AS ID,
        tasks.name AS TASK_NAME,
        tasks.deadline_datetime AS TASK_DEADLINE,
        tasks.status AS TASK_STATUS,
        projects.name AS PROJECT_NAME
        FROM tasks 
        JOIN projects
        ON tasks.project_id = projects.id
        WHERE
        tasks.author_id = '".$USER['id']."'";
    if (!$result = $mysqli->query($current_tasks_list_query)) {
        die('Ошибка в запросе '.$current_tasks_list_query.' - '.$mysqli->error);
    }
    while ($res = $result->fetch_assoc()) {
        $current_tasks_items[] = $res;
    }
}
if ($selected_menu_isset) {
    //Запрашиваем задачи пользователя по его ID и ID проекта для вывода задач
    $current_tasks_list_query = "SELECT
        tasks.id AS ID,
        tasks.name AS TASK_NAME,
        tasks.deadline_datetime AS TASK_DEADLINE,
        tasks.status AS TASK_STATUS,
        projects.name AS PROJECT_NAME
        FROM tasks 
        JOIN projects
        ON tasks.project_id = projects.id
        WHERE
        tasks.project_id = $selected_menu_item_id AND
        tasks.author_id = '".$USER['id']."'";
    if (!$result = $mysqli->query($current_tasks_list_query)) {
        die('Ошибка в запросе '.$current_tasks_list_query.' - '.$mysqli->error);
    }
    while ($res = $result->fetch_assoc()) {
        $current_tasks_items[] = $res;
    }
}


$mysqli->close();


// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);


$content = include_template('index.php', [
    'current_tasks_items' => $current_tasks_items,
    'show_completed_tasks' => $_SESSION['SHOW_COMPLETED_TASKS']??NULL
]);

echo include_template('layout.php', [
    'menu_items' => $menu_items,
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
