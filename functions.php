<?php

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require($name);

    $result = ob_get_clean();

    return $result;
}

function countTasksInProject($arTasks, $project_name) {
    $tasks_num = 0;
    foreach ($arTasks as $value) {
        if ($value['category_name'] === $project_name) {
            $tasks_num++;
        }
    }
    return $tasks_num;
}
