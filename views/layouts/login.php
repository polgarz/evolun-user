<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use lajax\languagepicker\widgets\LanguagePicker;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition login-page">
    <?php $this->beginBody() ?>
        <div class="wrapper">
            <header class="main-header">
                <!-- Header Navbar -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Navbar Right Menu -->
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <?= LanguagePicker::widget([
                                'itemTemplate' => '<li><a href="{link}" title="{language}"><i class="{language}"></i> {name}</a></li>',
                                'activeItemTemplate' => '<a href="#" class="dropdown-toggle" title="{language}" data-toggle="dropdown" role="button" aria-expanded="false">{name}</a>',
                                'parentTemplate' => '<li class="dropdown">{activeItem}<ul class="dropdown-menu" role="menu">{items}</ul></li>',
                                'encodeLabels' => false,
                            ]) ?>
                        </ul>
                    </div>
                </nav>
            </header>

            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
