<?php
namespace Doingsdone;

/**
 * Class MySQL
 * @package Doingsdone
 */
class MySQL {
    protected $host;
    protected $username;
    protected $password;
    protected $dbname;
    protected $connection;

    /**
     * MySQL constructor.
     * @param $host
     * @param $username
     * @param $password
     * @param $dbname
     */
    public function __construct(string $host, string $username, string $password, string $dbname) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getDbname(): string
    {
        return $this->dbname;
    }

    /**
     * @return \mysqli
     */
    public function getConnection(): \mysqli
    {
        if (!empty($this->connection)) {
            return $this->connection;
        } else {
            $mysqli = new \mysqli($this->host, $this->username, $this->password, $this->dbname);
            if ($mysqli->connect_error) {
                die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
            }
            $this->connection = $mysqli;
            mysqli_set_charset($this->connection, "utf8");
            return $mysqli;
        }
    }

    /**
     * @param $query
     * @return bool|\mysqli_result
     */
    public function makeQuery(string $query)
    {
        if (!$result = $this->getConnection()->query($query)) {
            die('Ошибка в запросе '.$query.' - '.$this->getConnection()->error);
        }
        return $result;
    }

    /**
     * @param \mysqli_result
     * @return array
     */
    public function getAssocResult(\mysqli_result $data)
    {
        $arResult = [];
        while ($res = $data->fetch_assoc()) {
            $arResult[] = $res;
        }
        return $arResult;
    }
}
