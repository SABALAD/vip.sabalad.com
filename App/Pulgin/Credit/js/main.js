(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooButton', 'mooPhrase', 'mooGlobal', 'mooAlert','mooFileUploader','mooValidate', 'typeahead', 'bloodhound', 'tagsinput'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooCredit = factory();
    }
}(this, function ($, mooButton, mooPhrase, mooGlobal,mooAlert,mooFileUploader,mooValidate, typeahead, bloodhound, tagsinput) {

    var initCreditSendToMember = function(){
        var friends_friendSuggestion = new Bloodhound({
            datumTokenizer:function(d){
                return Bloodhound.tokenizers.whitespace(d.name);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: {
                url: mooConfig.url.base +'/credits/get_member.json',
                cache: false,
                filter: function(list) {
                    return $.map(list.data, function(obj) {
                        return obj;
                    });
                }
            },
            
            identify: function(obj) { return obj.id; },
        });
            
        friends_friendSuggestion.initialize();

        $('#friendSuggestion').tagsinput({
            freeInput: false,
            itemValue: 'id',
            itemText: 'name',
            typeaheadjs: {
                name: 'friends_friendSuggestion',
                displayKey: 'name',
                highlight: true,
                limit:10,
                source: friends_friendSuggestion.ttAdapter(),
                templates:{
                    notFound:[
                        '<div class="empty-message">',
                            'unable to find any member',
                        '</div>'
                    ].join(' '),
                    suggestion: function(data){
                    if($('#userTagging').val() != '')
                    {
                        var ids = $('#friendSuggestion').val().split(',');
                        if(ids.indexOf(data.id) != -1 )
                        {
                            return '<div class="empty-message" style="display:none">unable to find any member</div>';
                        }
                    }
                        return [
                            '<div class="suggestion-item">',
                                '<img alt src="'+data.avatar+'"/>',
                                '<span class="text">'+data.name+'</span>',
                            '</div>',
                        ].join('')
                    }
                }
            }
        });
        $('#sendButtonCredit').click(function(){
            mooButton.disableButton('sendButtonCredit');
            $('#sendButtonCredit').spin('small');
            //console.log(sModal);
            $.post(mooConfig.url.base + '/credits/ajax_doSend', jQuery("#sendCredits").serialize(), function(data){
                mooButton.enableButton('sendButtonCredit');
                $('#sendButtonCredit').spin(false);
                var json = $.parseJSON(data);

                if ( json.result == 1 )
                {
                    $("#friend").val('');
                    $("#credit").val('');
                    $(".error-message").hide();
                    $(".alert-success").show();
                    $(".alert-success").html(json.message);
                }
                else
                {
                    $(".alert-success").hide();
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });

            return false;
        });
    }

    var initCreditSendToFriend = function()
    {
        $("#friends").tokenInput(mooConfig.url.base + '/friends/do_get_json',
            { preventDuplicates: true,
                hintText: mooPhrase.__('Enter a friend\'s name'),
                noResultsText: mooPhrase.__('No results'),
                tokenLimit: 10,
                resultsFormatter: function(item)
                {
                    return '<li>' + item.avatar + item.name + '</li>';
                }
            }
        );

        $('#sendButtonCredit').click(function(){
            mooButton.disableButton('sendButtonCredit');
            $('#sendButtonCredit').spin('small');
            //console.log(sModal);
            $.post(mooConfig.url.base + '/credits/ajax_doSend', jQuery("#sendCredits").serialize(), function(data){
                mooButton.enableButton('sendButtonCredit');
                $('#sendButtonCredit').spin(false);
                var json = $.parseJSON(data);

                if ( json.result == 1 )
                {
                    $("#friend").val('');
                    $("#credit").val('');
                    $(".error-message").hide();
                    $(".alert-success").show();
                    $(".alert-success").html(json.message);
                }
                else
                {
                    $(".alert-success").hide();
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });

            return false;
        });
    }

    var creditFaqCreate = function(){
            mooButton.disableButton('createButton');
            $.post(mooConfig.url.base + "/credits/faq_save", $("#createForm").serialize(), function (data) {
                mooButton.enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1)
                    location.reload();
                else
                {
                    $(".error-message").show();
                    $(".error-message").html('<strong>Error! </strong>' + json.message);
                }
            });
            return false;
    }

    var toggleField = function()
    {
        $('.opt_field').toggle();
    }

    var initWithDrawRequest = function()
    {
        $('#createFormWithDraw').validate({
            errorClass: "error-message",
            errorElement: "div"
        });

        $('#amount').on('keyup',function(){
            var text,
                number = $(this).val();
            text = (number * formula_credit) / formula_money;
            $("#wd_money").text(text);
        });

        $.validator.addMethod('compare',function(value, element, params){
            return (parseInt(value) >= parseInt(minimum_withdrawal_amount) && parseInt(value) <= parseInt(maximum_withdrawal_amount)) ? true : false;

            return false;
        },mooPhrase.__('validate_between'));
        $(document).off('click', "#btnWithDraw" );
        $(document).on('click', "#btnWithDraw" ,function(){
            $("#amount").rules("add",{
                    required: true,
                    number:true,
                    compare : $("#amount").val()
                });

           $("#payment").rules("add",{
                required:true
           });
            $("#payment_info").rules("add",{
                required: function(element){
                    if($('#li_payment_info').css("display") == 'none'){
                        return false;
                    }else{
                        return true;
                    }
                }
            });
        });

        $("#payment").on('change',function(){
           var value = $("#payment option:selected").val();

            if(value != ""){
                $("#li_payment_info").show();
            }else{
                $("#li_payment_info").hide();
            }
        });

        $(document).off('click', ".delete_withdraw_request");
        $(document).on('click', ".delete_withdraw_request", function(){
           var id = $(this).attr('data-id');
            $('#themeModal .modal-content').load(url + "/credits/withdraw_delete/" + id);
            $("#themeModal").modal('show');
        });

    }

    var init = function(){
        $('body').on('validateCredit', function(e, data){
            data.status = false;
            data.message = mooPhrase.__('credit_amount_not_enought');
        });
    }

    var initBuyCreditPaypal = function(urlReturn, urlReturnPaypal){
        $('input[type="submit"]').prop('disabled', true);
        $('#buyCreditForm input').on('change', function() {
            $('input[type="submit"]').prop('disabled', false);
            var valSelect = $('input[name=sell_selected]:checked', '#buyCreditForm').val(); 
            var arrVal = valSelect.split('_');
            $(".sell_id").val(arrVal[0]);
            $("#amount").val(arrVal[1]);
            if(!mooConfig.isApp){
                $("#return").val(urlReturn + "/" + arrVal[0]);
            }else{
                $("#return").val(urlReturn + "/" + arrVal[0] + '?app_no_tab=1');
            }            
            $("#notify_url").val(urlReturnPaypal + "/" + arrVal[0]);
        });
    }

    var initWithDrawal = function(){
        $('.deleteRequestWithdraw').unbind('click');
        $('.deleteRequestWithdraw').click(function(event) {
            var data = $(this).data();
            var deleteUrl = mooConfig.url.base + '/credit/credit_withdraw_payments/delete/' + data.id;
            mooAlert.confirm(mooPhrase.__('are_you_sure_you_want_to_remove_this_entry'), deleteUrl);
        });

        $('.btn-request-withdraw').unbind('click');
        $('.btn-request-withdraw').click(function(event) {
            $(this).spin('small');
            $(this).attr('disabled', 'disabled');

            $.post(mooConfig.url.base + "/credit/credit_withdraw_payments/save_request_withdraw", $('#createForm').serialize(), function (data) {
                var json = $.parseJSON(data);

                if (json.result == 1){
                    if(!mooConfig.isApp){
                        window.location = mooConfig.url.base + '/credits/index/my_withdraw_requests';
                    }else{
                        window.location = mooConfig.url.base + '/credit/credit_withdraw_payments/success?app_no_tab=1';
                    }
                    
                }
                else{
                    $('.btn-request-withdraw').spin(false);
                    $('.btn-request-withdraw').removeAttr('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
        });

        $('.btn-request-withdraw-edit').unbind('click');
        $('.btn-request-withdraw-edit').click(function(event) {
            $(this).spin('small');
            $(this).attr('disabled', 'disabled');

            $.post(mooConfig.url.base + "/credit/credit_withdraw_payments/save_request_withdraw_edit", $('#editForm').serialize(), function (data) {
                var json = $.parseJSON(data);

                if (json.result == 1){
                    if(!mooConfig.isApp){
                        window.location = mooConfig.url.base + '/credits/index/my_withdraw_requests';
                    }else{
                        window.location = mooConfig.url.base + '/credit/credit_withdraw_payments/success?app_no_tab=1';
                    }
                    
                }
                else{
                    $('.btn-request-withdraw-edit').spin(false);
                    $('.btn-request-withdraw-edit').removeAttr('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
        });
    }

    return {
        init : function(){
            init();
        },
        initCreditSendToMember: function (){
            initCreditSendToMember();
        },
        initCreditSendToFriend: function () {
            initCreditSendToFriend();
        },
        creditFaqCreate: function () {
            creditFaqCreate();
        },
        toggleField: function () {
            toggleField();
        },
        uploader : function() {
            uploader();
        },
        initWithDrawRequest : function() {
            initWithDrawRequest();
        },
        initBuyCreditPaypal: function(urlReturn, urlReturnPaypal){
            initBuyCreditPaypal(urlReturn, urlReturnPaypal);
        },
        initWithDrawal : function() {
            initWithDrawal();
        }
    }

}));
