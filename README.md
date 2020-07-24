Ueditor for Yii2
================
yii2整合ueditor富文本编辑器

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist netpc/yii2-ueditor "*"
```

or add

```
"netpc/yii2-ueditor": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

视图:

```php
use netpc\ueditor\UEditor;

<?= UEditor::widget([]); ?>
<?= UEditor::widget([
    'name' => 'txtContent',
    'id' => 'txtContent',
    'options' => [
        'focus' => true,
        'toolbars' => [
            ['fullscreen', 'source', 'undo', 'redo', 'bold']
        ],
    ],
    'attributes' => [
        'style' => 'height:80px'
    ]
]);
?>
<?= $form->field($model, 'content')->textarea(['rows' => 6])->widget(UEditor::className(), [
    'options' => [
        'focus' => true,
        'toolbars' => [
            [
                'fullscreen', 'source', 'undo', 'redo', '|',
                'fontsize',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat',
                'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|',
                'forecolor', 'backcolor', '|',
                'lineheight', '|',
                'indent', '|'
            ],
        ],
    ],
    'attributes' => [
        'style' => 'height:80px'
    ]
]) ?>
```