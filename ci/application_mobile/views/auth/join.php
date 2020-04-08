<div class="join-wrap">
    <!--<div style="text-align: center;padding-top:55%;">-->
    <div class="join_content" style="text-align: center;">
        <form name="join_form" id="join_form" method="post" action="/Auth/join_web_proc">
            <input type="hidden" name="m_num" value="<?=$member_row['m_num']?>" >
            <input type="hidden" name="return_url" value="<?=$return_url?>" >
            <div class="checkbox_group">
                가입 시 <?=$this->config->item('site_name_kr');?>의 <a href="#none" id="btn_agreement" >이용약관</a>, <a href="#none" id="btn_privacy">개인정보취급방침</a>,<br>
                <a href="#none" id="btn_agreemaketing" >이벤트/쇼핑정보 수신동의</a> 에 동의하신 것으로 확인합니다.
            </div>
            <button type="submit" class="btn" style="">가입완료</button>

        </form>
    </div>
</div>

<script>
    //document.ready
    $(function(){

        toggle_bg(true);

        //이용약관 modal
        $('#btn_agreement').on('click', function(){
            var container = $('<div style="height:300px;overflow:auto;border:1px solid #eee;padding:1em;"></div>');
            $(container).load('/common/termofus/?simple=Y');

            modalPop.createPop('이용약관', container);
            modalPop.show({'hide_footer':true});
        });

        //이메일정보수신동의 modal
        $('#btn_agreemaketing').on('click', function(){
            var container = $('<div style="height:300px;overflow:auto;border:1px solid #eee;padding:1em;"></div>');
            $(container).load('/common/UseEventNoti/?simple=Y');
            modalPop.createPop('이벤트/쇼핑정보 수신동의', container);
            modalPop.show({'hide_footer':true});
        });

        //개인정보보호정책 modal
        $('#btn_privacy').on('click', function(){
            var container = $('<div style="height:300px;overflow:auto;border:1px solid #eee;padding:1em;"></div>');
            $(container).load('/common/privacy/?simple=Y');
            modalPop.createPop('개인정보 취급방침', container);
            modalPop.show({'hide_footer':true});
        });

        //Ajax Form
        $('#join_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {
                var cf = confirm('<?=$this->config->item('site_name_kr');?>의 서비스 이용약관, 개인정보취급방침, 이벤트/쇼핑정보 수신동의에 동의합니다.');
                if(cf == false) return false;

            },
            success: function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == '<?php echo get_status_code('success'); ?>' ) {

                    if( !empty(result.goUrl) ) {
                        go_link(result.goUrl,'','','', 'R');
                    }else{
                        go_link('/','','','', 'R');
                    }

                }

            }
        });//end of ajax_form()
    });//end of document.ready()
</script>