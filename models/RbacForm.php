<?php

namespace evolun\user\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Ez a jogosultságok modelje
 */
class RbacForm extends Model
{
    /**
     * A jogkör neve
     * @var string
     */
    public $name;

    /**
     * A jogkör szülője
     * @var string
     */
    public $parent;

    /**
     * A jogkör leírása (beszédes neve)
     * @var string
     */
    public $description;

    /**
     * A jogkör alá tartozó permission-ök
     * @var [string]
     */
    public $permissions;

    /**
     * Az authmanager által visszaadott Item modelt
     * @var Item
     */
    private $_item;

    /**
     * Feltölti a modelt az item adataival
     * @param Item $item
     * @param array $config
     */
    public function __construct($item = null, $config = [])
    {
        if ($item !== null) {
            $this->_item = $item;
            $this->name = $this->_item->name;
            $this->description = $this->_item->description;
            $this->permissions = array_keys($this->getManager()->getChildren($this->name));
            $this->parent = (new \yii\db\Query)->select('parent')->from($this->getManager()->itemChildTable)->where(['child' => $this->name])->scalar();
        }

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['name'], 'match', 'pattern' => '/^[a-zA-Z]+$/', 'message' => Yii::t('user/rbac', 'Invalid format! You have to use alphabet letters only, without space')],
            [['name'], 'uniqueName', 'when' => function($model) {
                return $model->_item === null;
            }],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['permissions', 'parent'], 'safe'],
        ];
    }

    /**
     * Az egyedi név custom validátora
     * @param  string $attribute Az attribútum neve
     * @param  array $params     A validátor paraméterei
     * @return void
     */
    public function uniqueName($attribute, $params)
    {
        if ($this->getManager()->getRole($this->$attribute) !== null) {
            $this->addError($attribute, Yii::t('user/rbac', 'There is already a role with this name, please choose another one'));
        }
    }

    /**
     * Beszúrja, vagy módosítja az itemet
     * @return boolean sikeres volt-e a mentés?
     */
    public function save()
    {
        if ($this->validate()) {
            // ha új jogkörről van szó
            if ($this->_item === null) {
                $this->_item = $this->getManager()->createRole($this->name);
                $this->_item->name = $this->name;
                $isNew = true;
            } else {
                $isNew = false;
            }

            // a leírást mindenképp szerkesztjük
            $this->_item->description = $this->description;

            // töröljük a már létező permissiönöket
            $permissions = $this->getManager()->getPermissionsByRole($this->_item->name);
            foreach($permissions as $permission) {
                $this->getManager()->removeChild($this->_item, $permission);
            }

            if ($isNew) {
                $this->getManager()->add($this->_item);

                // szülőt csak hozzáadásnál lehet megadni
                if ($this->parent) {
                    $this->getManager()->addChild($this->getManager()->getRole($this->parent), $this->_item);
                }
            } else {
                $this->getManager()->update($this->name, $this->_item);
            }

            // ha kaptunk permissionoket, hozzáadjuk az itemhez
            if (!empty($this->permissions)) {
                foreach($this->permissions as $permission) {
                    $permission = $this->getManager()->getPermission($permission);
                    $this->getManager()->addChild($this->_item, $permission);
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('user/rbac', 'Name'),
            'type' => Yii::t('user/rbac', 'Type'),
            'description' => Yii::t('user/rbac', 'Description'),
            'permissions' => Yii::t('user/rbac', 'Permissions'),
            'parent' => Yii::t('user/rbac', 'Parent role'),
        ];
    }

    /**
     * Visszaadja az authManager-t
     * @return AuthManager
     */
    private function getManager()
    {
        return Yii::$app->authManager;
    }
}
