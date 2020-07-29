<?php
/**
 * @link https://github.com/netpc/yii2-ueditor
 * @link http://ueditor.baidu.com/website/index.html
 */

namespace netpc\ueditor;

use yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class UEditorController
 * UEditor服务端响应
 */
class UEditorController extends Controller
{
	/**
	 * UEditor的配置
	 * @see http://fex.baidu.com/ueditor/#start-config
	 * @var array
	 */
	public $config = [];

	/**
	 * 水印参数
	 * ['path'=>'水印图片位置','position'=>9]
	 * ['text'=>'水印文本内容','position'=>9]
	 * position in [1 ,9]，表示从左上到右下的9个位置， 默认位置为 9。
	 * @var array
	 */
	public $watermark = [];

	public function init()
	{
		parent::init();

		//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
		//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
		date_default_timezone_set("Asia/chongqing");
		error_reporting(E_ERROR);
		header("Content-Type: text/html; charset=utf-8");

		//CSRF 基于 POST 验证，UEditor 无法添加自定义 POST 数据，同时由于这里不会产生安全问题，故简单粗暴地取消 CSRF 验证。
		//如需 CSRF 防御，可以使用 server_param 方法，然后在这里将 Get 的 CSRF 添加到 POST 的数组中。。。
		Yii::$app->request->enableCsrfValidation = false;

		//当客户使用低版本IE时，会使用swf上传插件，维持认证状态可以参考文档UEditor「自定义请求参数」部分。
		//http://fex.baidu.com/ueditor/#server-server_param

		//保留UE默认的配置引入方式
		if (file_exists(__DIR__ . '/config.json'))
			$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", '', file_get_contents(__DIR__ . '/config.json')), true);
		else
			$CONFIG = [];

		if (!is_array($this->config))
			$this->config = [];

		if (!is_array($CONFIG))
			$CONFIG = [];

		$this->config = $this->config + $CONFIG;
		// $this->webroot = Yii::getAlias('@webroot');
		// if (!is_array($this->thumbnail))
		// 	$this->thumbnail = false;
	}

	/**
	 * 显示配置信息
	 */
	public function actionConfig()
	{
		return $this->config;
	}

	/**
	 * 可以直接修改ueditor.all.js文件中getActionUrl函数
	 */
	public function actionIndex()
	{
		$action = strtolower(Yii::$app->request->get('action', 'config'));
		//$actions = $this->getRoute();
		//$actions=Yii::$app->controller->actions();
		//print_r($actions);exit;
		if (Yii::$app->request->get('callback', false)) {
			Yii::$app->response->format = Response::FORMAT_JSONP;
		} else {
			Yii::$app->response->format = Response::FORMAT_JSON;
		}
		return $this->run($action);

		// if (isset($actions[$action]))
		// 	return $this->run($actions[$action]);
		// else
		// 	return ['state' => 'Unknown action.'];
	}

	/**
	 * 上传图片
	 */
	public function actionUploadimage()
	{
		$config = [
			'pathFormat' => $this->config['imagePathFormat'],
			'maxSize' => $this->config['imageMaxSize'],
			'allowFiles' => $this->config['imageAllowFiles']
		];
		$fieldName = $this->config['imageFieldName'];
		$result = $this->upload($fieldName, $config);
		return $result;
	}

	/**
	 * 上传涂鸦
	 */
	public function actionUploadscrawl()
	{
		$config = [
			'pathFormat' => $this->config['scrawlPathFormat'],
			'maxSize' => $this->config['scrawlMaxSize'],
			'allowFiles' => $this->config['scrawlAllowFiles'],
			'oriName' => 'scrawl.png'
		];
		$fieldName = $this->config['scrawlFieldName'];
		$result = $this->upload($fieldName, $config, 'base64');
		return $result;
	}

	/**
	 * 上传视频
	 */
	public function actionUploadvideo()
	{
		$config = [
			'pathFormat' => $this->config['videoPathFormat'],
			'maxSize' => $this->config['videoMaxSize'],
			'allowFiles' => $this->config['videoAllowFiles']
		];
		$fieldName = $this->config['videoFieldName'];
		$result = $this->upload($fieldName, $config);
		return $result;
	}

	/**
	 * 上传文件
	 */
	public function actionUploadfile()
	{
		$config = [
			'pathFormat' => $this->config['filePathFormat'],
			'maxSize' => $this->config['fileMaxSize'],
			'allowFiles' => $this->config['fileAllowFiles']
		];
		$fieldName = $this->config['fileFieldName'];
		$result = $this->upload($fieldName, $config);
		return $result;
	}

