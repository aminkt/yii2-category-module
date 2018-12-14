category manager
================
Category Module

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist saghar/yii2-category-module "v1.1.0.x-dev"
```

or add

```
"saghar/yii2-category-module": "v1.1.0.x-dev"
```

to the require section of your `composer.json` file.


Configuration
-----

Once the extension is installed, simply use it in your code by  :

1. Add module configuration to your config file in module section like as:
```php
'category' => [
    'class' => \saghar\category\Category::class,
    'modelClass' => \path\to\your\Model::class,
    'searchModelClass' => \path\to\your\search\Model::class
],
```

> Note: You can leave `modelClass` and `searchModelClass` blank to use default models of module.
> If you are using mongo db, you can use models implemented in `\saghar\category\models\mongo`

2. Run migration files of module using `yii migrate --migrationPath=@vendor/saghar/yii2-category-module/src/migrations --interactive=0`

> Note: If you are using mongo db please skip this step.

Advanced configuration
-------
If you want use your own model feel free to write your own code but please be aware about this steps to configurate your
your app using Category module.

1. Create your own Active record and implement `\saghar\category\interfaces\CategoryInterface`

2. In you configuration file define your models like described in configuration section.

Done.


Use Category module api to create, update, delete and fetch your categories.
-------------
You can use `\saghar\category\controllers\RestApiController` and extend your controller from this file.
This file will provide below actions and routes:

```text
GET /v2/category      // List of all categories.
GET /v2/category/[id]  // Detail of one single category.
DELETE /v2/category/[id]  // Delete a single category from server.
POST /v2/category     // Create a new category.
PUT /v2/category/[id]    // Update  category.

```


> Note: 
> All request except index and view shoud use at least one auth method to authrize user.


> Warning:
>   Cross origin is disabled by default. if you have any problem with this please report it.


CONTRIBUTING
---------
[If you want help us to improve this module please check this linkg.](CONTRIBUTING.md)