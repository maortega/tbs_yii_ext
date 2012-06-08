<?php

class TBSViewRenderer extends CApplicationComponent implements IViewRenderer{
	public $fileExtension = '.html';
	public $filePermission = 0755;
	private $tbs;
	private $yii;
	
	function init(){
		Yii::import('application.extensions.tbs_us.lib.*');
		spl_autoload_unregister(array('YiiBase','autoload'));
		require_once('tbs_class.php');
		spl_autoload_register(array('YiiBase','autoload'));
		$this->tbs = new clsTinyButStrong();
		$this->tbs->VarRef['yii'] = Yii::app();
	}
	
	public function renderFile($context, $sourceFile, $data, $return){
		// Current controller properties will be accessible as {this.property}
		$data['this'] = $context;
		$this->tbs->VarRef['data'] = $data;
		if(!is_file($sourceFile) || ($file=realpath($sourceFile))=== false)
			throw new CException(Yii::t('ext','View file "$sourceFile" does not exist.', array('{file}'=>$sourceFile)));
			
		$this->tbs->loadTemplate($sourceFile);
		
		//Merge blocks
		if(isset($context->blocks) && count($context->blocks) != 0){
			foreach($context->blocks as $block){
				$this->tbs->MergeBlock($block['name'],$block['values']);
			}
		}
		
		
		if($return){
			$this->tbs->show(TBS_NOTHING);
			return $this->tbs->Source;
		}else{
			$this->tbs->show();	
		}
		
	}
}