	/**
	 * 文件列表
	 */
	public function actionListfile()
	{
		$allowFiles = $this->config['fileManagerAllowFiles'];
		$listSize = $this->config['fileManagerListSize'];
		$path = $this->config['fileManagerListPath'];
		$result = $this->lists($allowFiles, $listSize, $path);
		return $result;
	}

	/**
	 *  图片列表
	 */
	public function actionListimage()
	{
		$allowFiles = $this->config['imageManagerAllowFiles'];
		$listSize = $this->config['imageManagerListSize'];
		$path = $this->config['imageManagerListPath'];
		$result = $this->lists($allowFiles, $listSize, $path);
		return $result;
	}

	/**
	 * 抓取远程图片
	 * User: Jinqn
	 * Date: 14-04-14
	 * Time: 下午19:18
	 */
	public function actionCatchimage()
	{
		set_time_limit(0);

		/* 上传配置 */
		$config = [
			'pathFormat' => $this->config['catcherPathFormat'],
			'maxSize' => $this->config['catcherMaxSize'],
			'allowFiles' => $this->config['catcherAllowFiles'],
			'oriName' => 'remote.png'
		];
		$fieldName = $this->config['catcherFieldName'];

		/* 抓取远程图片 */
		$list = array();
		if (isset($_POST[$fieldName])) {
			$source = $_POST[$fieldName];
		} else {
			$source = $_GET[$fieldName];
		}

		foreach ($source as $imgUrl) {
			$item = new Uploader($imgUrl, $config, "remote");
			$info = $item->getFileInfo();
			array_push($list, array(
				"state" => $info["state"],
				"url" => $info["url"],
				"size" => $info["size"],
				"title" => htmlspecialchars($info["title"]),
				"original" => htmlspecialchars($info["original"]),
				"source" => htmlspecialchars($imgUrl)
			));
		}

		/* 返回抓取数据 */
		return [
			'state' => count($list) ? 'SUCCESS' : 'ERROR',
			'list' => $list
		];
	}

	/**
	 * 返回文件和图片列表
	 * @param $allowFiles
	 * @param $listSize
	 * @param $path
	 * @return array
	 */
	protected function lists($allowFiles, $listSize, $path)
	{
		$allowFiles = substr(str_replace('.', '|', join('', $allowFiles)), 1);
		/* 获取参数 */
		$size = isset($_GET['size']) ? $_GET['size'] : $listSize;
		$start = isset($_GET['start']) ? $_GET['start'] : 0;
		$end = $start + $size;

		/* 获取文件列表 */
		$path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == '/' ? '' : '/') . $path;
		$files = $this->getFiles($path, $allowFiles);
		if (!count($files)) {
			$result = [
				'state' => 'no match file',
				'list' => [],
				'start' => $start,
				'total' => count($files),
			];
			return $result;
		}
		/* 获取指定范围的列表 */
		$len = count($files);
		for ($i = min($end, $len) - 1, $list = []; $i < $len && $i >= 0 && $i >= $start; $i--) {
			$list[] = $files[$i];
		}

		//倒序
		//for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
		//    $list[] = $files[$i];
		//}

		/* 返回数据 */
		$result = [
			'state' => 'SUCCESS',
			'list' => $list,
			'start' => $start,
			'total' => count($files),
		];
		return $result;
	}

	/**
	 * 遍历获取目录下的指定类型的文件
	 * @param $path 路径
	 * @param $allowFiles 允许类型
	 * @param $files 返回文件列表
	 * @return array
	 */
	protected function getfiles($path, $allowFiles, &$files = array())
	{
		if (!is_dir($path)) return null;
		if (substr($path, strlen($path) - 1) != '/') $path .= '/';
		$handle = opendir($path);
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..') {
				$path2 = $path . $file;
				if (is_dir($path2)) {
					$this->getfiles($path2, $allowFiles, $files);
				} else {
					if (preg_match("/\.(" . $allowFiles . ")$/i", $file)) {
						$files[] = array(
							'url' => substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
							'mtime' => filemtime($path2)
						);
					}
				}
			}
		}
		return $files;
	}

	/**
	 * 各种上传
	 * @param $fieldName
	 * @param $config
	 * @param $base64
	 * @return array
	 */
	protected function upload($fieldName, $config, $base64 = 'upload')
	{
		/* 生成上传实例对象并完成上传 */
		$up = new Uploader($fieldName, $config, $base64);

		/**
		 * 得到上传文件所对应的各个参数,数组结构
		 * array(
		 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
		 *     "url" => "",            //返回的地址
		 *     "title" => "",          //新文件名
		 *     "original" => "",       //原始文件名
		 *     "type" => ""            //文件类型
		 *     "size" => "",           //文件大小
		 * )
		 */

		/* 返回数据 */
		return $up->getFileInfo();
	}
}