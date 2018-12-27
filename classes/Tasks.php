<?php
namespace Doingsdone;


/**
 * Class Tasks
 * @package Doingsdone
 */
class Tasks
{
    protected $DBconnection;
    protected $menu_items;

    /**
     * Tasks constructor.
     * @param MySQL $connection
     */
    public function __construct(MySQL $connection)
    {
        $this->DBconnection = $connection;
    }

    /**
     * @param $user_id
     * @return array
     */
    public function getMenu($user_id)
    {
        if (!isset($this->menu_items)) {
            $all_items_query = "SELECT
            projects.id AS ID,
            projects.name AS NAME,
            COUNT(tasks.id) AS TASKS_COUNT
            FROM projects
            LEFT JOIN tasks
            ON projects.id = tasks.project_id
            WHERE projects.author_id = $user_id
            GROUP BY projects.id";
            $this->menu_items = $this->DBconnection->getAssocResult($this->DBconnection->makeQuery($all_items_query));
            return $this->menu_items;
        } else {
            return $this->menu_items;
        }
    }

    /**
     * @param $user_id
     * @return float|int
     */
    public function countAllTasks($user_id) {
        return array_sum(array_column($this->getMenu($user_id), 'TASKS_COUNT'));
    }
    /**
     * Определяем, осталось ли до конца задачи менее 24 часов
     * @param string $deadline
     * @return bool
     */
    public static function isImportantTask(string $deadline): bool {
        if (\DateTime::createFromFormat('d.m.Y', $deadline)) {
            $date_task_timestamp = new \DateTime($deadline, new \DateTimeZone('Europe/Moscow'));
            $date_now_timestamp = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
            $interval_timestamp = $date_task_timestamp->getTimestamp()-$date_now_timestamp->getTimestamp();
            $hours_stay = floor($interval_timestamp/3600);
            return $hours_stay <= 24;
        } else {
            return false;
        }
    }
}
