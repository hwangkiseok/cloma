<? if(count($aCommentLists) < 1){?>

    <div class="box comment_area no-before">

        <div class="box-in" style="background: #f0f0f0;">
            <div style="text-align: center;background: #fff;padding: 25px;border-radius: 10px;">등록한 댓글이 없습니다</div>
        </div>

    </div>

<? }else{?>

    <? foreach ($aCommentLists as $k => $r) { //if(zsDebug()) zsView($r); ?>

        <div class="box comment_area no-before">

            <div class="box-in comment">

                <div class="question">
                    <div style="padding-left: 2px;margin-bottom: 8px;">
                        <a class="zs-cp" style="display: inline-block;width: 100%;height: 100%;" onclick="go_product('<?=$r['p_num']?>','comment')">
                            <img src="<?=$r['p_today_image']?>" alt="<?=$r['p_name']?>" style="width: 70px;border-radius: 5px;" />
                            <span><?=$r['p_name']?></span>
                        </a>
                    </div>
                    <!--
                    <div class="fl profile_img" style="margin-right: 10px;">
                        <?if(empty($r['m_sns_profile_img']) == true){?>
                            <img src="<?=IMG_HTTP?>/images/no_profile.png" alt="<?=$r['cmt_name']?>" />
                        <?}else{?>
                            <img src="<?=$r['m_sns_profile_img']?>" alt="<?=$r['cmt_name']?>" />
                        <?}?>
                    </div>
                    <div class="fl cmt_name"><?=$r['cmt_name']?></div>
                    -->
                    <div class="fl cmt_content">
                        <span style="display: inline-block">
                            <span class="text" style=""><?=$r['cmt_content']?></span><br>
                            <span class="date no_font fr"><?=view_date_format($r['cmt_regdatetime'],3)?></span>
                        </span>

                    </div>
                    <div class="clear"></div>
                </div>

                <?if(empty($r['cmt_answer']) == false){?>

                <div class="answer">
                    <div class="fl" style="margin-right: 10px;">
                        <img src="<?=IMG_HTTP?>/images/300_300_icon.png?<?=time()?>"  style="border-radius: 100%;width: 40px" />
                    </div>
                    <div class="fl cmt_answer">
                        <span style="display: inline-block">
                            <span class="text sig_col"><?=nl2br($r['cmt_answer'])?></span><br>
                            <span class="date no_font fr"><?=view_date_format($r['cmt_answertime'],2)?></span>
                        </span>
                    </div>
                    <div class="clear"></div>
                </div>

                <?}?>
            </div>

            <div class="clear"></div>
            <div style="text-align: center;height: 1px;">
                <div style="display: inline-block;width: 95%;background: #ccc;height: 1px;vertical-align: top;"></div>
            </div>
        </div>

    <? } ?>

<? } ?>

<script type="text/javascript">

    $(function(){

        <?if($isNoMore == true){?>
        $('input[name="more"]').val(0);
        <?}?>
    });

    /* scrolling paging */
    var ajax_on  = false;
    $(window).scroll(function(){

        var more = $('input[name="more"]').val();

        if(more == 0) return false; //리스트 end
        if(ajax_on == true ) return false; //ajax 중인경우 return

        ajax_on = true;

        var x = parseInt($(this).scrollTop());
        var h = parseInt($('body').height()) - 200;
        var chkH =  parseInt($(window).outerHeight(true)) ;

        if( h < x +chkH ) comment_paging_ajax();

        ajax_on = false;

    });


    function del_cmt(cmt_num){

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {cmt_num:cmt_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    comment_paging_ajax(true);
                }
            },
            complete : function() {
            }
        });

    }

</script>