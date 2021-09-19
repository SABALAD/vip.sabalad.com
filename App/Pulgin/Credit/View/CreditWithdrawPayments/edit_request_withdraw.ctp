<?php 
    $request_amount = $item['CreditWithdrawPayment']['request_amount'];
    $bank_info = $item['CreditWithdrawPayment']['bank_info'];
 ?>

<div class="title-modal">
    <?php echo __d('credit', 'Withdrawal Funds')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
        	<form id="editForm" method="post">
	            <p>  	
	            	<?php if(!empty($balance)){
	                        $current_balance = round($balance['CreditBalances']['current_credit'], 8);
	                    }
	                    else{ $current_balance = 0;}?>
	            	<?php echo sprintf(__d('credit', 'Your current balance is <b> %s </b>. How many credit do you want to withdraw?'), $current_balance); ?>
	            </p>
	            <div class="form-group">
	            	<?php echo $this->Form->text('request_amount', array('class' => 'form-control', 'min' => 0, 'type' => 'number', 'value' => $request_amount)); ?>
	            </div>
	            <p>
	            	<?php echo __d('credit', 'Please provide us your bank account, paypal , Wallet Metamask...and all details here so that we can transfer requested money or CSBL Coin to') ?>
	            </p>
	            <div class="form-group">
                    <?php echo $this->Form->hidden('id', array('value' => $item['CreditWithdrawPayment']['id'])); ?>
	            	<?php echo $this->Form->textarea('bank_info', array('class' => 'form-control', 'rows' => 6, 'value' => $bank_info)); ?>
	            </div>
        	</form>
        </div>
    </div>
    <div class="row">
		<div class="col-md-12">
            <button class="btn btn-action btn-request-withdraw-edit"  data-url="<?php echo $this->base.'/credits/credit_withdraw_payments'; ?>" title="Submit"> <?php echo __d('credit', 'Submit'); ?> </button>
            <button type="button" class="btn default" data-dismiss="modal"><?php echo __('Close'); ?></button>
        </div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger error-message" style="display:none;margin-top:10px;"></div>
		</div>
	</div>
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooCredit"], function($, mooCredit) {
        mooCredit.initWithDrawal();
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooCredit'), 'object' => array('$', 'mooCredit'))); ?>
        mooCredit.initWithDrawal();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>