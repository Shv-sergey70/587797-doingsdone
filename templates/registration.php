<h2 class="content__main-heading">Регистрация аккаунта</h2>

<form class="form" action="" method="post">
    <div class="form__row">
        <label class="form__label" for="email">E-mail <sup>*</sup></label>

        <input class="form__input <?=$errors['email']?'form__input--error':''?>" type="text" name="email" id="email" value="<?=htmlspecialchars($_POST['email']??'', ENT_QUOTES)?>" placeholder="Введите e-mail">

        <?php if(!empty($errors['email'])):?>
            <p class="form__message">E-mail введён некорректно</p>
        <?php endif;?>
    </div>

    <div class="form__row">
        <label class="form__label" for="password">Пароль <sup>*</sup></label>

        <input class="form__input <?=$errors['password']?'form__input--error':''?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">
        <?php if(!empty($errors['password'])):?>
            <p class="form__message">Это поле нужно заполнить</p>
        <?php endif;?>
    </div>

    <div class="form__row">
        <label class="form__label" for="name">Имя <sup>*</sup></label>

        <input class="form__input <?=$errors['name']?'form__input--error':''?>" type="text" name="name" id="name" value="<?=htmlspecialchars($_POST['name']??'', ENT_QUOTES)?>" placeholder="Введите имя">
        <?php if(!empty($errors['name'])):?>
            <p class="form__message">Это поле нужно заполнить</p>
        <?php endif;?>
    </div>

    <div class="form__row form__row--controls">
        <?php if(isset($errors)):?>
            <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
            <?php foreach ($errors as $key => $error):?>
                <p class="error-message">[<?=$dict[$key]?>] - <?=$error?></p>
            <?php endforeach;?>
        <?php endif;?>

        <input class="button" type="submit" name="" value="Зарегистрироваться">
    </div>
</form>
