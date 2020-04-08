
<?php link_src_html("/plugins/icheck/skins/square/blue.css", "css"); ?>
<?php link_src_html("/plugins/icheck/icheck.min.js", "js"); ?>

<form id="wForm" class="wForm" action="/mypage/withdraw_proc">

    <p>정말로 회원탈퇴를 하시겠습니까 !!!</p>
    <p>&nbsp;</p>
    <p>불편하셨던 사항을 남겨주시면<br>서비스에 적극 반영하도록 하겠습니다.</p>
    <p>&nbsp;</p>
    <ul>
        <? foreach ($member_withdraw_list as $v => $txt) { ?>
            <li> <label><input type="radio" name="withdraw_reason" value="<?=$v?>" />&nbsp;&nbsp;<?=$txt?></label> </li>
        <? } ?>
    </ul>

</form>

<script type="text/javascript">

    $(function(){

        $('input[type="radio"]').iCheck({
            radioClass: 'iradio_square-blue'
        });


        $('#wForm').ajaxForm({
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(){
            },
            success: function(result){

                if(empty(result.msg) == false) showToast(result.msg);

                if(result.success == true){ //처리완료

                    if(isApp == 'Y'){
                        app_draw_member();
                    }else{

                        var container2 = $('<div class="warning-draw">');

                        var html  = '<p>회원탈퇴가 정상적으로</p>';
                            html += '<p>완료되었습니다.</p><br>';
                            html += '<p>더 나은 모습으로 찾아뵙겠습니다.</p>';

                            $(container2).html(html);

                            modalPop.createPop('회원탈퇴', container2);

                            modalPop.createButton('확인', 'btn btn-default btn-sm', function(){
                                go_home();
                            });
                            modalPop.show();
                    }

                }else{

                    if(empty(result.data) == false){

                        var container2 = $('<div class="warning-draw">');

                        var html  = '<p>현재 결재하신 주문 건이</p>';
                            html += '<p>총 '+ number_format(result.data) + ' 건이 있습니다.</p><br>';
                            html += '<p style="font-size: 14px!important;">고객센터(<?=$this->config->item('site_help_tel')?>)로</p>';
                            html += '<p style="font-size: 14px!important;">문의 후 해주시기 바랍니다.</p>';

                        $(container2).html(html);

                        modalPop.createPop('회원탈퇴', container2);
                        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
                        modalPop.show();

                    }

                }

            },
            error: function(){

            }

        });

    });

</script>
