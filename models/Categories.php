<?php

namespace saghar\category\models;

use aminkt\widgets\alert\Alert;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "tbl_categories".
 *
 * @property int $id
 * @property string $section
 * @property string $name
 * @property string $tags
 * @property string $description
 * @property int $status
 * @property int $parentId
 * @property int $depth
 * @property int $createAt
 * @property int $updateAt
 *
 * @property Categories $parent
 * @property string $parentName
 */
class Categories extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_REMOVED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_categories';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createAt', 'updateAt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updateAt'],
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
            [['status', 'parentId', 'depth'], 'integer'],
            [['section', 'name', 'tags', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Categories::className(), ['id' => 'parentId']);
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
            'status' => 'Status',
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
     *          'status' => $status,
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
     * @internal  smallInteger $status status
     *
     * @internal  integer $parentId parentId
     *
     * @internal  integer $depth depth
     *
     * @return Categories
     *
     * @throws \RuntimeException
     */
    public static function create($data)
    {
        $categories = new Categories();
        $categories->section = isset($data['section']) ? $data['section'] : 'main';
        $categories->name = isset($data['name']) ? $data['name'] : '';
        $categories->tags = isset($data['tags']) ? $data['tags'] : null;
        $categories->description = isset($data['description']) ? $data['description'] : null;
        $categories->status = isset($data['status']) ? $data['status'] : 1;
        $categories->parentId = isset($data['parentId']) ? $data['parentId'] : null;
        $categories->depth = isset($data['depth']) ? $data['depth'] : 0;

        if ($categories->save()) {
            Alert::success('دسته با موفقیت ایجاد شد', 'اسم دسته جدید : ' . $categories->name);
            return $categories;
        } else {
            \Yii::error($categories->getErrors());
            throw new \RuntimeException('دسته ذخیره نشد.');
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
     *          'status' => $status,
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
     * @internal  smallInteger $status status
     *
     * @internal  integer $parentId parentId
     *
     * @internal  integer $depth depth
     *
     * @return Categories
     *
     * @throws NotFoundHttpException
     */
    public static function edit($id, $data)
    {
        $category = Categories::findOne($id);
        if ($category) {
            $category->section = isset($data['section']) ? $data['section'] : 'main';
            $category->name = isset($data['name']) ? $data['name'] : '';
            $category->tags = isset($data['tags']) ? $data['tags'] : null;
            $category->description = isset($data['description']) ? $data['description'] : null;
            $category->status = isset($data['status']) ? $data['status'] : 1;
            $category->parentId = isset($data['parentId']) ? $data['parentId'] : null;
            $category->depth = isset($data['depth']) ? $data['depth'] : 0;
            if ($category->save()) {
                Alert::success('دسته با موفقیت ویرایش شد', 'اسم دسته : ' . $category->name);
                return $category;
            } else {
                throw new \RuntimeException('تغییرات ذخیره نشد.');
            }
        } else {
            throw new NotFoundHttpException('دسته پیدا نشد');
        }
    }

    /**
     * Return an array to show categories as a tree
     *
     * @param null $id
     *
     * @return array|null
     */
    public static function getCategoriesAsArray($id = null)
    {
        $categories = static::find()->where(['parentId' => $id])->andWhere(['!=', 'status', self::STATUS_REMOVED])->all();

        if ($categories) {
            $arr = Array();
            foreach ($categories as $cat) {
                $children = static::getCategoriesAsArray($cat->id);
                if ($children)
                    $arr[] = ['id' => $cat->id, 'name' => $cat->name, 'parent' => $children];
                else
                    $arr[] = ['id' => $cat->id, 'name' => $cat->name];
            }
            return $arr;
        }
        return null;
    }

    /**
     * Return an array to show business categories as a tree
     *
     * @param null $id
     *
     * @return array|null
     */
    public static function getBusinessCategoriesAsArray($id = null)
    {
        $categories = static::find()
            ->where(['parentId' => $id])
            ->andWhere(['=', 'section', 'business'])
            ->andWhere(['!=', 'status', self::STATUS_REMOVED])
            ->all();
        if ($categories) {
            $arr = Array();
            foreach ($categories as $cat) {
                $children = static::getBusinessCategoriesAsArray($cat->id);
                if ($children)
                    $arr[] = ['id' => $cat->id, 'name' => $cat->name, 'parent' => $children];
                else
                    $arr[] = ['id' => $cat->id, 'name' => $cat->name];
            }
            return $arr;
        }
        return null;
    }

    /**
     * Get parent name
     *
     * @return string
     */
    public function getParentName()
    {
        if ($this->parent) {
            return $this->parent->name;
        }
        return 'بدون والد';
    }

    /**
     * Set category status
     *
     * @param integer $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        if ($this->save()) {
            return $this;
        }
        throw new \RuntimeException('Status not changed');
    }

    /**
     * Returns static class instance, which can be used to obtain meta information.
     *
     * @param bool $refresh whether to re-create static instance even, if it is already cached.
     *
     * @return static class instance.
     */
    public static function instance($refresh = false)
    {
        // TODO: Implement instance() method.
}}
