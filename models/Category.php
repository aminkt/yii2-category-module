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
 *
 * @property Category[] $children
 * @property string $updateAt
 * @property string $createAt
 *
 * @property Category $parent read-only
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
            [['children'], 'safe'],
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
     *          'prent' => [
     *              'section' => $section,
     *              'name' => $name,
     *              'tags' => $tags,
     *              'description' => $description,
     *              'status' => $status,
     *              'parentId' => $parentId,
     *              'prent' => [
     *                  .....
     *              ]
     *              'depth' => $depth,
     *          ]
     *          'depth' => $depth,
     *      ]
     * </code>
     *
     * @return Category
     *
     * @internal  string $section section optional
     *
     * @internal  string $name name
     *
     * @internal  string $tags tags optional
     *
     * @internal  string $description description optional
     *
     * @internal  smallInteger $status status optional
     *
     * @internal  integer $parentId parentId optional
     *
     * @internal  array $parrent optional
     *
     * @internal  integer $depth depth optional
     *
     */
    public static function create($data)
    {
        $category = new Category();
        $category->section = isset($data['section']) ? $data['section'] : 'main';
        $category->name = $data['name'];
        $category->tags = isset($data['tags']) ? $data['tags'] : null;
        $category->description = isset($data['description']) ? $data['description'] : null;
        $category->status = isset($data['status']) ? $data['status'] : self::STATUS_ACTIVE;
        if(isset($data['parent'])){
            $category->parent = $data['parent'];
        }elseif (isset($data['parentId'])){
            $category->parentId = $data['parentId'];
        }
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
     *          'prent' => [
     *              'section' => $section,
     *              'name' => $name,
     *              'tags' => $tags,
     *              'description' => $description,
     *              'status' => $status,
     *              'parentId' => $parentId,
     *              'prent' => [
     *                  .....
     *              ]
     *              'depth' => $depth,
     *          ]
     *          'depth' => $depth,
     *      ]
     * </code>
     *
     * @return null|Category
     *
     * @throws NotFoundHttpException
     *
     * @internal  string $section section optional
     *
     * @internal  string $name name optional
     *
     * @internal  string $tags tags optional
     *
     * @internal  string $description description optional
     *
     * @internal  smallInteger $status status optional
     *
     * @internal  integer $parentId parentId optional
     *
     * @internal  array $parrent optional optional
     *
     * @internal  integer $depth depth optional
     *
     */
    public static function edit($id, $data)
    {
        $category = Category::findOne($id);
        if ($category) {
            $category->section = isset($data['section']) ? $data['section'] : $category->section;
            $category->name = isset($data['name']) ? $data['name'] : $category->name;
            $category->tags = isset($data['tags']) ? $data['tags'] : $category->tags;
            $category->description = isset($data['description']) ? $data['description'] : $category->description;
            $category->status = isset($data['status']) ? $data['status'] : $category->status;
            if(isset($data['parent'])){
                $category->parent = $data['parent'];
            }elseif (isset($data['parentId'])){
                $category->parentId = $data['parentId'];
            }
            $category->depth = isset($data['depth']) ? $data['depth'] : $category->depth;
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
     * Get one level down children of current category.
     *
     * @return $this
     */
    public function getChildren(){
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
                $childrenren = static::getCategoriesAsArray($cat->id);
                if ($childrenren)
                    $arr[] = ['id' => $cat->id, 'name' => $cat->name, 'parent' => $childrenren];
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
                $childrenren = static::getCategoriesBySectionAsArray($section, $cat->id);
                if ($childrenren)
                    $arr[] = ['id' => $cat->id, 'name' => $cat->name, 'parent' => $childrenren];
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
     * @deprecated This function is not valid and will delete as soon
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

    /**
     * Create new children category
     *
     * @param array $data <code>
     *      [
     *          'id' => $parentId
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
     * @internal  integer $id parent id.
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
    public function setChildren(array  $array) {
        foreach ($array as $data){
            if(isset($data['id'])){
                $children = self::findOne($data['id']);
            }else{
                $children = new Category();
            }
            $children->load($data, '');
            $children->depth = $this->depth + 1;
            $children->parentId = $this->id;
            $children->save();
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $this->status = self::STATUS_REMOVED;
        return $this->save(false);
    }

    public function fields()
    {
        $fields = [
            'id',
            'section',
            'name',
            'description',
            'tags',
            'updateAt',
            'createAt',
            'children'
        ];

        return $fields;
    }
}
