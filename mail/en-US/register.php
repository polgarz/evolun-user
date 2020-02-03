<?php
use yii\helpers\Url;
?>
<h1>Welcome! :)</h1>
<p>
    Your profile is ready, click the link below to sign in:
    <br>
    <?=Url::base(true) ?><br>
</p>
<p>
    Your login data:<br>
    <b>Email address: </b> <?=$user->email ?><br>
    <b>Password: </b> <?=$user->passwordRepeat ?>
</p>