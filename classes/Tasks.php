<?php
namespace Doingsdone;

/**
 * Created by PhpStorm.
 * User: shv.sergey
 * Date: 17.12.2018
 * Time: 12:49
 */

class Tasks
{
    protected $DBconnection;
    public function __construct(MySQL $connection)
    {
        $this->DBconnection = $connection;
    }
    public function getMenu($user_id)
    {
        $all_items_query = "SELECT
        projects.id AS ID,
        projects.name AS NAME,
        COUNT(tasks.id) AS TASKS_COUNT
        FROM projects
        LEFT JOIN tasks
        ON projects.id = tasks.project_id
        WHERE projects.author_id = '$user_id'
        GROUP BY projects.id";
        return $this->DBconnection->getAssocResult($this->DBconnection->makeQuery($all_items_query));
    }
}
