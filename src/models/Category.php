<?php

namespace saghar\category\models;

use aminkt\widgets\alert\Alert;
use api\components\UrlMaker;
use saghar\category\interfaces\CategoryConstantsInterface;
use saghar\category\interfaces\CategoryInterfaces;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%categories}}".
 *
 * @property int $id
 * @property string $section
 * @property string $name
 * @property string $description
 * @property int $status
 * @property int $parent_id
 * @property int $depth
 *
 * @property Category[] $children
 * @property string $update_at
 * @property string $create_at
 *
 * @property Category $parent read-only
 * @property string $parentName
 */
class Category extends ActiveRecord implements CategoryInterfaces, CategoryConstantsInterface
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%categories}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_at', 'update_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_at'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status', 'parent_id', 'depth'], 'integer'],
            [['children'], 'safe'],
            [['section', 'name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
    }

    /**
     * Get one level down children of current category.
     *
     * @return $this
     */
    public function getChildren(){
        return $this->hasMany(self::class, ['parent_id' => 'id'])->where(['depth'=>$this->depth+1])->andWhere(['status'=>self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if($this->parent){
            $this->depth = $this->parent->depth + 1;
        }else{
            $this->depth = 0;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $this->status = self::STATUS_REMOVED;
        return $this->save(false);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = [
            'id',
            'section',
            'name',
            'description',
            'updateAt',
            'createAt'
        ];

        if($this->children){
            $fields[] = 'children';
        }
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }
}
