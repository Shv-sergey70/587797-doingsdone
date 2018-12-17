<?php
require_once('functions.php');
spl_autoload_register('classes_autoloader');

session_start();
$USER = isset($_SESSION['USER'])?$_SESSION['USER']:null;
isAuth($USER);

$mysql = new MySQL("localhost", "root", "root", "DOINGSDONE");
$mysqli = $mysql->getConnection();
$tasks = new Tasks($mysql);

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
        if (empty($task['date'])) {
            $insert_task_query = "INSERT INTO tasks SET
            name = '".$task['name']."', 
            project_id = '".$task['project']."',
            file_url = '".$file_url."',
            author_id = '".$USER['id']."'";
        } else {
            $insert_task_query = "INSERT INTO tasks SET
            name = '".$task['name']."', 
            project_id = '".$task['project']."',
            file_url = '".$file_url."',
            author_id = '".$USER['id']."',
            deadline_datetime = '".$task['date']."'";
        }
        $mysql->makeQuery($insert_task_query);
        header('Location: /');
        die();
    }
} else {
    //Запрашиваем проекты и задачи пользователя по его ID - меню
    $menu_items = $tasks->getMenu($USER['id']);
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
