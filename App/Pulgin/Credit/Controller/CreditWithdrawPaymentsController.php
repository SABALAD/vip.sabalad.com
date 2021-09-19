<?php

class CreditWithdrawPaymentsController extends CreditAppController
{
	public $components = array('Paginator');

	public function __construct($request = null, $response = null){
		parent::__construct($request, $response);
		$this->loadModel('Credit.CreditWithdrawPayment');
	}

	public function admin_index(){
		$cond = array();

		if (!empty($this->request->data['keyword'])){
            $cond['OR']['User.name LIKE ?'] = '%' . $this->request->data['keyword'] . '%';
        }

        if(!empty($this->request->data['status']) && $this->request->data['status'] != 'all'){
            $cond['CreditWithdrawPayment.status'] = $this->request->data['status'];
        }

		$this->Paginator->settings = array(
            'conditions' => $cond,
            'limit' => Configure::read('Credit.credit_item_per_pages'),
            'order' => array(
                'CreditWithdrawPayment.request_date' => 'DESC'
            )
        );

        $items = $this->Paginator->paginate('CreditWithdrawPayment');
        $this->set('items', $items);
        $this->set('title_for_layout', __d('credit', 'Manage Withdrawal Requests'));
	}

	public function request_withdraw(){
        $this->set('title_for_layout', __d('credit', 'Request Withdrawal'));
		$viewer = MooCore::getInstance()->getViewer();
        if (empty($viewer)) {
            return false;
        }
        $viewerId = MooCore::getInstance()->getViewer(true);

        $this->loadModel('Credit.CreditBalances');
        $balance = $this->CreditBalances->getBalancesUser($viewerId);

        $this->set('balance', $balance);
	}

