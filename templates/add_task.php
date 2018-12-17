<h2 class="content__main-heading">Добавление задачи</h2>

<form class="form"  action="" method="post" enctype="multipart/form-data">
    <div class="form__row">
        <label class="form__label" for="name">Название <sup>*</sup></label>

        <input class="form__input <?=!empty($errors['name'])?'form__input--error':''?>" type="text" name="name" id="name" value="<?=htmlspecialchars($_POST['name']??'', ENT_QUOTES)?>" placeholder="Введите название">
        <p class="form__message">
            <span class="form__message error-message"><?=!empty($errors['name'])?'Это поле нужно заполнить':''?></span>
        </p>
    </div>

    <div class="form__row">
        <label class="form__label" for="project">Проект <sup>*</sup></label>

        <select class="form__input form__input--select <?=!empty($errors['project'])?'form__input--error':''?>" name="project" id="project">
            <?php foreach ($projects_categories as $project_category):?>
                <option value="<?=$project_category['ID']?>" <?=isset($_POST['project']) && $_POST['project'] === $project_category['ID'] || ($selected_menu_id === (int)$project_category['ID'])?'selected':''?>><?=$project_category['NAME']?></option>
            <?php endforeach;?>
        </select>
        <p class="form__message">
            <span class="form__message error-message"><?=!empty($errors['project'])?'Выберите проект из списка':''?></span>
        </p>
    </div>

    <div class="form__row">
        <label class="form__label" for="date">Дата выполнения</label>

        <input class="form__input form__input--date <?=!empty($errors['date'])?'form__input--error':''?>" type="date" name="date" id="date" value="<?=htmlspecialchars($_POST['date']??'', ENT_QUOTES)?>" placeholder="Введите дату в формате ГГГГ.ММ.ДД ЧЧ:СС">
        <p class="form__message">
            <span class="form__message error-message"><?=!empty($errors['date'])?'Введите дату в формате гггг.мм.дд чч:мм':''?></span>
        </p>
    </div>

    <div class="form__row">
        <label class="form__label" for="preview">Файл</label>

        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="preview" id="preview" value="">

            <label class="button button--transparent" for="preview">
                <span>Выберите файл</span>
            </label>
        </div>
    </div>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Добавить">
    </div>
    <?=isset($errors)?'Пожалуйста, исправьте ошибки в форме':''?>
</form>
