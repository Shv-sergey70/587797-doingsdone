<?php
namespace Doingsdone;

class MySQL {
    protected $host;
    protected $username;
    protected $password;
    protected $dbname;
    protected $connection;
    public function __construct($host, $username, $password, $dbname) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
    }
    public function getConnection()
    {
        if (!empty($this->connection)) {
            return $this->connection;
        } else {
            $mysqli = new \mysqli($this->host, $this->username, $this->password, $this->dbname);
            if ($mysqli->connect_error) {
                die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
            }
            $this->connection = $mysqli;
            return $mysqli;
        }
    }

    public function makeQuery($query)
    {
        if (!$result = $this->getConnection()->query($query)) {
            die('Ошибка в запросе '.$query.' - '.$this->getConnection()->error);
        }
        return $result;
    }

    public function getAssocResult($data)
    {
        $arResult = [];
        while ($res = $data->fetch_assoc()) {
            $arResult[] = $res;
        }
        return $arResult;
    }
}
