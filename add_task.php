<?php
use Doingsdone\MySQL as MySQL;
use Doingsdone\Tasks as Tasks;
use Respect\Validation\Validator as V;

require_once('dbconn.php');
require_once('functions.php');
require_once('vendor/autoload.php');

session_start();
$USER = isset($_SESSION['USER'])?$_SESSION['USER']:null;
isAuth($USER);

$mysql = new MySQL($DB['host'], $DB['username'], $DB['password'], $DB['dbname']);
$mysqli = $mysql->getConnection();
$tasks = new Tasks($mysql);


//Запрашиваем проекты и задачи пользователя по его ID - меню
$menu_items = $tasks->getMenu($USER['id']);

//Для формы добавления задачи
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task = $_POST;
    $requiredValidator = V::notEmpty();
    $required = ['name', 'project'];
    $dict = ['name' => 'Название', 'project' => 'Проект'];
    $errors = [];
    foreach ($required as $item) {
        if (!$requiredValidator->validate($task[$item])) {
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
    if (!V::optional(V::date( 'Y-m-d H:i'))->validate($task['date'])) {
        $errors['date'] = 'Введите дату в формате гггг.мм.дд чч:мм';
    }
    if (!count($errors)) {
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
            author_id = ".$USER['id'];
        if (!empty($task['date'])) {
            $insert_task_query .= ", deadline_datetime = '".$task['date']."'";
        }
        $mysql->makeQuery($insert_task_query);
        header('Location: /');
        die();
    }
}

$content = include_template('add_task.php', [
    'projects_categories' => $menu_items,
    'selected_menu_id' => $_SESSION['selected_menu_item_id']??NULL,
    'errors' => $errors??NULL
]);

$mysqli->close();
echo include_template('layout.php', [
    'all_tasks_count' => $tasks->countAllTasks($USER['id']),
    'menu_items' => $menu_items,
    'title' => 'Дела в порядке',
    'content' => $content,
    'user' => $USER
]);
