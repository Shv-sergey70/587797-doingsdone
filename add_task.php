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
    $task = $_POST;
    $required = ['name', 'project'];
    $dict = ['name' => 'Название', 'project' => 'Проект'];
    $errors = [];
    foreach ($required as $item) {
        if (empty($task[$item])) {
            $errors[$item] = 'Это поле нужно заполнить';
        }
    }
    if (!empty($task['project'])) {
        $project_from_list = false;
        foreach ($menu_items as $key => $menu_item) {
            if ($task['project'] === $menu_item['ID']) {
                $project_from_list = true;
            }
        }
        if (!$project_from_list) {
            $errors['project'] = 'Выберите проект из списка';
        }
    }
    if (!empty($task['date'])) {
        if (!DateTime::createFromFormat('Y-m-d H:i', $task['date'])) {
            $errors['date'] = 'Введите дату в формате гггг.мм.дд чч:мм';
        }
    } else {
        $task['date'] = 'NULL';
    }
    if (count($errors)) {
        $content = include_template('add_task.php', [
            'projects_categories' => $menu_items,
            'errors' => $errors
        ]);
    } else {
        $file_url = null;
        if (!empty($_FILES['preview']['name'])) {
            $tmp_name = $_FILES['preview']['tmp_name'];
            $original_name = $_FILES['preview']['name'];
            $filename_pieces = explode('.', $original_name);
            $file_extension = array_pop($filename_pieces);
            $new_name = uniqid('img_').'.'.$file_extension;
            $file_url = 'uploads/' . $new_name;
            move_uploaded_file($tmp_name, $file_url);
        }
        $insert_task_query = "INSERT INTO tasks SET
        name = '".$task['name']."', 
        project_id = '".$task['project']."',
        file_url = '".$file_url."',
        author_id = $user_id,
        deadline_datetime = ".$task['date'];
        if (!$result = $mysqli->query($insert_task_query)) {
            die('Ошибка в запросе '.$insert_task_query.' - '.$mysqli->error);
        }
        header('Location: /');
        die();
    }
} else {
    $content = include_template('add_task.php', [
        'projects_categories' => $menu_items
    ]);
}



$mysqli->close();
echo include_template('layout.php', [
    'menu_items' => $menu_items,
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
