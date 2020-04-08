<? if(count($aCommentLists) < 1){?>

    <div class="box comment_area no-before">

        <div class="box-in" style="background: #f0f0f0;">
            <div style="text-align: center;background: #fff;padding: 25px;border-radius: 10px;">등록한 댓글이 없습니다</div>
        </div>

    </div>

<? }else{?>

    <? foreach ($aCommentLists as $k => $r) {?>

        <div class="box comment_area no-before">

            <div class="box-in comment">

                <div class="question">
                    <div class="fl profile_img" style="margin-right: 10px;">
                        <?if(empty($r['m_sns_profile_img']) == true){?>
                            <img src="https://via.placeholder.com/40" alt="<?=$r['cmt_name']?>" />
                        <?}else{?>
                            <img src="<?=$r['m_sns_profile_img']?>" alt="<?=$r['cmt_name']?>" />
                        <?}?>
                    </div>
                    <div class="fl cmt_name"><?=$r['cmt_name']?></div>
                    <div class="fl cmt_content">
                        <span class="text"><?=$r['cmt_content']?></span>
                        <span class="date no_font"><?=view_date_format($r['cmt_regdatetime'],3)?></span>
                    </div>

                    <div class="clear"></div>
                </div>

                <?if(empty($r['cmt_answer']) == false){?>

                <div class="answer">
                    <div class="fl" style="margin-right: 10px;">
                        <img src="https://via.placeholder.com/40"  style="border-radius: 100%;" />
                    </div>
                    <div class="fl cmt_answer">
                        <span class="text sig_col"><?=nl2br($r['cmt_answer'])?></span>
                        <span class="date no_font"><?=view_date_format($r['cmt_answertime'],2)?></span>
                    </div>
                    <div class="clear"></div>
                </div>

                <?}?>
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