<?php
/**
 * @link https://github.com/netpc/yii2-ueditor
 * @link http://ueditor.baidu.com/website/index.html
 */

namespace netpc\ueditor;

/**
 * This is just an example.
 */

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class UEditor extends InputWidget
{
	public $attributes;

	public function init()
	{
		//print_r($this->id);exit;
		//默认name
		if (!isset($this->name)) {
			$this->name = 'content';
		}
		parent::init();
	}

	public function run()
	{
		$view = $this->getView();

		$this->attributes['id'] = $this->id;
		//是否ActiveForm中调用
		if ($this->hasModel()) {
			$input = Html::activeTextarea($this->model, $this->attribute, $this->attributes);
		} else {
			$input = Html::textarea($this->name, '', $this->attributes);
		}
		UeditorAsset::register($view);//将Ueditor用到的脚本资源输出到视图
		$js = 'var ue = UE.getEditor("' . $this->id . '",' . $this->getOptions() . ');';//Ueditor初始化脚本
		$view->registerJs($js, $view::POS_READY);//将Ueditor初始化脚本也响应到视图中
		return $input;

	}

	public function getOptions()
	{
		unset($this->options['id']);//Ueditor识别不了id属性,故而删之
		return Json::encode($this->options);
	}
}