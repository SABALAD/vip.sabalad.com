<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooCredit'), 'object' => array('$', 'mooCredit'))); ?>
	mooCredit.initWithDrawal();
<?php $this->Html->scriptEnd(); ?>

<ul id="list-content">
	<?php if(count($items) > 0): ?>
		<?php foreach ($items as $val) :?>

			<li class="full_content p_m_10">
					<div class="row">
						<div class="col-md-3">
							<?php echo __d('credit', 'Request Date:') ?>
						</div>
						<div class="col-md-9">
							<?php echo $this->Moo->getTime( $val['CreditWithdrawPayment']['request_date'], Configure::read('core.date_format'), $utz ); ?>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<?php echo __d('credit', 'Request Amount:') ?>
						</div>
						<div class="col-md-9">
							<?php echo round($val['CreditWithdrawPayment']['request_amount'], 8); ?>
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
	                        <button id="dropdown-edit" data-target="#" data-toggle="dropdown" >
	                            <i class="material-icons">more_vert</i>
	                        </button>
	                        <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit" style="float: right;">
	                        	<?php if($val['CreditWithdrawPayment']['status'] == 'pending'): ?>
	                        		<li>
									<?php 
										$this->MooPopup->tag(array('href' => $this->Html->url(array("controller" => "credit_withdraw_payments",
                                                                    "action" => "edit_request_withdraw",
                                                                    "plugin" => 'credit',
                                                                    $val['CreditWithdrawPayment']['id']
                                                                )),
                                                                'title' => __d('credit', 'Edit'),
                                                                'data-dismiss' => 'modal',
                                                                'innerHtml' => __d('credit', 'Edit'),
                                                                'target' => 'ajax'
                                                            ));
									 ?>
									</li>
	                        	<?php endif; ?>
	                            <li><a href="javascript:void(0)" data-id="<?php echo $val['CreditWithdrawPayment']['id']?>" class="deleteRequestWithdraw" > <?php echo __d( 'credit', 'Delete')?></a></li>
                                <li class="seperate"></li>
	                        </ul>
	                    </div>
	                </div>
	                <hr>

			</li>
	
		<?php endforeach; ?>
	<?php else: ?>
		<li class="full_content p_m_10"><?php echo __d('credit', 'No items found') ?></li>
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