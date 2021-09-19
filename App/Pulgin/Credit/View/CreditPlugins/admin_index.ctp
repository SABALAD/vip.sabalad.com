<?php
	echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
	echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

	$this->Html->addCrumb(__d('credit', 'Plugins Manager'), '/admin/plugins');

	$this->startIfEmpty('sidebar-menu');
	echo $this->element('admin/adminnav', array("cmenu" => $type));
	$this->end();
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu($type , __d('credit','Credits'));?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <form class="form-horizontal intergration-setting" method="post" enctype="multipart/form-data" action="">
							    <div class="form-group">
						            <label class="col-md-3 control-label">
						            	<?php echo __d('credit', 'Enable credit with third party') ?>
						            </label>
						            <div class="col-md-7">
                						<?php
                							echo $this->Form->hidden('type', array('value' => $type));

                							$options = array(
											    'yes' => __d('credit', 'Enable'),
											    'no' => __d('credit', 'Disable')
											);

											$attributes = array(
												'separator' => '<br/>',
												'label' => array('class' => 'radio-setting'),
											    'legend' => false,
											    'value' => (isset($enable_third_party) ? $enable_third_party : 'no')
											);

											echo $this->Form->radio('enable_third_party', $options, $attributes);
                						 ?>
                					</div>
					            </div>
					            <div class="form-body">
					            	<div class="row">
			                            <div class="col-md-offset-3 col-md-9">
					                    	<input type="submit" class="btn btn-circle btn-action" value="<?php echo __d('credit', 'Save Settings'); ?>">
					                	</div>							            
							        </div>
					            </div>
						    </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>