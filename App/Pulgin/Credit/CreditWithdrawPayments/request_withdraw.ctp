<div class="bar-content">
    <div class="content_center">
    	<div id="list-content">
			<div class="row">
				<div class="col-md-12">
					<h3>
						<?php echo __d('credit', 'Withdrawal Funds')?>
					</h3>
				</div>
			</div>

			<div class="row">
			    <div class="col-md-12">
			    	<form id="createForm" method="post">
			            <p>  	
			            	<?php if(!empty($balance)){
			                        $current_balance = round($balance['CreditBalances']['current_credit'], 2);
			                    }
			                    else{ $current_balance = 0;}?>
			            	<?php echo sprintf(__d('credit', 'Your current balance is <b> %s </b>. How many credit do you want to withdraw?'), $current_balance); ?>
			            </p>
			            <div class="form-group">
			            	<?php echo $this->Form->text('request_amount', array('class' => 'form-control', 'min' => 0, 'type' => 'number')); ?>
			            </div>
			            <p>
			            	<?php echo __d('credit', 'Please provide us your bank account, paypal...and all details here so that we can transfer requested money to') ?>
			            </p>
			            <div class="form-group">
			            	<?php echo $this->Form->textarea('bank_info', array('class' => 'form-control', 'rows' => 6)); ?>
			            </div>
			    	</form>
			    </div>
			</div>
			<div class="row">
				<div class="col-md-12">
			        <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1 btn-request-withdraw"  data-url="<?php echo $this->base.'/credits/credit_withdraw_payments'; ?>" title="Submit"> <?php echo __d('credit', 'Submit'); ?> </button>
			    </div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-danger error-message" style="display:none;margin-top:10px;"></div>
				</div>
			</div>
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