<?php
    $helper = MooCore::getInstance()->getHelper("Credit_Credit");

    if(isset($item_object['ActivityComment'])){
    	echo __d('credit','Comment status');
    }else{
    	echo __d('credit','Reply comment status');
    }
?>