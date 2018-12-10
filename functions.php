<?php
declare(strict_types=1);
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

function isImportantTask(string $deadline): bool {
    if (DateTime::createFromFormat('d.m.Y', $deadline)) {
        $date_task_timestamp = new DateTime($deadline, new DateTimeZone('Europe/Moscow'));
        $date_now_timestamp = new DateTime('now', new DateTimeZone('Europe/Moscow'));
        $interval_timestamp = $date_task_timestamp->getTimestamp()-$date_now_timestamp->getTimestamp();
        $hours_stay = floor($interval_timestamp/3600);
        return $hours_stay <= 24;
    } else {
        return false;
    }
}

function isAuth(?array $user) {
    if (!$user) {
        header('Location: /guest.php');
        die();
    }
}
