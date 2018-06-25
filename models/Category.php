<?php

namespace saghar\category\models;

use aminkt\widgets\alert\Alert;
use api\components\UrlMaker;
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
 * @property string $tags
 * @property string $description
 * @property int $status
 * @property int $parentId
 * @property int $depth
 * @property Category $child
 * @property string $updateAt
 * @property string $createAt
 *
 * @property Category $parent
 * @property string $parentName
 */
class Category extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_REMOVED = 2;

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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section' => 'Section',
            'name' => 'نام',
            'tags' => 'تگ ها',
            'description' => 'توضیحات',
            'status' => 'وضعیت',
            'parentId' => 'والد',
            'depth' => 'عمق',
            'updateAt' => 'Update At',
            'createAt' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parentId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parentId' => 'id']);
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
     * @return Category
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
     */
    public static function create($data)
    {
        $category = new Category();
        $category->section = isset($data['section']) ? $data['section'] : 'main';
        $category->name = isset($data['name']) ? $data['name'] : '';
        $category->tags = isset($data['tags']) ? $data['tags'] : null;
        $category->description = isset($data['description']) ? $data['description'] : null;
        $category->status = isset($data['status']) ? $data['status'] : 1;
        $category->parentId = isset($data['parentId']) ? $data['parentId'] : null;
        $category->depth = isset($data['depth']) ? $data['depth'] : 0;

        if ($category->save()) {
            Alert::success('دسته با موفقیت ایجاد شد', 'اسم دسته جدید : ' . $category->name);
            return $category;
        } else {
            \Yii::error($category->getErrors());
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
     * @return null|Category
     *
     * @throws NotFoundHttpException
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
     */
    public static function edit($id, $data)
    {
        $category = Category::findOne($id);
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
     * Get one level down child of current category.
     *
     * @return $this
     */
    public function getChild(){
        return $this->hasMany(self::class, ['parentId' => 'id'])->where(['depth'=>$this->depth+1]);
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
        /** @var Category $categories */
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
     * Return an array to show categories as a tree found in specific section
     *
     * @param $section
     * @param null $id
     *
     * @return array|null
     */
    public static function getCategoriesBySectionAsArray($section, $id = null)
    {
        /** @var Category $categories */
        $categories = static::find()
            ->where(['parentId' => $id])
            ->andWhere(['=', 'section', $section])
            ->andWhere(['!=', 'status', self::STATUS_REMOVED])
            ->all();
        if ($categories) {
            $arr = Array();
            foreach ($categories as $cat) {
                $children = static::getCategoriesBySectionAsArray($section, $cat->id);
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
     * Get category link
     *
     * @param bool $api
     *
     * @return null|string
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getLink($api = true)
    {
        if ($this->depth < 2) {
            $route = [
                '/business/index',
                'depth' => $this->depth + 1,
                'parentId' => $this->id
            ];
        } elseif ($this->depth == 2) {
            $route = ['/business/list', 'catId' => $this->id];
        } else {
            return null;
        }

        if ($api) {
            return UrlMaker::to($route);
        } else {
            return Url::to($route);
        }
    }

    public function fields()
    {
        $fields = [
            'id',
            'name',
            'description',
            'updateAt',
            'createAt',
            'child'
        ];

        return $fields;
    }
}