	public function save_request_withdraw(){
		$data = $this->request->data;

		$viewer = MooCore::getInstance()->getViewer();

		if(empty($data['request_amount']) || $data['request_amount'] < 0){
			echo json_encode(array('result' => 0, 'message' => __d('credit', 'Request amount is invalid.')));
			exit();
		}

		if(empty($data['bank_info'])){
			echo json_encode(array('result' => 0, 'message' => __d('credit', 'Please enter withdraw info detail.')));
			exit();
		}

		$viewerId = MooCore::getInstance()->getViewer(true);

        $this->loadModel('Credit.CreditBalances');
        $balance = $this->CreditBalances->getBalancesUser($viewerId);

        if(empty($balance) || $data['request_amount'] > $balance['CreditBalances']['current_credit']){
        	echo json_encode(array('result' => 0, 'message' => __d('credit', 'Request amount must be less than or equal current balance.')));
			exit();
        }

        $data_ins = array();

        $credit_currency_exchange = Configure::read('Credit.credit_currency_exchange');

        
    	$amount_paid = 0;

        $amount_paid = number_format($data['request_amount']/$credit_currency_exchange, 2, '.', '');

        $data_ins = array(
        	'request_date' 		=> date('Y-m-d H:i:s'),
        	'user_id' 			=> $viewerId,
        	'request_amount' 	=> $data['request_amount'],
        	'amount_paid' 		=> $amount_paid,
        	'bank_info' 		=> $data['bank_info']
        );

        $this->CreditWithdrawPayment->clear();
        if($this->CreditWithdrawPayment->save($data_ins)){
            //update balance
            $balance['CreditBalances']['current_credit'] -= $data['request_amount'];
            $balance['CreditBalances']['spent_credit'] += $data['request_amount'];

            $this->CreditBalances->clear();
            $this->CreditBalances->save($balance);

            //send notify
            $this->loadModel('Notification');

            $this->Notification->record( array( 'recipients'  => 1,
                'sender_id'   => $viewerId,
                'action'      => 'request_withdraw_payment',
                'url'         => '/admin/credit/credit_withdraw_payments',
                'plugin'      => 'Credit'
            ) );

            //add log
            $this->loadModel('Credit.CreditLogs');
            $this->loadModel('Credit.CreditActiontypes');
            $action_type    = $this->CreditActiontypes->getActionTypeFormModule('request_withdrawal',false);
            $action_id      = $action_type['CreditActiontypes']['id'];
            $credit         = '-'.$data['request_amount'];
            $object_type    = 'credit_credit_withdraw_payments';
            $object_id      = $this->CreditWithdrawPayment->id;

            $this->CreditLogs->addLog($action_id, $credit, $object_type, $viewerId, $object_id);

            //send mail
            $this->MooMail->send($viewer['User']['email'], 'request_withdraw_payment', array());

            $this->Session->setFlash(__d('credit', 'Your withdrawal request is being processed! The request is processed within 3-5 working days'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }        

		echo json_encode(array('result' => 1));
		exit();

	}

	public function admin_delete_request_withdraw($id = null){
		$id = intval($id);
		$item = $this->CreditWithdrawPayment->findById($id);
        $this->_checkExistence($item);

        //return credit

        if($item['CreditWithdrawPayment']['status'] == 'pending'){            
            $this->loadModel('Credit.CreditBalances');
            $balance = $this->CreditBalances->getBalancesUser($item['CreditWithdrawPayment']['user_id']);
            $balance['CreditBalances']['current_credit'] += $item['CreditWithdrawPayment']['request_amount'];
            $balance['CreditBalances']['spent_credit'] -= $item['CreditWithdrawPayment']['request_amount'];

            $this->CreditBalances->clear();
            $this->CreditBalances->save($balance);

            //add log
            $this->loadModel('Credit.CreditLogs');
            $this->loadModel('Credit.CreditActiontypes');
            $action_type    = $this->CreditActiontypes->getActionTypeFormModule('cancelled_withdrawal',false);
            $action_id      = $action_type['CreditActiontypes']['id'];
            $credit         = $item['CreditWithdrawPayment']['request_amount'];
            $object_type    = 'credit_credit_withdraw_payments';
            $viewerId       = $item['CreditWithdrawPayment']['user_id'];
            $object_id      = $item['CreditWithdrawPayment']['id'];

            $this->CreditLogs->addLog($action_id, $credit, $object_type, $viewerId, $object_id);
        }       

		$this->CreditWithdrawPayment->delete($id);
		$this->Session->setFlash(__d('credit', 'Payment request has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect('/admin/credit/credit_withdraw_payments');
	}

	public function admin_reject_request_withdraw($id = null){
		$id = intval($id);
		$item = $this->CreditWithdrawPayment->findById($id);
        $this->_checkExistence($item);

        //update balance
        $this->loadModel('Credit.CreditBalances');
        $balance = $this->CreditBalances->getBalancesUser($item['CreditWithdrawPayment']['user_id']);

        $balance['CreditBalances']['current_credit'] += $item['CreditWithdrawPayment']['request_amount'];
        $balance['CreditBalances']['spent_credit'] -= $item['CreditWithdrawPayment']['request_amount'];

        $this->CreditBalances->clear();
        $this->CreditBalances->save($balance);

        //update status
		$item['CreditWithdrawPayment']['status'] = 'reject';

        $this->CreditWithdrawPayment->clear();
        $this->CreditWithdrawPayment->save($item);

        //add log
        $this->loadModel('Credit.CreditLogs');
        $this->loadModel('Credit.CreditActiontypes');
        $action_type    = $this->CreditActiontypes->getActionTypeFormModule('cancelled_withdrawal',false);
        $action_id      = $action_type['CreditActiontypes']['id'];
        $credit         = $item['CreditWithdrawPayment']['request_amount'];
        $object_type    = 'credit_credit_withdraw_payments';
        $viewerId       = $item['CreditWithdrawPayment']['user_id'];
        $object_id      = $item['CreditWithdrawPayment']['id'];

        $this->CreditLogs->addLog($action_id, $credit, $object_type, $viewerId, $object_id);

        //send notify
        $this->loadModel('Notification');
		$this->Notification->record( array( 'recipients'  => $item['CreditWithdrawPayment']['user_id'],
			'sender_id'   => $this->Auth->user('id'),
			'action'	  => 'request_withdrawal_reject',
			'url' 		  => '/credits/index/my_withdraw_requests',
			'plugin' 	  => 'Credit'
		) );

        $this->Session->setFlash(__d('credit', 'Payment request has been rejected'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect('/admin/credit/credit_withdraw_payments');
	}

	public function delete($id = null){
        $this->set('title_for_layout', __d('credit', 'Request Withdrawal'));
		$id = intval($id);
		$item = $this->CreditWithdrawPayment->findById($id);
        $this->_checkExistence($item);

        //return credit
        if($item['CreditWithdrawPayment']['status'] == 'pending'){
            $this->loadModel('Credit.CreditBalances');
            $balance = $this->CreditBalances->getBalancesUser($item['CreditWithdrawPayment']['user_id']);

            $balance['CreditBalances']['current_credit'] += $item['CreditWithdrawPayment']['request_amount'];
            $balance['CreditBalances']['spent_credit'] -= $item['CreditWithdrawPayment']['request_amount'];

            $this->CreditBalances->clear();
            $this->CreditBalances->save($balance);

            //add log
            $this->loadModel('Credit.CreditLogs');
            $this->loadModel('Credit.CreditActiontypes');
            $action_type    = $this->CreditActiontypes->getActionTypeFormModule('cancelled_withdrawal',false);
            $action_id      = $action_type['CreditActiontypes']['id'];
            $credit         = $item['CreditWithdrawPayment']['request_amount'];
            $object_type    = 'credit_credit_withdraw_payments';
            $viewerId       = MooCore::getInstance()->getViewer(true);
            $object_id      = $item['CreditWithdrawPayment']['id'];

            $this->CreditLogs->addLog($action_id, $credit, $object_type, $viewerId, $object_id);
        }        

		$this->CreditWithdrawPayment->delete($id);
		$this->Session->setFlash(__d('credit', 'Payment request has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        if (!$this->isApp())
        {
            $this->redirect($this->referer());
        }        
	}

	public function edit_request_withdraw($id = null){
        $this->set('title_for_layout', __d('credit', 'Edit Request Withdrawal'));
		$viewer = MooCore::getInstance()->getViewer();
        if (empty($viewer)) {
            return false;
        }
        $viewerId = MooCore::getInstance()->getViewer(true);

        $this->loadModel('Credit.CreditBalances');
        $balance = $this->CreditBalances->getBalancesUser($viewerId);

        $id = intval($id);
		$item = $this->CreditWithdrawPayment->findById($id);
        $this->_checkExistence($item);

        $this->set('balance', $balance);
        $this->set('item', $item);
	}

	public function save_request_withdraw_edit(){
		$data = $this->request->data;

		if(empty($data['request_amount']) || $data['request_amount'] < 0){
			echo json_encode(array('result' => 0, 'message' => __d('credit', 'Request amount is invalid.')));
			exit();
		}

		if(empty($data['bank_info'])){
			echo json_encode(array('result' => 0, 'message' => __d('credit', 'Please enter withdraw info detail.')));
			exit();
		}

		$viewerId = MooCore::getInstance()->getViewer(true);

        $this->loadModel('Credit.CreditBalances');
        $balance = $this->CreditBalances->getBalancesUser($viewerId);

        $item = $this->CreditWithdrawPayment->findById($data['id']);

        if( ($data['request_amount'] - $item['CreditWithdrawPayment']['request_amount']) > $balance['CreditBalances']['current_credit']){
        	echo json_encode(array('result' => 0, 'message' => __d('credit', 'The current balances is not enough to make the transaction')));
			exit();
        }

        //balance change
        $balance_change = 0;

        $balance_change = $data['request_amount'] - $item['CreditWithdrawPayment']['request_amount'];

        if($balance_change > 0 || $balance_change < 0){
            $balance['CreditBalances']['current_credit'] -= $balance_change;
        	$balance['CreditBalances']['spent_credit'] += $balance_change;
        	$this->CreditBalances->clear();
        	$this->CreditBalances->save($balance);
        }

        $data_ins = array();

        $credit_currency_exchange = Configure::read('Credit.credit_currency_exchange');

        
    	$amount_paid = 0;

        $amount_paid = number_format($data['request_amount']/$credit_currency_exchange, 2, '.', '');

        $data_ins = array(
        	'id'				=> $data['id'],
        	'request_amount' 	=> $data['request_amount'],
        	'amount_paid' 		=> $amount_paid,
        	'bank_info' 		=> $data['bank_info']
        );

        $this->CreditWithdrawPayment->clear();
        $this->CreditWithdrawPayment->save($data_ins);

        //add log
        $this->loadModel('Credit.CreditLogs');
        $this->loadModel('Credit.CreditActiontypes');
        $action_type    = $this->CreditActiontypes->getActionTypeFormModule('request_withdrawal',false);
        $action_id      = $action_type['CreditActiontypes']['id'];
        $credit         = '-'.$data['request_amount'];
        $object_type    = 'credit_credit_withdraw_payments';

        $log = $this->CreditLogs->find('first', array('conditions' => array('CreditLogs.object_type' => 'credit_credit_withdraw_payments', 'CreditLogs.object_id' => $data['id'])));

        if(!empty($log)){
            $this->CreditLogs->save(array('id' => $log['CreditLogs']['id'], 'credit' => '-'.$data['request_amount']));
        }

        $this->Session->setFlash(__d('credit', 'Your withdrawal request has been updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

		echo json_encode(array('result' => 1));
		exit();
	}

	public function admin_accepted_request_withdraw($id = null){
		$id = intval($id);
		$item = $this->CreditWithdrawPayment->findById($id);
		$this->_checkExistence($item);

		$item['CreditWithdrawPayment']['status'] = 'accepted';

		$this->CreditWithdrawPayment->clear();
		$this->CreditWithdrawPayment->save($item);

		//send notify

		$this->loadModel('Notification');

		$this->Notification->record( array( 'recipients'  => $item['CreditWithdrawPayment']['user_id'],
			'sender_id'   => $this->Auth->user('id'),
			'action'	  => 'request_withdrawal_accepted',
			'url' 		  => '/credits/index/my_withdraw_requests',
			'plugin' 	  => 'Credit'
		) );

		$this->Session->setFlash(__d('credit', 'Payment request has been accepted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
		$this->redirect($this->referer());
	}

    public function admin_export_exel($keyword = '', $status = 'all'){
        $data = $this->request->data;
        $cond = array();

        if (!empty($keyword) && $keyword != 'none'){
            $cond['OR']['User.name LIKE ?'] = '%' . $keyword . '%';
        }

        if(!empty($status) && $status != 'all'){
            $cond['CreditWithdrawPayment.status'] = $status;
        }

        $list_item = $this->CreditWithdrawPayment->find('all', array('conditions' => $cond));

        $this->response->download("withdrawal_export.csv");
        $this->set(compact('list_item'));

        $this->layout = 'ajax';
        return;
    }

    public function success(){
    }

}

?>