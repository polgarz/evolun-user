<?php
use yii\helpers\Url;
?>
<h1>Új jelszó igénylése</h1>
<p>
    Szia!<br>
    Ezt a levelet azért kaptad, mert te - vagy valaki a nevedben - új jelszót igényelt az önkéntes rendszerben. Ha nem te voltál az, ezt a levelet hagyd figyelmen kívül. Ha te voltál, és szeretnél új jelszót megadni, kattints ide:<br>
    <?= Url::to(['/user/default/reset-password', 'token' => $user->auth_key], true) ?><br>
</p>