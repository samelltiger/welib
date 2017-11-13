<?php
namespace  welib\controllers\common;

use Yii;
use yii\rest\ActiveController;

class BaseController extends ActiveController
{
	/**
	*  获取GET请求的参数
	* @param $name    参数名称
	* @param $default 如果没有传来该参数时，用这个值作为默认值
	*/
	public function get( $name = null , $default=null){
		return \Yii::$app->request->get( $name , $default );
	}

	/**
	*  获取POST请求的参数
	* @param $name    参数名称
	* @param $default 如果没有传来该参数时，用这个值作为默认值
	*/
	public function post( $name = null , $default=null ){
		return \Yii::$app->request->post( $name , $default );
	}

	/**
	* @param $data  渲染的数据
	* @param $state 标识此次请求是否成功
	* @param $code  返回的状态码
	* @param $message 错误信息
	*/
	public static function renderJson( $data , $state=1 , $code=200 , $message=null ){
		Yii::$app->response->statusCode = $code;
		$response =[
			'success' => $state?"success":"fail",
			'code' 	  => $code,
			'message' => $message,
			'data'	  => $data,
		];
		return $response;
	}

	// 获取模型验证的第一条错误
	public function getModelOneStrErrors($model){
		if($model->hasErrors()){
			$errors = $model->getFirstErrors();
			foreach ($errors as $value) {
				return $value;
			}
		}
		return false;
	}

	public function is_email($value){
		return preg_match('/[\w\d_-]+@[\w\d_-]+(\.[\w\d_-]+)+$/', $value);
	}

	public function is_id($value){
		return preg_match('/[\d]+$/', $value);
	}

	/**
	* 判读传来的值是否合法
	* @param mixed $value 值
	* @return int 0:id,1:email,2：数据不合法
	*/
	public function is_email_or_id($value){
		if($this->is_id($value))
			return 0;
		elseif($this->is_email($value))
			return 1;
		else
			return 2;
	}

	//递归判断所有的值是否为id
	public function is_id_map($arr){
		$state = true;

		if(is_array($arr)){
			foreach ($arr as $v) {
				if(is_array($v)){
					if ( !$this->is_id_map($v) ) 
						return false ;
				}
				else{
					if( !$this->is_id($v) ) 
						return false;
				}
			}
			return true;
		}else{
			return $this->is_id($arr);
		}
	}


	/**
	* 递归读取数组的深度
	* @param mixed $value 值
	* @return array 返回array($max,$min)
	*/
	public function array_deep($arr){
		$i = 0;
		$max = 0;
		$min = 100000000;
		if(!is_array($arr))
			return 0;

		foreach ($arr as $v) {
			if(!is_array($v)){
				$i = 1;
			}else{
				$i = 1+max($this->array_deep($v)) ;
			}
			if($max < $i)
				$max = $i;

			if($min > $i ){
				$min = $i;
			}
		}
		return [$max,$min];
	}

	/**
	*  该功能无法用语言表达，请自己尝试
	* @param $obj    继承自 yii\db\ActiveRecord 或者 yii\base\Model 的对象
	* @param $modelname $obj的类名
	* @param $arr_str 要保存的$obj对象中的属性名
	*/
	public function loadModelValue($obj,$modelname,$arr_str){
		$arr=[];
		foreach ($obj as $key => $value) {
			if( in_array($key, $arr_str) )
				$arr[$modelname][$key] = $value;
		}
		return $arr;
	}
}

?>