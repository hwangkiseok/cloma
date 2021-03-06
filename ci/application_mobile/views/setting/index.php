<div class="box">
    <div class="box-in setting">

        <p class="tit">알림설정</p>

        <ul class="push_set">

            <?if(is_app() == true){?>
            <li>
                <div class="fl">알림설정</div>
                <div class="fr chk-ani <?if($aMemberInfo['m_push_yn'] == 'Y'){?>active<?}?>">
                    <span class="circle"><?if($aMemberInfo['m_push_yn'] == 'Y'){?>켜짐<?}else{?>꺼짐<?}?></span>
                    <span class="hr-line"></span>
                </div>
                <div class="clear"></div>
            </li>
            <?}?>
            <li>
                <div class="fl">알림메시지보기</div>
                <div class="fr" style="text-align: right;position: relative;padding-right: 10px;" onclick="go_link('/push','','','push')">
                    보기 <i></i>
                </div>
                <div class="clear"></div>
            </li>
        </ul>

        <p class="tit">부가정보</p>

        <ul class="etc_info">
            <li>
                <div class="fl">이용약관</div>
                <div class="fr viewCommon" data-type="TermOfUs"> 보기<i></i> </div>
                <div class="clear"></div>
            </li>
            <li>
                <div class="fl">개인정보취급방침</div>
                <div class="fr viewCommon" data-type="Privacy"> 보기<i></i> </div>
                <div class="clear"></div>
            </li>
            <li>
                <div class="fl">이벤트/쇼핑정보수신동의</div>
                <div class="fr viewCommon" data-type="UseEventNoti"> 보기<i></i> </div>
                <div class="clear"></div>
            </li>
            <li>
                <div class="fl">사업자정보확인</div>
                <div class="fr viewCommon" data-type="bizInfo"> 보기<i></i> </div>
                <div class="clear"></div>
            </li>
        </ul>

        <?if(is_app() == true){?>
            <p class="tit">버전정보</p>
            <ul class="v_info">
                <li> 현재 버전 v <em class="no_font curr_v"></em> / 최신버전 v <em class="no_font"><?=$aVersionInfo['av_version']?></em> </li>
            </ul>
        <?}?>

        <p class="tit" style="border-bottom: none;padding: 10px">
            <span class="fr">
                <button class="btn btn-border-blue member_withdraw">회원탈퇴하기</button>
            </span>
        </p>

        <div class="clear">
    </div>
</div>

<script type="text/javascript">

    $(function(){

        $('.member_withdraw').on('click',function(){

            var container = $('<div class="member_withdraw_wrap">')
            $(container).load('/mypage/withdraw');
            modalPop.createPop('회원탈퇴', container);
            modalPop.createButton('탈퇴하기', 'btn btn-primary btn-sm', function(){
                $('#wForm').submit();
            });
            modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
            modalPop.show();

        });

        <?if(is_app() == true){?>
        var v = app_version_chk();
        $('.curr_v').html(v);
        <?}?>

        $('.viewCommon').on('click',function(){

            var tit = $(this).parent().find('div.fl').text();

            var type = $(this).data('type');

            if(type != 'bizInfo'){

                app_refresh_act(false);

                var container = $('<div style="height:300px;overflow:auto;border:1px solid #eee;padding:1em;"></div>');
                $(container).load('/common/'+type+'?simple=Y');
                modalPop.createPop(tit, container);
                modalPop.show({'hide_footer':true,'body_class':'no_padding'});

            }else{

                var go_url = 'http://www.ftc.go.kr/bizCommPop.do?wrkr_no=398-87-00626';

                <?if(is_app()){?>
                appNewWebBrowser(go_url);
                <?}else{?>
                go_link(go_url, '','Y');
                <?}?>

            }

        });

        <? if(is_app() == true) {?>

        $('.chk-ani').on('click',function(){
            if( $(this).hasClass('active') == true){
                app_push_able('N');
                push_toggle('N');
                $(this).removeClass('active');
                $(this).find('.circle').html('꺼짐');
            }else{
                 app_push_able('Y');
                push_toggle('Y');
                $(this).addClass('active');
                $(this).find('.circle').html('켜짐');
            };
        });

        <?}?>
    });

    function push_toggle(f){

        isShowLoader = false;

        $.ajax({
            url : '/setting/toggle_push',
            data : {f:f},
            type : 'post',
            dataType : 'json',
            async:false,
            success : function(result) {

                if( result.status != status_code['success'] ) {
                    alert('새고로침 후 다시 시도해주세요 !');
                    return false;
                }

            }

        });

    }

</script>

