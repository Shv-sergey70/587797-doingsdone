CREATE DATABASE DOINGSDONE;

CREATE TABLE PROJECT (
id INT AUTO_INCREMENT PRIMARY_KEY,
name CHAR,
author INT
);

CREATE TABLE TASK (
create_datetime DATETIME,
finish_datetime DATETIME,
STATUS TINYINT DEFAUL 0,
NAME TEXT,
FILE_URL CHAR,
deadline_datetime DATETIME,
author_id INT,
project_id INT
);

CREATE TABLE USER (
register_datetime DATETIME,
email CHAR,
name CHAR,
password CHAR,
contacts CHAR
);
