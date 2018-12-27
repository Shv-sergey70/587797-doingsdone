<?php
declare(strict_types=1);
/**
 * Функци-шаблонизатор
 * @param string $name
 * @param array $data
 * @return false|string
 */
function include_template(string $name, array $data) {
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

/**
 * Определяет, аутентифицирован ли пользователь
 * @param array|null $user
 */
function isAuth(?array $user): void {
    if (!$user) {
        header('Location: /guest.php');
        die();
    }
}
