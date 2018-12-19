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
        if (empty($this->menu_items)) {
            $all_items_query = "SELECT
            projects.id AS ID,
            projects.name AS NAME,
            COUNT(tasks.id) AS TASKS_COUNT
            FROM projects
            LEFT JOIN tasks
            ON projects.id = tasks.project_id
            WHERE projects.author_id = '$user_id'
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
}
