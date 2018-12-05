DROP DATABASE IF EXISTS DOINGSDONE;
CREATE DATABASE DOINGSDONE DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE DOINGSDONE;

CREATE TABLE projects (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(255),
author_id INT
);

CREATE TABLE tasks (
id INT AUTO_INCREMENT PRIMARY KEY,
name TEXT,
status TINYINT DEFAULT 0,
create_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
finish_datetime DATETIME,
deadline_datetime DATETIME,
file_url VARCHAR(255),
author_id INT,
project_id INT
);

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
register_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
email VARCHAR(100),
name CHAR(150),
password CHAR(150),
contacts TEXT,
UNIQUE(`email`)
);

CREATE INDEX deadline_datetime ON tasks(deadline_datetime);
CREATE INDEX author_id ON tasks(author_id);
CREATE INDEX project_id ON tasks(project_id);

CREATE INDEX author_id ON projects(author_id);
