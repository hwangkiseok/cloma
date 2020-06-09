<style>
    #container.join { width:100%; height:100%; margin:0 auto;  }
    #container.join .join-wrap { position:fixed; width:100%; height:100%; left:0; top:0; overflow:hidden;background-color: #ff3c63;
        /*background:url('/images/join_welcome.png') no-repeat; background-size:cover; */}
    #container.join .join_content { position:absolute; bottom:30px; width:100%; text-align:center; margin-bottom:0px; padding:20px 20px 15px; }
    #container.join .checkbox_group { margin-top:10px; text-align: center;font-size: 13px; color: #e8e8e8; line-height: 20px; }
    #container.join .checkbox_group a { color:#e8e8e8; text-decoration: underline; }
    /*180125 디자인수정 style*/
    #container.join .join_top img{width:20%; margin:50px 35px 10px;}
    #container.join .join_top p{font-size:31px; color:#fff; font-weight:500; letter-spacing: -1.5px; margin-left: 35px; line-height: 37px;}

</style>
<div id="container" class="join">
    <div class="join-wrap">

        <!--<div style="text-align: center;padding-top:55%;">-->
        <div class="join_content">
            <form name="join_form" id="join_form" method="post" action="/Auth/join_web_proc">
            <input type="hidden" name="m_num" value="<?=$member_row['m_num']?>" >
            <input type="hidden" name="return_url" value="<?=$return_url?>" >
                <button type="submit" class="btn btn-yellow btn-xxlarge" style="background:#fff;color:#ff3c63;font-size:23px;">가입완료</button>
                <div class="checkbox_group">
                    가입 시 옷쟁이들의 <a href="#none" id="btn_agreement" >이용약관</a>, <a href="#none" id="btn_privacy">개인정보취급방침</a>,
                    <a href="#none" id="btn_agreemaketing" >이벤트/쇼핑정보 수신동의</a> 에 동의하신 것으로 확인합니다.
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    //document.ready
    $(function(){

        //이용약관 modal
        $('#btn_agreement').on('click', function(){
            var container = $('<div style="height:300px;overflow:auto;border:1px solid #eee;padding:1em;"></div>');
            $(container).load('/customer/agreement/?pop=1');

            modalPop.createPop('이용약관', container);
            modalPop.show({'hide_footer':true});
        });

        //이메일정보수신동의 modal
        $('#btn_agreemaketing').on('click', function(){
            var container = $('<div style="height:300px;overflow:auto;border:1px solid #eee;padding:1em;"></div>');
            $(container).load('/customer/shopping_info/?pop=1');
            modalPop.createPop('이벤트/쇼핑정보 수신동의', container);
            modalPop.show({'hide_footer':true});
        });

        //개인정보보호정책 modal
        $('#btn_privacy').on('click', function(){
            var container = $('<div style="height:300px;overflow:auto;border:1px solid #eee;padding:1em;"></div>');
            $(container).load('/customer/privacy/?pop=1');

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
                var cf = confirm('옷쟁이들의 서비스 이용약관, 개인정보취급방침, 이벤트/쇼핑정보 수신동의에 동의합니다.');
                if(cf == false) return false;

            },
            success: function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }
                //alert(result.goUrl);
                if( result.status == '<?php echo get_status_code('success'); ?>' ) {

                    if(result.goUrl) {
                        //alert('');
                        location.replace(result.goUrl);
                    }else{
                        go_link('/', 'R');
                    }

                }
                else {
                    if( result.error_data ) {
                        $.each(result.error_data, function(key, msg){
                            if( $('#field_' + key).length ) {
                                error_message($('#field_' + key), msg);
                            }
                        });
                    }
                }//end of if()

                if( !empty(result.goUrl) ) {
                    go_link(result.goUrl, 'R');
                }
            }
        });//end of ajax_form()
    });//end of document.ready()
</script>