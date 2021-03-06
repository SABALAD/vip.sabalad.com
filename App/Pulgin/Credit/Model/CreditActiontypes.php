<?php
App::uses('CreditAppModel', 'Credit.Model');
class CreditActiontypes extends CreditAppModel {

    public function getActions()
    {
        $sCond = array(
            'show' => true
        );
        $actions = $this->find('all', array(
            'conditions' => $sCond,
            'order' => array('action_module asc')
        ));
        return $actions;
    }

    public function getActionTypeFormModule($action_module,$is_model = false)
    {
        if ($is_model) {
            $action = $this->find('first', array(
                'conditions' => array('action_type' => $action_module,'type'=>'model', 'is_active' => 1)
            ));
        }
        else{
            $action = $this->find('first', array(
                'conditions' => array('action_type' => $action_module, 'is_active' => 1)
            ));
        }
        return $action;
    }

    public function getListActionType($action_module = array())
    {
        $action = $this->find('all', array(
            'conditions' => array('action_type' => $action_module, 'is_active' => 1)
        ));
        return $action;
    }
}
