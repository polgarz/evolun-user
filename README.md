User module for Evolun
=======

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist polgarz/evolun-user "@dev"
```

or add

```
"polgarz/evolun-user": "@dev"
```

to the require section of your `composer.json` file.

Migration
-----
```
php yii migrate/up --migrationPath=@vendor/polgarz/evolun-user/migrations
```

Configuration
-----

```php
'components' => [
    ...
    'user' => [
        'identityClass' => 'evolun\user\models\User',
        'enableAutoLogin' => true,
        'loginUrl' => ['user/default/login'],
    ],
]
```
```php
'modules' => [
    'user' => [
        'class' => 'evolun\user\Module',
        'modules' => [
            'profile' => [
                'class' => 'evolun\user\modules\profile\Module',
            ],
            'event' => [
                'class' => 'evolun\user\modules\event\Module',
            ],
        ]
    ],
],
```

Available submodules (tabs)
-----
- Profile
- Event (you need to install the [evolun-event](https://github.com/polgarz/evolun-event) extension to use this submodule)
