<?php
require_once('functions.php');
spl_autoload_register('classes_autoloader');

session_start();
$USER = isset($_SESSION['USER'])?$_SESSION['USER']:null;
isAuth($USER);

$mysql = new MySQL("localhost", "root", "root", "DOINGSDONE");
$mysqli = $mysql->getConnection();
$tasks = new Tasks($mysql);


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
        $mysql->makeQuery($check_task_query);
    }

}
//Запрашиваем проекты и задачи пользователя по его ID - меню
$menu_items = $tasks->getMenu($USER['id']);

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
    $current_tasks_items = $mysql->getAssocResult($mysql->makeQuery($current_tasks_list_query));
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
    $current_tasks_items = $mysql->getAssocResult($mysql->makeQuery($current_tasks_list_query));
}


$mysqli->close();

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
