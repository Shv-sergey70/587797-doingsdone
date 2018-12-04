<?php
require_once('functions.php');
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$menu_items = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks_items = [
    [
        'name' => 'Собеседование в IT компании',
        'finish_date' => '3.12.2018',
        'category_name' => 'Работа',
        'isDone' => 'Нет'
    ],
    [
        'name' => 'Выполнить тестовое задание',
        'finish_date' => '25.12.2018',
        'category_name' => 'Работа',
        'isDone' => 'Нет'
    ],
    [
        'name' => 'Сделать задание первого раздела',
        'finish_date' => '21.12.2018',
        'category_name' => 'Учеба',
        'isDone' => 'Да'
    ],
    [
        'name' => 'Встреча с другом',
        'finish_date' => '22.12.2018',
        'category_name' => 'Входящие',
        'isDone' => 'Нет'
    ],
    [
        'name' => 'Купить корм для кота',
        'finish_date' => 'Нет',
        'category_name' => 'Домашние дела',
        'isDone' => 'Нет'
    ],
    [
        'name' => 'Заказать пиццу',
        'finish_date' => 'Нет',
        'category_name' => 'Домашние дела',
        'isDone' => 'Нет'
    ]
];

$content = include_template('index.php', [
    'tasks_items' => $tasks_items,
    'menu_items' => $menu_items,
    'show_complete_tasks' => $show_complete_tasks
]);

echo include_template('layout.php', [
    'tasks_items' => $tasks_items,
    'menu_items' => $menu_items,
    'title' => 'Дела в порядке',
    'content' => $content
]);
