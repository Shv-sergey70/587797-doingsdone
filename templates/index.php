<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="" method="GET">
    <input class="search-form__input" type="text" name="search" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>
<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="?time=all" class="tasks-switch__item composer require respect/validation<?=(!isset($task_time_filter))?'tasks-switch__item--active':''?>">Все задачи</a>
        <a href="?time=today" class="tasks-switch__item <?=($task_time_filter === 'today')?'tasks-switch__item--active':''?>">Повестка дня</a>
        <a href="?time=tomorrow" class="tasks-switch__item <?=($task_time_filter === 'tomorrow')?'tasks-switch__item--active':''?>">Завтра</a>
        <a href="?time=expired" class="tasks-switch__item <?=($task_time_filter === 'expired')?'tasks-switch__item--active':''?>">Просроченные</a>
    </nav>

    <label class="checkbox">
        <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?=isset($show_completed_tasks)?'checked':''?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>
<?php if(!empty($search_error)):?>
    <div><?=$search_error?></div>
<?php else:?>
    <table class="tasks">
        <?php foreach ($current_tasks_items as $value):?>
            <?php if (!$value['TASK_STATUS'] || (!!$value['TASK_STATUS'] && isset($show_completed_tasks))):?>
                <tr class="tasks__item task <?=(!!$value['TASK_STATUS'] && isset($show_completed_tasks))?'task--completed':''?> <?=isImportantTask($value['TASK_DEADLINE']??'')?'task--important':''?>">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="<?=$value['ID']?>" <?=(!!$value['TASK_STATUS'] && isset($show_completed_tasks))?'checked':""?>>
                            <span class="checkbox__text"><?=htmlspecialchars($value['TASK_NAME'], ENT_QUOTES)?></span>
                        </label>
                    </td>
                    <?php if(!empty($value['FILE_SRC'])):?>
                    <td class="task__file">
                        <a class="download-link" href="<?=$value['FILE_SRC']?>">Прикрепленный файл</a>
                    </td>
                    <?php endif;?>

                    <td class="task__date"><?=htmlspecialchars($value['TASK_DEADLINE']??'', ENT_QUOTES)?></td>
                </tr>
            <?php endif;?>
        <?php endforeach;?>
    </table>
<?php endif;?>
