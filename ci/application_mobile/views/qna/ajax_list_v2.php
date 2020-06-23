
<? foreach ($qna_list as $k => $r) { $img_arr = json_decode($r['bq_file'],true); //zsView($r); ?>

    <div class="box qna_area no-before">

        <div class="box-in qna" style="position: relative;">

            <div class="qna_question2">
                <div class="q_top">
                    <em style="color:orange"><?=$this->config->item($r['bq_category'],'board_qna_category');?></em>
                    <?if($r['bq_answer_yn'] == 'Y'){?>
                        <span class="btn btn-border-blue">답변완료</span>
                        <span class="btn btn-border-purple show_answer zs-cp" role="button">답변보기</span>
                    <?}else{?>
                        <span class="btn btn-border-gray">답변대기</span>
                    <?}?>
                    <em class="no_font fr date"><?=view_date_format($r['bq_regdatetime'])?></em>
                </div>
                <div class="q_btm" <?if($r['bq_answer_yn'] == 'Y'){?> style="padding-bottom: 15px;" <?}?>> <?=nl2br($r['bq_content'])?></div>

                <?if(empty($img_arr) == false){?>
                    <div class="q_img">
                        <? foreach ($img_arr as $v) {?>
                            <span><img src="<?=$v?>" width="100%"  /></span>
                        <? } ?>
                    </div>
                <?}?>

                <div class="del_wrap"> <span class="zs-cp del_qna" data-seq="<?=$r['bq_num']?>">질문삭제하기</span> </div>

            </div>

            <?if($r['bq_answer_yn'] == 'Y'){?>
                <div class="qna_answer_warp" style="display: none;">
                    <div class="qna_answer" style="margin: 0;position: absolute;top: 61px;z-index: 100">

                        <div class="a_top"> <i class="icon_answer"></i> 답변 <span class="close_answer zs-cp" role="button"></span> </div>
                        <div class="a_cont"> <?=$r['bq_answer_content']?> </div>
                        <div class="a_btm"><span class="writer"><?=$r['bq_last_writer']?></span> <em class="no_font date"><?=view_date_format($r['bq_answerdatetime'])?></em> </div>
                    </div>
                </div>
            <?}?>



        </div>
    </div>

<? } ?>

<script type="text/javascript">
    $(function(){
        <? if($req['page'] == $total_page){?>
        $('input[name="more"]').val(0);
        <?} ?>
    })
</script>