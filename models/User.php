<?php

namespace evolun\user\models;

use Yii;
use yii\web\IdentityInterface;
use yii\helpers\FileHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use evolun\user\Module;

class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * A jelszó ismétlés ellenőrzésre szolgáló property
     * @var string
     */
    public $passwordRepeat;

    /**
     * Küldjünk-e levelet felvétel után?
     * @var boolean
     */
    public $sendEmailAfterCreate = 1;

    /**
     * Jogosultsági szint
     * @var string
     */
    private $role;

    /**
     * Letrehozasi scenario
     */
    const SCENARIO_CREATE  = 'create';

    /**
     * Adatmodositasi scenario
     */
    const SCENARIO_UPDATE  = 'update';

    /**
     * Profil szerkesztes
     */
    const SCENARIO_PROFILE = 'profile';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ['class' => BlameableBehavior::className()],
            ['class' => TimestampBehavior::className(), 'value' => new \yii\db\Expression('NOW()')],
            [
                'class' => \mohorev\file\UploadImageBehavior::class,
                'attribute' => 'image',
                'scenarios' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_PROFILE],
                'placeholder' => '@vendor/polgarz/evolun-user/assets/profile-placeholder.png',
                'path' => Yii::$app->params['uploadBasePath'] . '/images/user/{id}',
                'url' => Yii::$app->params['uploadBaseUrl'] . '/images/user/{id}',
                'thumbs' => [
                    'xs' => ['width' => 25, 'height' => 25, 'mode' => \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND],
                    's'  => ['width' => 100, 'height' => 100, 'mode' => \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE]  = $scenarios['default'];
        $scenarios[self::SCENARIO_UPDATE]  = $scenarios['default'];
        $scenarios[self::SCENARIO_PROFILE] = $scenarios['default'];
        unset($scenarios[self::SCENARIO_PROFILE]['role']);

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'nickname', 'phone'], 'required'],
            [['role'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['email'], 'unique'],
            ['password', 'string', 'min' => 6],
            [['passwordRepeat'], 'required', 'when' => function($model) {
                return !empty($model->password);
            }, 'whenClient' => "function (attribute, value) {
                return $('#user-password').val().length > 0;
            }"],
            ['image', 'image', 'skipOnEmpty' => true, 'extensions' => 'jpg, jpeg, gif, png', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_PROFILE]],
            [['inactive'], 'boolean'],
            [['password'], 'required', 'on' => self::SCENARIO_CREATE],
            [['email'], 'email'],
            [['facebook'], 'url'],
            [['member_since', 'workgroupList', 'birth_date', 'created_at', 'updated_at', 'role', 'last_activity'], 'safe'],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password', 'message' => 'A két jelszónak egyeznie kell!'],
            [['driving_license', 'created_by', 'updated_by', 'sendEmailAfterCreate'], 'integer'],
            [['name', 'nickname', 'email', 'password', 'phone', 'skype', 'address', 'auth_key', 'facebook'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('user', 'Full name'),
            'nickname' => Yii::t('user', 'Nickname'),
            'email' => Yii::t('user', 'Email address'),
            'password' => Yii::t('user', 'Password'),
            'passwordRepeat' => Yii::t('user', 'Password (repeat)'),
            'phone' => Yii::t('user', 'Phone number'),
            'member_since' => Yii::t('user', 'Join date'),
            'skype' => Yii::t('user', 'Skype'),
            'facebook' => Yii::t('user', 'Facebook'),
            'address' => Yii::t('user', 'Address'),
            'driving_license' => Yii::t('user', 'Driving license'),
            'birth_date' => Yii::t('user', 'Birth date'),
            'created_at' => Yii::t('user', 'Created at'),
            'updated_at' => Yii::t('user', 'Updated at'),
            'created_by' => Yii::t('user', 'Created by'),
            'updated_by' => Yii::t('user', 'Updated by'),
            'last_activity' => Yii::t('user', 'Last activity'),
            'createdByName' => Yii::t('user', 'Created by'),
            'updatedByName' => Yii::t('user', 'Updated by'),
            'role' => Yii::t('user', 'Role'),
            'sendEmailAfterCreate' => Yii::t('user', 'Send email after create'),
            'image' => Yii::t('user', 'Profile image'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // ha insert van, akkor a jelszót elkódoljuk, és generálunk random auth keyt
            if ($insert) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
                $this->password = md5($this->password);
            } else {
                // ha adatmósoítás van, és a jelszó nem üres
                if (!empty($this->password)) {
                    // elkódoljuk a jelszót
                    $this->password = md5($this->password);
                    $this->auth_key = \Yii::$app->security->generateRandomString();
                } else {
                    // ha üres a jelszó, akkor visszaállítjuk az eredetit!
                    $this->password = $this->oldAttributes['password'];
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // ha új felvétel van, és kell emailt küldeni, emailt küldünk
        if ($insert && $this->sendEmailAfterCreate) {
            Yii::$app->mailer->compose(Yii::$app->controller->module->registerEmail, ['user' => $this])
                ->setFrom(Yii::$app->params['mainEmail'])
                ->setTo($this->email)
                ->setSubject(Yii::t('user', 'Welcome!'))
                ->send();
        }
    }

    /**
     * Visszaadja azt a felhasználót, aki létrehozta ezt a rekordot
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(self::className(), ['created_by' => 'id']);
    }

    /**
     * Visszaadja azt a felhasználót, aki utoljára módosította ezt a rekordot
     * @return ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(self::className(), ['updated_by' => 'id']);
    }

    /**
     * Visszaadja annak a felhasználónak a nevét, aki létrehozta ezt a rekordot
     * @return string
     */
    public function getCreatedByName()
    {
        if ($this->createdBy) {
            return $this->createdBy->name;
        }
    }

    /**
     * Visszaadja annak a felhasználónak a nevét, aki utoljára módosította ezt a rekordot
     * @return string
     */
    public function getUpdatedByName()
    {
        if ($this->updatedBy) {
            return $this->updatedBy->name;
        }
    }

    /**
     * Megkeres egy felhasználót az ID-ja alapján
     * @param string|int $id
     * @return IdentityInterface|null
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Megkeres egy felhasználót az auth_key alapján
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * Visszaadja az aktuális user id-ját
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Visszaadja az aktuális user auth kulcsát
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Ellenőrzi, hogy a tárolt auth_key egyezik-e a paraméterben kapottal
     * @param string $authKey
     * @return boolean
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Ellenőrzi, hogy a tárolt jelszó egyezik-e a paraméterben kapottal
     * @param string $authKey
     * @return boolean
     */
    public function validatePassword($password)
    {
        return $this->password === md5($password);
    }

    /**
     * Visszaadja, hogy a felhasználó mióta tag relatív időben (pl. 1 éve, 2 hónapja, 3 napja)
     * @param bool $round Pontosan kell-e az idő, vagy elég csak az első elem (pl.: 1 éve)
     * @return string
     */
    public function getRelativeMemberSince($round = false)
    {
        $member_since = new \DateTime($this->member_since);

        // ez csak egy nagyon egyszeru vizsgalat, ha valaki nagyon rossz evszamot allitana be
        if ($member_since->format('Y') < 1800) {
            return null;
        }

        $now = new \DateTime();
        $diff = $member_since->diff($now);
        $relative = [];

        if ($diff->y > 0) {
            if ($diff->y === 1) {
                $relative[] = $diff->y . ' ' . Yii::t('user', 'year');
            } else {
                $relative[] = $diff->y . ' ' . Yii::t('user', 'years');
            }
        }

        if ($diff->m > 0) {
            if ($diff->m === 1) {
                $relative[] = $diff->m . ' ' . Yii::t('user', 'month');
            } else {
                $relative[] = $diff->m . ' ' . Yii::t('user', 'months');
            }
        }

        if ($diff->d > 0) {
            if ($diff->d === 1) {
                $relative[] = $diff->d . ' ' . Yii::t('user', 'day');
            } else {
                $relative[] = $diff->d . ' ' . Yii::t('user', 'days');
            }
        }

        if (count($relative) > 1 && !$round) {
            if (count($relative) == 2) {
                return $relative[0] . ' ' . Yii::t('user', 'and') . ' ' . $relative[1];
            } else {
                return $relative[0] . ' ' . $relative[1] . ', ' . Yii::t('user', 'and') . ' ' . $relative[2];
            }
        } else if (count($relative)) {
            return $relative[0];
        }
    }

    /**
     * Visszaadja a felhasználó aktuális jogosultsági szintjét
     * @return string
     */
    public function getRole()
    {
        if (!isset($this->role)) {
            if (!$this->isNewRecord) {
                $auth = \Yii::$app->authManager;
                $roles = $auth->getRolesByUser($this->id);

                if (!empty($roles)) {
                    return current($roles)->name;
                }
            }
        }

        return $this->role;
    }

    /**
     * Beállítja a $role propertyt
     * @param void
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
}
