<script type="text/javascript">

    $(function(){
        $('.prof_wrap').on('click',function(){
            <? if( is_app() ) { ?>
            //카메라+저장소 권한 요청
            appAllowPermission('CAMERA|STORAGE');
            <? } ?>
        });

        //파일 선택시
        $("#m_sns_profile_img").on('change', function(){
            // show_loader();
            readURL(this);
        });

        $('.select-tap.gender span').on('click',function(){
            $('.select-tap.gender span').removeClass('active');
            $(this).addClass('active');
            $('input[name="gender"]').val($(this).data('gender'));
        });

        $('.select-tap.age_range span').on('click',function(){
            $('.select-tap.age_range span').removeClass('active');
            $(this).addClass('active');
            $('input[name="age_range"]').val($(this).data('age_range'));
        });

        $('.change_nickname .name_txt').on('click',function(){
            $(this).parent().find('span').toggleClass('on');

            if($('.change_nickname .name_input').hasClass('on') == true){
                $('input[name="temp_nickname"]').focus();
            }
        });
    })

    /**
     * 선택한 파일 미리보기
     * @param input
     */
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                if($('.prof_wrap img').hasClass('prof') == false) $('.prof_wrap img').addClass('prof');
                $('.prof_wrap img').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);

            hide_loader();
        }
    }//end of readURL()

    function validChk(){

        var nick = $('input[name="temp_nickname"]').val();
        $('input[name="m_nickname"]').val(nick);

        if( empty($('input[name="m_nickname"]').val()) == true){
            alert('<?=$this->config->item('site_name_kr')?>에서 사용할 닉네임을 기입해주세요 !');

            $('.change_nickname .name_txt').removeClass('on');
            $('.change_nickname .name_input').addClass('on');
            $('input[name="m_nickname"]').focus();

            return false;
        }

    }

    $(function(){

        $('form[name="myform"]').ajaxForm({
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(){
            },
            success: function(result){

                if( !empty(result.message) && result.message_type == 'alert' ) {
                    alert(result.message);
                }

                if( result.status == '<?php echo get_status_code('success');?>' ){
                    app_main_member_reload();
                    location.reload();
                }else{
                    showToast('개인정보 변경에 실패했습니다.');
                }
            },
            error: function(){
                alert("Submit Error");
            }
        });


    });

</script>

<form method="post" name="myform" action="<?=$this->page_link->update_proc?>" onsubmit="return validChk();" enctype="multipart/form-data">

    <input type="hidden" name="age_range" value="">
    <input type="hidden" name="gender" value="">
    <input type="hidden" name="m_nickname" value="<?=$aMemberInfo['m_nickname']?>">
    <input type="file" name="m_sns_profile_img" id="m_sns_profile_img" class="file" accept="image/*">

    <div class="box">
        <div class="box-in mypage-top">

            <label class="prof_wrap" for="m_sns_profile_img">
                <?if(empty($aMemberInfo['m_sns_profile_img']) == false){ ?>
                    <img class="prof" src="<?=$aMemberInfo['m_sns_profile_img']?>" alt="" />
                <?}else{?>
                    <img src="<?=IMG_HTTP?>/images/mypage_empty_profile.png" alt="" />
                <?}?>
            </label>
            <div class="nick_wrap">
                <p>
                    <span class="change_nickname zs-cp">
                        <span class="name_txt on"><?=$aMemberInfo['m_nickname']?><i class="icon-modify"></i></span>
                        <span class="name_input">
                            <input type="text" name="temp_nickname" value="<?=$aMemberInfo['m_nickname']?>" title="" />
                        </span>
                    </span>
                    <span class="warning"> 이름입력시 개인정보 입력은 자제해 주세요. </span>
                </p>
            </div>

        </div>
    </div>

    <div class="clear"></div>

    <div class="box">
        <div class="box-in mypage-btm">

            <label>성별 설정</label>
            <div class="select-tap gender">
                <span data-gender="F" <?if($aMemberInfo['m_gender'] == 'F'){?>class="active"<?}?> role="button">여성</span>
                <span data-gender="M" <?if($aMemberInfo['m_gender'] == 'M'){?>class="active"<?}?> role="button" style="border-right: 1px solid #ddd;">남성</span>
            </div>
            <div class="clear"></div>

            <label style="margin-top: 35px;">연령대 설정</label>
            <div class="select-tap age_range">
                <span data-age_range="10" <?if($aMemberInfo['m_age_range'] == '10'){?>class="active"<?}?> role="button">10대</span>
                <span data-age_range="20" <?if($aMemberInfo['m_age_range'] == '20'){?>class="active"<?}?> role="button">20대</span>
                <span data-age_range="30" <?if($aMemberInfo['m_age_range'] == '30'){?>class="active"<?}?> role="button">30대</span>
                <span data-age_range="40" <?if($aMemberInfo['m_age_range'] == '40'){?>class="active"<?}?> role="button">40대</span>
                <span data-age_range="50" <?if($aMemberInfo['m_age_range'] == '50'){?>class="active"<?}?> role="button">50대</span>
                <span data-age_range="60" <?if($aMemberInfo['m_age_range'] == '60'){?>class="active"<?}?> role="button">60대</span>
            </div>
            <div class="clear"></div>

        </div>
    </div>

    <div class="clear"></div>

    <div class="box no-before">
        <div class="box-in mypage-btn-area">
            <button class="btn btn-default btn-full" style="font-size: 18px;width: 50%!important;" type="submit">변경</button>
        </div>
    </div>

</form>