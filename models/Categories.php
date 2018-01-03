<?php

namespace saghar\category\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "tbl_categories".
 *
 * @property int $id
 * @property string $section
 * @property string $name
 * @property string $tags
 * @property string $description
 * @property int $parentId
 * @property int $depth
 * @property int $createAt
 * @property int $updateAt
 */
class Categories extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createAt',
                'updatedAtAttribute' => 'updateAt',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_categories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'createAt', 'updateAt'], 'required'],
            [['parentId', 'depth', 'createAt', 'updateAt'], 'integer'],
            [['section', 'name', 'tags', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section' => 'Section',
            'name' => 'Name',
            'tags' => 'Tags',
            'description' => 'Description',
            'parentId' => 'Parent ID',
            'depth' => 'Depth',
            'createAt' => 'Create At',
            'updateAt' => 'Update At',
        ];
    }

    /**
     * Create new category
     *
     * @param array $data <code>
     *      [
     *          'section' => $section,
     *          'name' => $name,
     *          'tags' => $tags,
     *          'description' => $description,
     *          'parentId' => $parentId,
     *          'depth' => $depth,
     *      ]
     * </code>
     *
     * @internal  string $section section
     *
     * @internal  string $name name
     *
     * @internal  string $tags tags
     *
     * @internal  string $description description
     *
     * @internal  integer $parentId parentId
     *
     * @internal  integer $depth depth
     *
     * @return Categories|null
     */
    public static function create($data)
    {
        $categories = new Categories();
        $categories->section = isset($data['section']) ? $data['section'] : 'main';
        $categories->name = isset($data['name']) ? $data['name'] : '';
        $categories->tags = isset($data['tags']) ? $data['tags'] : null;
        $categories->description = isset($data['description']) ? $data['description'] : null;
        $categories->parentId = isset($data['parentId']) ? $data['parentId'] : null;
        $categories->depth = isset($data['depth']) ? $data['depth'] : 0;

        if ($categories->save()) {
            \Yii::$app->getSession()->setFlash('success', 'دسته ذخیره شد.');
            return $categories;
        } else {
            \Yii::error($categories->getErrors());
            \Yii::$app->getSession()->setFlash('error', 'دسته ذخیره نشد.');
            return null;
        }
    }

    /**
     * Edit category
     *
     * @param $id
     *
     * @param array $data <code>
     *      [
     *          'section' => $section,
     *          'name' => $name,
     *          'tags' => $tags,
     *          'description' => $description,
     *          'parentId' => $parentId,
     *          'depth' => $depth,
     *      ]
     * </code>
     *
     * @internal  string $section section
     *
     * @internal  string $name name
     *
     * @internal  string $tags tags
     *
     * @internal  string $description description
     *
     * @internal  integer $parentId parentId
     *
     * @internal  integer $depth depth
     *
     * @return Categories|null
     */
    public static function edit($id, $data)
    {
        $category = Categories::findOne($id);
        if ($category) {
            $category = new Categories();
            $category->section = isset($data['section']) ? $data['section'] : 'main';
            $category->name = isset($data['name']) ? $data['name'] : '';
            $category->tags = isset($data['tags']) ? $data['tags'] : null;
            $category->description = isset($data['description']) ? $data['description'] : null;
            $category->parentId = isset($data['parentId']) ? $data['parentId'] : null;
            $category->depth = isset($data['depth']) ? $data['depth'] : 0;
            if ($category->save()) {
                \Yii::$app->getSession()->setFlash('success', 'تغییرات ذخیره شد.');
                return $category;
            } else {
                \Yii::error($category->getErrors());
                \Yii::$app->getSession()->setFlash('error', 'تغییرات ذخیره نشد.');
                return null;
            }
        } else {
            \Yii::$app->getSession()->setFlash('error', 'دسته پیدا نشد');
            return null;
        }
    }

}
