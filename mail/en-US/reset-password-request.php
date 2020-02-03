<?php
use yii\helpers\Url;
?>
<h1>Password reset request</h1>
<p>
    Hello!<br>
    You received this email because you wanted to change your password. If it wasn't you, ignore this email, if you want to change your password, click the link below:
    <br>
    <?= Url::to(['/user/default/reset-password', 'token' => $user->auth_key], true) ?><br>
</p>