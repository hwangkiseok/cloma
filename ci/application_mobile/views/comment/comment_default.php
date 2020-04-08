<? if($isAppend == false){?>

<?php link_src_html("/js/comment.js", "js"); ?>

    <script>
        function cmt_default_frm_chk(){
            if( $('.cmt_default_frm textarea[name="cmt_content"]').val() == '' ){
                alert('댓글내용을 입력해주세요');
                return false;
            }

            $('form[name="comment_frm"]').submit();

        }
    </script>

<div class="box">

    <div class="box-in" style="padding-top: 20px;">

        <form class="cmt_default_frm" name="comment_frm" action="/comment/insert_proc">
            <input type="hidden" name="cmt_table_num" value="<?=$aInput['tb_num']?>" />
            <input type="hidden" name="cmt_table" value="<?=$aInput['tb']?>" />
            <textarea name="cmt_content"></textarea>
            <button type="button" onclick="cmt_default_frm_chk()" class="btn btn-default">저장</button>
            <div class="clear"></div>
        </form>

    </div>
</div>
<div class="clear"></div>

<?} ?>
<? foreach ($aCommentLists as $k => $r) {?>

    <div class="box comment_area <?if($k == 0 && $isAppend == false){?>no-before<?}?>">

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
                    <span class="text"><?= nl2br($r['cmt_content'])?></span>
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
<? if($isNoMore == false){?>
<div class="box no-before more-wrap ">
    <div class="box-in ">
        <button class="btn btn-border-gray btn-full" onclick="comment_paging_ajax();" STYLE="border-radius: 10px;color: #aaa;">더보기 +</button>
    </div>
</div>
<? } ?>