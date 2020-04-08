<div class="sms_auth_pop">
    <div class="cont">
        <div style="margin:0 0 20px 0;">
            <ul style="list-style:disc;margin-left:20px;">
                <li style="padding-left:0px;padding-bottom:5px;">이벤트에 참여하시기 위해서는 휴대폰인증을 받으셔야 합니다.</li>
            </ul>
        </div>
        <div class="auth_wrap">
            <div class="input_box">
                <label>휴대폰번호</label>
                <input type="number" name="ph1" value="010" class="input text-center" style="width:20%;" maxlength="4" maxlenthCheck />
                <em>-</em>
                <input type="number" name="ph2" value="" class="input text-center" style="width:20%;" maxlength="4" maxlenthCheck />
                <em>-</em>
                <input type="number" name="ph3" value="" class="input text-center" style="width:20%;" maxlength="4" maxlenthCheck />
            </div>

            <div class="clear"></div>

            <!-- 개인정보취급방침 -->
            <div class="agreement_text" style="width:100%;height:50px;overflow-y:scroll;border:1px solid #ddd;margin:10px 0;font-size:12px;"></div>
            <div style="text-align:right;">
                <label>
                    <input type="checkbox" id="privacy" name="privacy" class="icheck mgl10 fr" value="Y" checked /> 개인정보취급방침에 동의합니다.
                </label>
            </div>
            <!-- /개인정보취급방침 -->

            <div class="clear"></div>

            <a href="#none" class="btn_red btn" onclick="sms_auth_req();">인증하기</a>
        </div>

        <div class="clear"></div>

        <div class="auth_cert_wrap" style="margin-top:10px;display:none;">
            <div class="title line">인증번호를 입력해주세요.</div>

            <div class="clear"></div>

            <div class="input_box">
                <label class="fl" style="height:40px; line-height:40px;">인증번호</label>
                <div style="display:inline-block;float:left;margin-left:10px;">
                    <input type="number" name="auth_no" value="" class="input text-center" maxlength="5" maxlenthCheck style="width:100%;" />
                </div>
            </div>

            <div class="clear"></div>

            <div style="margin-top:5px;font-size:13px;">
                ※ 통신사 사정에 따라 SMS전송에 다소 시간이 걸릴 수 있습니다.
            </div>

            <div class="clear"></div>

            <div style="margin-top:10px;">
                <a href="#none" class="btn_retry btn fr" onclick="sms_auth_req(true);">재전송</a>
                <div class="timer">00:00</div>
            </div>
            <div class="clear"></div>

            <div class="clear"></div>
            <div style="margin-top:10px;">
                <a href="#none" class="btn_red btn" onclick="sms_auth_cert();">인증번호확인</a>
            </div>
        </div>
    </div>
</div>
<script>
    var obj_timer = null;
    var sms_auth_url = '/auth/sms_auth_nologin';
    var silent = '<?php echo $req['silent']; ?>';
    var phone = '';

    /**
     * SMS 인증 요청
     */
    function sms_auth_req(retry) {
        var mode = 'req';
        if( !empty(retry) ) {
            mode = 'retry';
        }

        var ph1 = $('[name="ph1"]').val();
        var ph2 = $('[name="ph2"]').val();
        var ph3 = $('[name="ph3"]').val();
        var ph = ph1 + ph2 + ph3;
        phone = ph;
        if( empty(ph1) || empty(ph2) || empty(ph3) ) {
            alert('휴대폰번호를 입력하세요.');
            if( empty(ph2) ) {
                $('[name="ph2"]').focus();
                return false;
            }
            if( empty(ph3) ) {
                $('[name="ph3"]').focus();
                return false;
            }
            return false;
        }//end of if()

        if( !$('[name="privacy"]').is(':checked') ) {
            alert('개인정보취급방침에 동의하셔야 합니다.');
            return false;
        }

        $.ajax({
            url : sms_auth_url,
            data : {'mode':mode, 'ph':ph, 'silent':silent},
            type : 'post',
            dataType : 'json',
            success : function(result){
                if( result.status == '<?php echo get_status_code('success'); ?>' ) {
                    alert('인증번호가 발송되었습니다.');
                    $('.auth_cert_wrap').show();
                    $('[name="auth_no"]').val('').focus();
                    show_timer();
                }
                else {
                    if( !empty(result.message) ) {
                        alert(result.message);
                    }
                }

                if( !empty(result.data) && !empty(result.data.reload) && result.data.reload == 'Y' ) {
                    location.reload();
                }
            }
        });
    }//end of sms_auth_req()

    /**
     * SMS 인증 확인
     */
    function sms_auth_cert() {
        $('.auth_cert_wrap').show();

        var no = $('[name="auth_no"]').val();
        if( empty(no) ) {
            alert('인증번호를 입력하세요.');
            $('[name="auth_no"]').focus();
            return false;
        }

        $.ajax({
            url : sms_auth_url,
            data : {'mode':'cert', 'no':no, 'silent':silent},
            type : 'post',
            dataType : 'json',
            success : function(result){
                if( !empty(result.message) && !empty(result.message_type) && result.message_type == 'alert' ) {
                    alert(result.message);
                }

                if( result.status == '<?php echo get_status_code('success'); ?>' ) {
                    <?php if( !empty($req['ret_call']) ) { ?>

                        if( typeof <?php echo $req['ret_call']; ?> == 'function' ) {
                            <?php echo $req['ret_call']; ?>(phone);
                        }

                    <?php } else { ?>

                    location.reload();

                    <?php }//endif ?>
                }
                else {
                    if( !empty(result.data) && !empty(result.data.reload) && result.data.reload == 'Y' ) {
                        location.reload();
                    }
                }
            },
            error : function(){
                alert('오류가 발생했습니다.');
            }
        });
    }//end of sms_auth_cert()

    /**
     * 타이머
     */
    function show_timer() {
        //초기화
        $('.timer').html('');
        $('.funcTimer').remove();

        if( obj_timer ) {
            clearInterval(obj_timer);
        }

        var time = parseInt(current_time() / 1000) + 180;   //3분
        var html = '<div class="time"><span class="funcTimer">00:00</span></div>';
        $('.timer').html(html);
        $('.timer').show();

        $('.funcTimer').html(leftTime(time, 'm'));
        obj_timer = setInterval(function(){
            if( $('.funcTimer').text() == '00:00' ) {
                clearInterval(obj_timer);
                return false;
            }
            $('.funcTimer').html(leftTime(time, 'm'));
        }, 1000);
    }//end of show_timer()


    $(function(){
        //개인정보취급방침 내용 출력
        $('.agreement_text').load('/customer/privacy/?pop=y');

        //icheck
        $('input[type="checkbox"].icheck, input[type="radio"].icheck').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });

        $('[name="ph2"]').focus();

        $('[name="ph2"]').on('keyup', function(){
            if( $(this).val().length >= 4 ) {
                $('[name="ph3"]').focus();
            }
        });
    });//end of document.ready()
</script>