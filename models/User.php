<?php

namespace evolun\user\models;

use Yii;
use yii\web\IdentityInterface;
use yii\helpers\FileHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

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
            'id' => 'ID',
            'name' => 'Teljes név',
            'nickname' => 'Becenév',
            'email' => 'Email cím',
            'password' => 'Jelszó',
            'passwordRepeat' => 'Jelszó (újra)',
            'phone' => 'Telefonszám',
            'member_since' => 'Csatlakozás ideje',
            'skype' => 'Skype',
            'facebook' => 'Facebook adatlap',
            'address' => 'Lakcím',
            'driving_license' => 'Jogositvány',
            'birth_date' => 'Születési dátum',
            'auth_key' => 'Auth Key',
            'created_at' => 'Létrehozva',
            'updated_at' => 'Utoljára módosítva',
            'created_by' => 'Létrehozta',
            'updated_by' => 'Utoljára módosította',
            'last_activity' => 'Utoljára aktív',
            'createdByName' => 'Létrehozta',
            'updatedByName' => 'Utoljára módosította',
            'role' => 'Jogosultsági szint',
            'sendEmailAfterCreate' => 'Email küldése felvétel után',
            'image' => 'Profilkép',
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
                ->setSubject('Üdv az önkénteseink között!')
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
            $relative[] = $diff->y . ' éve';
        }

        if ($diff->m > 0) {
            $relative[] = $diff->m . ' hónapja';
        }

        if ($diff->d > 0) {
            $relative[] = $diff->d . ' napja';
        }

        if (count($relative) > 1 && !$round) {
            if (count($relative) == 2) {
                return $relative[0] . ' és ' . $relative[1];
            } else {
                return $relative[0] . ' ' . $relative[1] . ', és ' . $relative[2];
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
