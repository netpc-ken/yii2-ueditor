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
		//Ĭ��name
		if (!isset($this->name)) {
			$this->name = 'content';
		}
		parent::init();
	}

	public function run()
	{
		$view = $this->getView();

		$this->attributes['id'] = $this->id;
		//�Ƿ�ActiveForm�е���
		if ($this->hasModel()) {
			$input = Html::activeTextarea($this->model, $this->attribute, $this->attributes);
		} else {
			$input = Html::textarea($this->name, '', $this->attributes);
		}
		UeditorAsset::register($view);//��Ueditor�õ��Ľű���Դ�������ͼ
		$js = 'var ue = UE.getEditor("' . $this->id . '",' . $this->getOptions() . ');';//Ueditor��ʼ���ű�
		$view->registerJs($js, $view::POS_READY);//��Ueditor��ʼ���ű�Ҳ��Ӧ����ͼ��
		return $input;

	}

	public function getOptions()
	{
		unset($this->options['id']);//Ueditorʶ����id����,�ʶ�ɾ֮
		return Json::encode($this->options);
	}
}