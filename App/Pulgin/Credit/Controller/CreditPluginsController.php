<?php 
	class CreditPluginsController extends CreditAppController {

	    public function beforeFilter() {
	        parent::beforeFilter();
	    }

	    public function admin_index(){
	    	$type = (isset($this->request->named['type']) ? $this->request->named['type'] : '');

	    	$this->loadModel('Credit.CreditActiontypes');

	    	$enabled = 'no';

	    	$check_enable = $this->CreditActiontypes->find('all', array('conditions' => array('CreditActiontypes.action_type' => strtolower($type), 'CreditActiontypes.is_active' => 1)));

	    	if(count($check_enable) > 0) $enabled  = 'yes';

	    	$this->set('enable_third_party', $enabled);

	    	if($this->request->isPost()){
	    		$data = $this->request->data;

	    		if(!empty($data['type'])){	    			
    				$helperPlugin = MooCore::getInstance()->getHelper($data['type']. '_' . $data['type']);

	    			if($data['enable_third_party'] == 'yes'){
	    				$helperPlugin->insertActionTypeCredit();
	    			}else{
	    				$helperPlugin->disableActionTypeCredit();
	    			}

	    			$this->set('enable_third_party', $data['enable_third_party']);
	    			$this->Session->setFlash(__d('credit','Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
	    		}
	    	}
	    	$this->set('type', $type);
	    	$this->set('title_for_layout', __d('credit','Credits'));
	    }
	}
 ?>