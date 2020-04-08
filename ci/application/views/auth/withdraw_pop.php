<div class="content withdraw_pop">
    <form name="pop_insert_form" id="pop_insert_form" method="post" action="/auth/withdraw_proc">

        <div style="width:100%;background:#fafafa;color:#333;padding:10px;overflow:hidden;border-top:1px solid #ddd;border-bottom:1px solid #ddd;">
            <!--탈퇴를 하시면 각종 이벤트 정보를 알림으로 받지 못합니다.-->
            불편하셨던 부분을 말씀해주세요.<br />
            더 나은 <?php echo $this->config->item('site_name_kr'); ?>이 될 수 있도록 노력하겠습니다.
        </div>

        <div class="clear"></div>

        <!--<div style="margin:20px 0;overflow:hidden;">-->
        <!--    <div style="float:left;margin-right:15px;">-->
        <!--        <i class="fa fa-microphone" style="font-size:38px;"></i>-->
        <!--    </div>-->
            <!--<ul style="margin:15px 0;">
                <li>불편하셨던 부분을 말씀해주세요.</li>
                <li>더 나은 <?php /*echo $this->config->item('site_name_kr'); */?>이 될 수 있도록 노력하겠습니다.</li>
            </ul>-->
        <!--</div>-->

        <div class="clear"></div>

        <ul class="reason_wrap">
            <!--<li style="margin-bottom:5px;">
                <input type="radio" name="mwl_reason" id="mwl_reason_99" value="99" checked />
                <label for="mwl_reason_99" style="padding-left:0;">
                    <input type="text" name="mwl_reason_etc" id="mwl_reason_etc" value="" class="input_text" placeholder="사유를 간단히 적어주세요" />
                </label>
            </li>-->
            <?php echo get_input_radio("mwl_reason", $this->config->item("member_withdraw_reason"), "", "", array(99), "<li>", "</li>"); ?>
        </ul>

        <div class="btns" style="margin-top:15px;">
            <button type="button" class="btn btn-gray btn-large2" data-dismiss="modal">계속 이용할래요</button>
            <button type="submit" class="btn btn-pink btn-large2">탈퇴 할래요</button>
        </div>
    </form>
</div>

<script>
    var pop_form = '#pop_insert_form';

    //document.ready
    $(function () {
        $('#mwl_reason_etc').focus();

        $('[name="rp_reason_radio"]').on('change', function () {
            $('#rp_reason').val('');

            if( $(this).val() == 'etc' ) {
                $('#rp_reason_etc').focus();
            }
            else {
                $('#rp_reason').val($(this).next('label').text());
            }
        });

        $('#mwl_reason_etc').on('click focus', function () {
            $('#mwl_reason_99').prop('checked', true);
        });

        $('#rp_reason_etc').on('focus', function () {
            $('#rp_reason').val('');
            $('#rp_reason_radio_etc').prop('checked', true);
        });

        $('#rp_reason_etc').on('keyup', function () {
            $('#rp_reason').val($(this).val());
        });

        //ajaxform
        $(pop_form).ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {
                //if( $('#mwl_reason_99').prop('checked') && !$('#mwl_reason_etc').val() ) {
                //    alert('서비스를 개선 시킬 수 있도록 사유를 적어주시면 정말 감사하겠습니다. 꼭 부탁드립니다.');
                //    return false;
                //}
                if( !$('[name="mwl_reason"]:checked').length ) {
                    alert('서비스를 개선 시킬 수 있도록 사유를 선택해주시면 정말 감사하겠습니다.');
                    return false;
                }
            },
            success: function(result) {
                if( !empty(result.message_type) && result.message_type == 'alert' && !empty(result.message) ) {
                    alert(result.message);
                }

                if( result.status == '<?php echo get_status_code('success'); ?>' ) {
                    location_replace('/auth/withdraw');
                }
                else {
                    if( result.error_data ) {
                        var error_text = null;

                        $.each(result.error_data, function(key, msg){
                            error_text += msg + '\n';
                        });

                        if( !empty(error_text) ) {
                            alert(error_text);
                        }
                    }
                }//end of if()
            }
        });//end of ajax_form()

    });//end of  document.ready
</script>