Ueditor for Yii2
================
yii2整合ueditor富文本编辑器

基于yii2官方扩展yiisoft/yii2-imagine和UEditor资源包[1.4.3.3 PHP 版本] UTF-8版开发

扩展特点：

    1. 支持修改尺寸
    2. 支持文字水印
    3. 支持图片水印
    4. 支持多图上传


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist netpc/yii2-ueditor "*"
composer require --prefer-dist netpc/yii2-ueditor:"dev-master"
```

or add

```
"netpc/yii2-ueditor": "*"
```

to the require section of your `composer.json` file.

Update
-----
```
 composer require netpc/yii2-ueditor
```
Usage
-----

Once the extension is installed, simply use it in your code by  :

前端视图:

```php
use netpc\ueditor\UEditor;

<?= UEditor::widget([]); //如无设置id和name，默认从w1自动递增 ?>
<?= UEditor::widget([
    'id' => 'txtContent',
    'name' => 'txtContent',
    'options' => [
        'focus' => true,
        'toolbars' => [
            ['fullscreen', 'source', 'undo', 'redo', 'bold'],
        ],
        'serverUrl' => ['/ueditor/index'],//serverUrl指向自定义后端地址
        'catchRemoteImageEnable' => false,//默认抓取远程图片
    ],
    'attributes' => [
        'style' => 'height:500px'
    ],
]);
?>
<?= $form->field($model, 'content')->textarea(['rows' => 6])->widget(UEditor::className(), [
    'options' => [//详细参考ueditor.config.js
        'focus' => true,
        'toolbars' => [
            [
                'fullscreen', 'source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
                'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                'directionalityltr', 'directionalityrtl', 'indent', '|',
                'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
                'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                'simpleupload', 'insertimage', 'emotion', 'scrawl', 'insertvideo', 'music', 'attachment', 'map', 'gmap', 'insertframe', 'insertcode', 'webapp', 'pagebreak', 'template', 'background', '|',
                'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
                'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
                'print', 'preview', 'searchreplace', 'drafts', 'help'
            ],
        ],
        'catchRemoteImageEnable' => false,//设置是否抓取远程图片
    ],
    'attributes' => [
        'style' => 'height:80px'
    ]
]) ?>
```

后端处理:

通过配置controllerMap路由映射到对应的控制器上
如下：ueditor/index映射到'netpc\ueditor\UEditorController'
```php
<?php
$config = [
	'components' => [
		'request' => [
			// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
			'cookieValidationKey' => '',
		],
	],
	'controllerMap' => [
		'ueditor' => [
			'class' => 'netpc\ueditor\UEditorController',
			'config' => [
				//server config @see http://fex.baidu.com/ueditor/#server-config
				'imagePathFormat' => '/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',//图片
				'scrawlPathFormat' => '/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',//涂鸦
				'snapscreenPathFormat' => '/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',//截图
				'catcherPathFormat' => '/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',//抓取远程
				'videoPathFormat' => '/upload/video/{yyyy}{mm}{dd}/{time}{rand:6}',//视频
				'filePathFormat' => '/upload/file/{yyyy}{mm}{dd}/{rand:4}_{filename}',//文件文档
				'imageManagerListPath' => '/upload/image/',//图片管理列表
				'fileManagerListPath' => '/upload/file/',//文件管理列表
			],
			'resize' => [//修改尺寸，上传图片自动修改为600像素宽度
				'width' => 600,//小于600不处理
				'height' => 0,//高度为0 不限制高度
			],
			'watermark' => [//生成水印需官方扩展支持 composer require --prefer-dist yiisoft/yii2-imagine
				'path' => '@vendor/netpc/yii2-ueditor/assets/images/watermark.png', //图片水印路径 '@webroot/images/watermark.png'
				'text' => 'netpc.com.cn测试', //文字水印内容
				//'quality' => 90, //压缩质量
				//'fontsize' => 50, //字体大小 默认14
				//'fontpath'=> '@vendor/netpc/yii2-ueditor/assets/fonts/Alibaba-PuHuiTi-Heavy.otf',//字体路径 '@webroot/fonts/xxx.ttf'
				//'fontcolor'=> '#000000',//字体颜色 默认#000000
				'point' => [-80, -10],//x,y对应width,height 正数实际像素移动 负数图片宽、高减去像素移动 [10, -30]左下 [-200, 10]右上 [-200, -30] 右下
				'center' => true,//当center为真时以图片中心点为开始根据point正负偏移，正右移动，负左移动。
				'width' => 100,//小于宽度不加水印
				'height' => 100,//小于高度不加水印
			],
		]
	],
];
```
相关链接
-----
@see http://fex.baidu.com/ueditor/<br>
@see https://github.com/yiisoft/yii2-imagine