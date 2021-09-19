<?php $this->setBodyClass('floating-menu'); ?>
<div class="bar-action-floating">
    <div class="container">
        <div id="stickyBrowseMenu" class="horizontal-main">
            <div class="horizontal-content">
                <div class="horizontal-menu-warp">
                    <ul class="browse-menu core-horizontal-menu horizontal-menu">
                        <li id="browse_all" class="<?php echo (isset($active_menu_top_members)) ? $active_menu_top_members : ""; ?>">
                            <a class="horizontal-menu-link" href="<?php echo $this->request->base ?>/credits">
                                <span class="horizontal-menu-text"><?php echo __d('credit', 'Top Members') ?></span>
                            </a>
                        </li>
                        <?php if (!empty($uid)): ?>
                            <li class="<?php echo (isset($active_menu_my_credits)) ? $active_menu_my_credits : ""; ?>">
                                <a class="horizontal-menu-link" href="<?php echo $this->request->base ?>/credits/index/my_credits">
                                    <span class="horizontal-menu-text"><?php echo __d('credit', 'My Transactions') ?></span>
                                </a>
                            </li>
                            <?php
                            if(ENABLE_WITHDRAW == true){
                                ?>
                                <li class="<?php echo (isset($active_menu_my_withdraw_request)) ? $active_menu_my_withdraw_request : ""; ?>">
                                    <a class="horizontal-menu-link" href="<?php echo $this->request->base ?>/credits/index/my_withdraw_request">
                                        <span class="horizontal-menu-text"><?php echo __d('credit', 'My withdraw request') ?></span>
                                    </a>
                                </li>
                            <?php } ?>
                        <?php endif; ?>
                        <li class="<?php echo (isset($active_menu_rank)) ? $active_menu_rank : ""; ?> ">
                            <a class="horizontal-menu-link" href="<?php echo $this->request->base ?>/credits/index/rank">
                                <span class="horizontal-menu-text"><?php echo __d('credit', 'Credits Rank') ?></span>
                            </a>
                        </li>
                        <li class="<?php echo (isset($active_menu_faqs)) ? $active_menu_faqs : ""; ?>">
                            <a class="horizontal-menu-link" href="<?php echo $this->request->base ?>/credits/index/faqs">
                                <span class="horizontal-menu-text"><?php echo __d('credit', 'FAQs') ?></span>
                            </a>
                        </li>
                        <li class="<?php echo (isset($active_menu_action_type)) ? $active_menu_action_type : ""; ?>">
                            <a class="horizontal-menu-link" href="<?php echo $this->request->base ?>/credits/index/action">
                                <span class="horizontal-menu-text"><?php echo __d('credit', 'Action types and credits') ?></span>
                            </a>
                        </li>

                        <?php if (!empty($uid)): ?>
                            <li class="<?php echo (isset($my_withdraw_requests)) ? $my_withdraw_requests : ""; ?>">
                                <a class="horizontal-menu-link" href="<?php echo $this->request->base ?>/credits/index/my_withdraw_requests">
                                    <span class="horizontal-menu-text"><?php echo __d('credit', 'My withdrawal requests') ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="core-horizontal-more hasChild hidden">
                            <a class="horizontal-menu-link horizontal-menu-header no-ajax" href="javascript:void(0);">
                                <span class="horizontal-menu-icon material-icons hidden">more_vert</span>
                                <span class="horizontal-menu-text"><?php echo __('More') ?></span>
                            </a>
                            <ul class="core-horizontal-dropdown horizontal-menu-sub"></ul>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="horizontal-action"></div>
        </div>
    </div>
</div>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooCoreMenu'), 'object' => array('$', 'mooCoreMenu'))); ?>
    $('.core-horizontal-menu').HorizontalMenu({asStickyBrowseMenuFor: '#stickyBrowseMenu'});
    $('#stickyBrowseMenu').StickyBrowseMenu({asHorizontalMenuFor: '.core-horizontal-menu'});
<?php $this->Html->scriptEnd(); ?>