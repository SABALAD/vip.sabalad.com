<?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__d('credit','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('credit', 'Manage Withdrawal Requests'), array('plugin' => 'credit', 'controller' => 'credit_withdraw_payments', 'action' => 'admin_index'));

    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Credit'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Credit', __d('credit','Manage Withdrawal Requests'));?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function(){

        $('.footable').footable();
        $('.btnExportWithdrawExel').unbind('click');
        $('.btnExportWithdrawExel').click(function(e){
            var keyword = $('#keyword').val();
            if(keyword == '') keyword = 'none';
            var status = $('#status').val();
            window.location = '<?php echo $this->Html->url('/admin/credit/credit_withdraw_payments/export_exel'); ?>'+'/'+keyword+'/'+status;
        });
    });

    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });
<?php $this->Html->scriptEnd(); ?>

<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-12">
                <div id="sample_1_filter" class="dataTables_filter">                    
                    <form method="post" action="<?php echo $this->request->base?>/admin/credit/credit_withdraw_payments">
                        <?php echo $this->Form->text('keyword', array('class' => 'form-control input-medium input-inline', 'placeholder' => __d('credit','Search by name')));?>
                        <?php 
                            $options = array('all' => __d('credit', 'All'), 'pending' => __d('credit', 'Pending'), 'accepted' => __d('credit', 'Accepted'), 'reject' => __d('credit', 'Rejected'));
                            echo $this->Form->select('status', $options, array('escape' => false, 'class' => 'form-control input-medium input-inline', 'value' => ( (isset($_POST['data']['status']) && !empty($_POST['data']['status'])) ? $_POST['data']['status'] : 'all')));
                         ?>
                        <button type="button" class="btn btn-action btnExportWithdrawExel"><?php echo __d('credit', 'Export data in list to exel') ?></button>
                        <?php echo $this->Form->submit('', array( 'style' => 'display:none' ));?>
                    </form>
                </div>
            </div>
        </div>
    </div>
        <table class="table table-striped table-bordered table-hover" id="sample_1">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('id', __d('credit', 'ID')); ?></th>
                <th><?php echo $this->Paginator->sort('request_date', __d('credit', 'Request Date')); ?></th>
                <th><?php echo $this->Paginator->sort('User.name', __d('credit', 'Name')); ?></th>
                <th><?php echo $this->Paginator->sort('request_amount', __d('credit', 'Request Amount')); ?> ( <?php echo __d('credit', 'credit'); ?> ) </th>
                <th><?php echo $this->Paginator->sort('amount_paid', __d('credit', 'Amount Payment')); ?> ( <?php echo Configure::read('Config.currency')['Currency']['symbol']; ?> )</th>
                <th><?php echo $this->Paginator->sort('bank_info', __d('credit', 'Bank Payment Details')); ?></th>
                <th><?php echo $this->Paginator->sort('status', __d('credit', 'Status')); ?></th>
                <th><?php echo __d('credit', 'Actions'); ?></th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($items)):?>
            <?php $count = 0;
            foreach ($items as $val): ?>
                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                    <td>
                        <?php echo $val['CreditWithdrawPayment']['id'];?>
                    </td>
                    <td>
                        <?php echo $val['CreditWithdrawPayment']['request_date'];?>
                    </td>
                    <td>
                        <a class="title" target="_blank" href="<?php echo $this->request->base?>/users/view/<?php echo $val['CreditWithdrawPayment']['user_id'] ?>">
                            <?php echo h($val['User']['name']); ?>
                        </a>
                    </td>
                    <td>
                        <?php echo $val['CreditWithdrawPayment']['request_amount'];?>
                    </td>
                    <td>
                        <?php echo $val['CreditWithdrawPayment']['amount_paid'];?>
                    </td>
                    <td>
                        <?php echo $val['CreditWithdrawPayment']['bank_info'];?>
                    </td>
                    <td>
                        <?php 
                            if($val['CreditWithdrawPayment']['status'] == 'pending'){
                                echo __d('credit', 'Pending');
                            }else if($val['CreditWithdrawPayment']['status'] == 'accepted'){
                                echo __d('credit', 'Accepted');
                            }else{
                                echo __d('credit', 'Rejected');
                            }
                         ?>
                    </td>
                    <td>
                        <a href="javascript:void(0)" title="<?php echo __d('credit','Delete');?>" onclick="mooConfirm('<?php echo __d('credit','Are you sure you want to delete this request?');?>', '<?php echo $this->request->base;?>/admin/credit/credit_withdraw_payments/delete_request_withdraw/<?php echo $val['CreditWithdrawPayment']['id'];?>')"><?php echo __d('credit','Delete');?></a>
                        <?php 
                            if($val['CreditWithdrawPayment']['status'] != 'accepted' && $val['CreditWithdrawPayment']['status'] != 'reject'){
                                echo ' | ';

                        ?>
                            <a href="javascript:void(0)" title="<?php echo __d('credit','Accepted');?>" onclick="mooConfirm('<?php echo __d('credit','Are you sure you want to accept this request?');?>', '<?php echo $this->request->base;?>/admin/credit/credit_withdraw_payments/accepted_request_withdraw/<?php echo $val['CreditWithdrawPayment']['id'];?>')"><?php echo __d('credit','Accepted');?></a>
                        <?php
                            }                        
                         ?>                        
                        <?php 
                            if($val['CreditWithdrawPayment']['status'] != 'accepted' && $val['CreditWithdrawPayment']['status'] != 'reject'){
                        ?>
                             | <a href="javascript:void(0)" title="<?php echo __d('credit','Reject');?>" onclick="mooConfirm('<?php echo __d('credit','Are you sure you want to reject this request?');?>', '<?php echo $this->request->base;?>/admin/credit/credit_withdraw_payments/reject_request_withdraw/<?php echo $val['CreditWithdrawPayment']['id'];?>')"><?php echo __d('credit','Reject');?></a>
                        <?php
                            }                        
                         ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php else:?>
            <tr>
                <td colspan="8">
                    <?php echo __d('credit', 'No item found');?>
                </td>
            </tr>
            <?php endif;?>
            </tbody>
        </table>

    <div class="pagination pull-right">
        <?php echo $this->Paginator->prev('« '.__d('credit', 'Previous'), null, null, array('class' => 'disabled')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next(__d('credit', 'Next').' »', null, null, array('class' => 'disabled')); ?>
    </div>
</div>
