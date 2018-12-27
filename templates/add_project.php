<h2 class="content__main-heading">Добавление проекта</h2>

<form class="form"  action="" method="post">
    <div class="form__row">
        <label class="form__label" for="project_name">Название <sup>*</sup></label>

        <input class="form__input <?=!empty($errors['name'])?'form__input--error':''?>" type="text" name="name" id="project_name" value="<?=!empty($_POST['name'])?htmlspecialchars($_POST['name'], ENT_QUOTES):''?>" placeholder="Введите название проекта">
        <p class="form__message">
            <span class="form__message error-message"><?=!empty($errors['name'])?$errors['name']:''?></span>
        </p>
    </div>

    <div class="form__row form__row--controls">
        <?php if(!empty($errors)):?>
            <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
            <?php foreach ($errors as $key => $error):?>
                <p class="error-message">[<?=$dict[$key]?>] - <?=$error?></p>
            <?php endforeach;?>
        <?php endif;?>
        <input class="button" type="submit" name="" value="Добавить">
    </div>
</form>
