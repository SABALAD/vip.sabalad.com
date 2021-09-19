<script>
function doRefesh()
{
	location.reload();
}
</script>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooCredit'), 'object' => array('$', 'mooCredit'))); ?>
	mooCredit.initWithDrawal();
<?php $this->Html->scriptEnd(); ?>
<div class="row">
	<div class="col-md-12">
        <a style="display: block; margin: 0px 10px 10px 10px;" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" href="<?php echo $this->request->base?>/credit/credit_withdraw_payments/request_withdraw" title="<?php echo __d('credit', 'Request WithDrawal')?>"><?php echo __d('credit', 'Request Withdrawal')?> </a>
	</div>
</div>
<ul id="list-content">
	<?php if(count($items) > 0): ?>
		<?php foreach ($items as $val) :?>

			<li class="full_content p_m_10">
					<div class="row">
						<div class="col-md-3">
							<?php echo __d('credit', 'Request Date:') ?>
						</div>
						<div class="col-md-9">
							<?php echo $val['CreditWithdrawPayment']['request_date'] ?>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<?php echo __d('credit', 'Request Amount:') ?>
						</div>
						<div class="col-md-9">
							<?php echo $val['CreditWithdrawPayment']['request_amount'] ?>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<?php echo __d('credit', 'Request Detail:') ?>
						</div>
						<div class="col-md-9">
							<?php echo $val['CreditWithdrawPayment']['bank_info'] ?>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<?php echo __d('credit', 'Status:') ?>
						</div>
						<div class="col-md-9">
							<?php 
								if($val['CreditWithdrawPayment']['status'] == 'pending'){
	                                echo __d('credit', 'Pending');
	                            }else if($val['CreditWithdrawPayment']['status'] == 'accepted'){
	                                echo __d('credit', 'Accepted');
	                            }else{
	                                echo __d('credit', 'Rejected');
	                            }
							 ?>
						</div>
					</div>
					<div class="list_option">
	                    <div class="dropdown">
	                        <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon"  id="credit_edit_<?php echo $val['CreditWithdrawPayment']['id'] ?>">
								<i class="material-icons">more_vert</i>
							</button>
	                        <ul for="credit_edit_<?php echo $val['CreditWithdrawPayment']['id'] ?>" class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect">
	                        	<?php if($val['CreditWithdrawPayment']['status'] == 'pending'): ?>
	                        		<li class="mdl-menu__item mdl-js-ripple-effect">
	                        			<a href="<?php echo $this->base.'/credit/credit_withdraw_payments/edit_request_withdraw/'.$val['CreditWithdrawPayment']['id']; ?>"> <?php echo __d( 'credit', 'Edit')?></a>
									</li>
	                        	<?php endif; ?>
	                            <li class="mdl-menu__item mdl-js-ripple-effect"><a href="<?php echo $this->base.'/credit/credit_withdraw_payments/delete/'.$val['CreditWithdrawPayment']['id']; ?>"> <?php echo __d( 'credit', 'Delete')?></a></li>
                                <li class="seperate"></li>
	                        </ul>
	                    </div>
	                </div>
	                <hr>

			</li>
	
		<?php endforeach; ?>
	<?php else: ?>
		<li class="full_content p_m_10"><?php echo __d('credit', 'No found items.') ?></li>
	<?php endif; ?>
</ul>
<?php if(count($items) > 0): ?>
	<div class="row">
		<div class="col-md-12">
			<div class="pagination pull-right" style="margin-bottom: 0;">
			    <?php echo $this->Paginator->prev('« '.__d('credit', 'Previous'), null, null, array('class' => 'disabled')); ?>
			    <?php echo $this->Paginator->numbers(); ?>
			    <?php echo $this->Paginator->next(__d('credit', 'Next').' »', null, null, array('class' => 'disabled')); ?>
			</div>
		</div>
	</div>
<?php endif; ?>