
<? foreach ($qna_list as $k => $r) { //zsView($r); ?>

    <div class="box qna_area no-before">

        <div class="box-in qna">

            <div class="qna_question">
                <div class="q_top">
                    <em style="color:orange"><?=$this->config->item($r['bq_category'],'board_qna_category');?></em>
                    <?if($r['bq_answer_yn'] == 'Y'){?>
                        <span class="btn btn-border-blue">답변완료</span>
                    <?}else{?>
                        <span class="btn btn-border-gray">답변대기</span>
                    <?}?>
                    <em class="no_font fr date"><?=view_date_format($r['bq_regdatetime'])?></em>
                </div>
                <div class="q_btm" <?if($r['bq_answer_yn'] == 'Y'){?> style="padding-bottom: 15px;" <?}?>> <?=nl2br($r['bq_content'])?></div>
            </div>

            <?if($r['bq_answer_yn'] == 'Y'){?>
            <div class="qna_answer">

                <div class="a_top">
                    ㄴ 답변
                </div>
                <div class="a_cont"> <?=$r['bq_answer_content']?> </div>
                <div class="a_btm"><span class="writer"><?=$r['bq_last_writer']?></span> <em class="no_font date"><?=view_date_format($r['bq_answerdatetime'])?></em> </div>
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