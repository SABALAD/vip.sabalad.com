<?php 

App::uses('CreditAppModel', 'Credit.Model');

class CreditWithdrawPayment extends CreditAppModel{
	public $belongsTo = array(
        'User' => array(
            'className' => 'User'
        )
    );
}

 ?>