<?php
use yii\helpers\Url;
?>
<h1>Üdv az önkéntes felületen! :)</h1>
<p>
    A felhasználói fiókod elkészült, a felületet az alábbi linken éred el:<br>
    <?=Url::base(true) ?><br>
</p>
<p>
    A belépési adatait a következők:<br>
    <b>Email cím: </b> <?=$user->email ?><br>
    <b>Jelszó: </b> <?=$user->passwordRepeat ?>
</p>