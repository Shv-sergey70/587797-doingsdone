<?php
use Doingsdone\MySQL as MySQL;


require_once('dbconn.php');
require_once('functions.php');
require_once('vendor/autoload.php');

$mysql = new MySQL($DB['host'], $DB['username'], $DB['password'], $DB['dbname']);
$mysqli = $mysql->getConnection();

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
$tasks = $mysql->getAssocResult($mysql->makeQuery($expiring_tasks_query));


// Create the Transport
$transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 2525))
    ->setUsername('e76b218a52bc88')
    ->setPassword('8aedeea47b167e');

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

foreach ($tasks as $task) {
    $to = [$task['USER_EMAIL'], 'shv.sergey70@gmail.com'];
//    echo "<pre>";
//    var_dump($task);
//    var_dump($to);
//    echo "</pre>";
    $body = "Уважаемый, ".$task['USER_NAME'].". У вас запланирована задача '".$task['TASK_NAME']."' на ".$task['DEADLINE'];
    // Create a message
    $message = (new Swift_Message())
        ->setSubject('Уведомление от сервиса «Дела в порядке»')
        ->setFrom(['doingsdone@info.com' => 'Doingsdone'])
        ->setTo($to)
        ->setBody($body);
    // Send the message
    $result = $mailer->send($message);
//    echo "<pre>";
//    var_dump($result);
//    echo "</pre>";
}
