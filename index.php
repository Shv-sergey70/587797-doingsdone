<?php
use Doingsdone\MySQL as MySQL;
use Doingsdone\Tasks as Tasks;

require_once('vendor/autoload.php');
require_once('functions.php');
date_default_timezone_set('Europe/Moscow');
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
    } else {
        unset($_SESSION['SHOW_COMPLETED_TASKS']);
    }
}
//Отметить задачу выполненной/невыполненной
if (isset($_GET['task_id'], $_GET['check'])) {
    $task_id = intval($_GET['task_id']);
    $check = intval($_GET['check']);
    if ($check === 1 || $check === 0) {
        $check_task_query = "UPDATE tasks
        SET tasks.status = $check
        WHERE 
        tasks.id = $task_id AND
        tasks.author_id = ".$USER['id'];
        $mysql->makeQuery($check_task_query);
    }

}
//Запрашиваем проекты и задачи пользователя по его ID - меню
$menu_items = $tasks->getMenu($USER['id']);


$tasks_list_query = "SELECT
        tasks.id AS ID,
        tasks.name AS TASK_NAME,
        tasks.deadline_datetime AS TASK_DEADLINE,
        tasks.status AS TASK_STATUS,
        tasks.file_url AS FILE_SRC,
        projects.name AS PROJECT_NAME
        FROM tasks 
        JOIN projects
        ON tasks.project_id = projects.id
        WHERE
        tasks.author_id = ".$USER['id'];


//Проверяем на существование выбранный проект (пункт меню)
if (!empty($_GET['project_id'])) {
    unset($_SESSION['selected_menu_item_id']);
    $selected_menu_item_id = intval($_GET['project_id']);
    foreach ($menu_items as $menu_item) {
        if ((int)$menu_item['ID'] === $selected_menu_item_id) {
            $_SESSION['selected_menu_item_id'] = $selected_menu_item_id;
        }
    }
    if (!$_SESSION['selected_menu_item_id']) {
        header("HTTP/1.x 404 Not Found");
        die();
    }
}
if (!empty($_GET['show_all'])) {
    unset($_SESSION['selected_menu_item_id']);
}
if (isset($_SESSION['selected_menu_item_id'])) {
    $tasks_list_query .= ' AND tasks.project_id = '.$_SESSION['selected_menu_item_id'];
}


//Фильтр задач по срокам
if (isset($_GET['time'])) {
    $time_mode = strval($_GET['time']);
    if ($time_mode === 'all') {
        unset($_SESSION['task_time_filter']);
    } else {
        $_SESSION['task_time_filter'] = $time_mode;
    }
}
if (isset($_SESSION['task_time_filter'])) {
    $now = date('Y-m-d H:i:s', time());
    $midnight = date('Y-m-d H:i:s', strtotime('+1 day 00:00:00'));
    $tomorrow_midnight = date('Y-m-d H:i:s', strtotime('+2 day 00:00:00'));
    if ($_SESSION['task_time_filter'] === 'today') {
        $tasks_list_query .= " AND tasks.deadline_datetime >= '$now' 
        AND tasks.deadline_datetime <= '$midnight'";
    } elseif ($_SESSION['task_time_filter'] === 'tomorrow') {
        $tasks_list_query .= " AND tasks.deadline_datetime >= '$midnight' 
        AND tasks.deadline_datetime <= '$tomorrow_midnight'";
    } elseif ($_SESSION['task_time_filter'] === 'expired') {
        $tasks_list_query .= " AND tasks.deadline_datetime < '$now'";
    } else {
        unset($_SESSION['task_time_filter']);
    }
}
if (isset($_GET['search'])) {
    $search_error = '';
    $search = trim($_GET['search']);
    if (empty($search)) {
        $search_error = 'Строка поиска не должна быть пустой';
    } else {
        $safe_search = $mysqli->real_escape_string($search);
        $tasks_list_query .= " AND MATCH(tasks.name) AGAINST('*".$safe_search."*' IN BOOLEAN MODE)";
    }
}
$current_tasks_items = [];
$current_tasks_items = $mysql->getAssocResult($mysql->makeQuery($tasks_list_query));
$mysqli->close();

$content = include_template('index.php', [
    'current_tasks_items' => $current_tasks_items,
    'show_completed_tasks' => $_SESSION['SHOW_COMPLETED_TASKS']??NULL,
    'task_time_filter' => $_SESSION['task_time_filter']??NULL,
    'search_error' => $search_error??NULL
]);

echo include_template('layout.php', [
    'all_tasks_count' => $tasks->countAllTasks($USER['id']),
    'menu_items' => $menu_items,
    'selected_menu_id' => $_SESSION['selected_menu_item_id']??NULL,
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
