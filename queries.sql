USE DOINGSDONE;

INSERT INTO projects SET id = 1, name = 'Входящие', author_id = 1;
INSERT INTO projects SET id = 2, name = 'Учёба', author_id = 2;
INSERT INTO projects SET id = 3, name = 'Работа', author_id = 2;
INSERT INTO projects SET id = 4, name = 'Домашние дела', author_id = 1;
INSERT INTO projects SET id = 5, name = 'Авто', author_id = 3;

INSERT INTO tasks(id, name, status, finish_datetime, deadline_datetime, file_url, author_id, project_id) VALUES
(1, 'Собеседование в IT компании', 0, NULL, '2018-12-03 00:00:00', NULL, 1, 3),
(2, 'Выполнить тестовое задание', 0, NULL, '2018-12-25 00:00:00', NULL, 1, 3),
(3, 'Сделать задание первого раздела', 1, '2018-12-01 14:52:23', '2018-12-21 00:00:00', NULL, 2, 2),
(4, 'Встреча с другом', 0, NULL, '2018-12-22 00:00:00', NULL, 3, 1),
(5, 'Купить корм для кота', 0, NULL, '2018-12-10 00:00:00', NULL, 1, 4),
(6, 'Заказать пиццу', 0, NULL, '2018-12-06 00:00:00', NULL, 1, 4);

INSERT INTO users(id, email, name, password, contacts) VALUES
(1, 'shv.sergey70@gmail.com', 'Сергей', '125125', 'Грибоедова 89'),
(2, 'gavgav@mail.ru', 'Николай', '12345', 'Невский проспект 120'),
(3, 'myamya@mail.ru', 'Анастасия', '1234567', 'Гороховая 24');

-- Получить список из всех проектов для одного пользователя;
-- SELECT * FROM projects WHERE author_id = 1;
-- Получить список из всех задач для одного проекта;
-- SELECT * FROM tasks WHERE project_id = 1
-- Пометить задачу как выполненную;
-- UPDATE tasks SET status = 1 WHERE id = 1
-- Получить все задачи для завтрашнего дня;
-- SELECT * FROM tasks WHERE deadline_datetime > NOW() AND deadline_datetime <= NOW()+ INTERVAL 1 DAY
-- Обновить название задачи по её идентификатору;
-- UPDATE tasks SET name = 'Обновленное название' WHERE id = 1
