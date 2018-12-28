<?php

$last_mail_send_datetime = trim(file_get_contents($_SERVER['DOCUMENT_ROOT']."/last_mail_send.txt"));


if (time() - strtotime($last_mail_send_datetime) > 3600) {
    // Записываем время последнего запуска скрипта
    file_put_contents($_SERVER['DOCUMENT_ROOT']."/last_mail_send.txt", date('d.m.Y H:i:s'));
    echo "<pre>";
    var_dump('Рассылка сработала!');
    echo "</pre>";
    $expiring_tasks_query = "SELECT
    tasks.name AS TASK_NAME,
    tasks.deadline_datetime AS DEADLINE,
    users.name AS USER_NAME,
    users.email AS USER_EMAIL
    FROM tasks
    JOIN users
    ON tasks.author_id = users.id
    where tasks.deadline_datetime > NOW()
    AND tasks.deadline_datetime <= DATE_ADD(NOW(), INTERVAL 1 HOUR)";
    $arTasks = $mysql->getAssocResult($mysql->makeQuery($expiring_tasks_query));


// Create the Transport
    $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 2525))
        ->setUsername('e76b218a52bc88')
        ->setPassword('8aedeea47b167e');

// Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

    foreach ($arTasks as $task) {
        $to = [$task['USER_EMAIL'], 'shv.sergey70@gmail.com'];
        $body = "Уважаемый, ".$task['USER_NAME'].". У вас запланирована задача '".$task['TASK_NAME']."' на ".$task['DEADLINE'];
        // Create a message
        $message = (new Swift_Message())
            ->setSubject('Уведомление от сервиса «Дела в порядке»')
            ->setFrom(['doingsdone@info.com' => 'Doingsdone'])
            ->setTo($to)
            ->setBody($body);
        // Send the message
        $mailer->send($message);
    }
}
