<div class="box">
    <div class="box-in">
        <div class="page_tit">
<!--            <h2> 나의 1:1문의 </h2>-->
<!--            <p>고객님께서 <span>문의하신 내역</span>입니다.</p>-->
<!--            <br>-->
            <p><button class="popQna btn btn-border-purple wide">문의하기</button></p>
        </div>
    </div>
</div>

<div class="qna_list">
<?if(count($qna_list) < 1 ){?>
    <div class="box qna_area no-before">
        <div class="box-in qna" style="text-align: center;padding: 20px;">
            <div class="qna_question">
            등록된 1:1문의가 없습니다.
            </div>
        </div>
    </div>
<?}else{?>

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

                    <div class="a_top"> <i class="icon_answer"></i> 답변 </div>
                    <div class="a_cont"> <?=$r['bq_answer_content']?> </div>
                    <div class="a_btm"><span class="writer"><?=$r['bq_last_writer']?></span> <em class="no_font date"><?=view_date_format($r['bq_answerdatetime'])?></em> </div>
                </div>
                <?}?>


            </div>
        </div>

    <? } ?>

<?}?>
</div>
<input type="hidden" name="page" value="2" title="페이지" />
<input type="hidden" name="more" value="<? if($req['page'] == $total_page){?>0<?} else{?>1<? } ?>" title="리스트가 더 있는지 여부" />

<script type="text/javascript">

    $(function(){

        if(isApp == 'Y'){
            var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
            $('.qna_list').css({'min-height': min_height+'px' , 'background-color' : '#f0f0f0'});
        }else{
            $('#container').css({'background-color' : '#f0f0f0'});
        }

        $('.popQna').on('click',function(){

            var container = $('<div>');

            $(container).load('<?php echo $this->page_link->insert_pop; ?>');

            modalPop.createPop('<a class="sig_col">문의등록</a>', container);
            modalPop.createButton('등록', 'btn btn-default btn-sm wide', function(){
                qna_frm_submit();
            });
            modalPop.createCloseButton('취소', 'btn btn-gray btn-sm wide');
            modalPop.show({backdrop:'static'});

        });

    });

    /* scrolling paging */
    var ajax_on  = false;
    var obj_name = 'qna_list';
    $(window).scroll(function(){

        var more = $('input[name="more"]').val();

        if(more == 0) return false; //리스트 end
        if(ajax_on == true ) return false; //ajax 중인경우 return

        ajax_on = true;

        var x = parseInt($(this).scrollTop());
        var h = parseInt($('body').height()) - 200;
        var chkH =  parseInt($(window).outerHeight(true)) ;

        if( h < x +chkH ) ajaxPaging();

        ajax_on = false;

    });

    function ajaxPaging(b = false){

        var p = $('input[name="page"]').val();
        isShowLoader = false;

        $.ajax({
            url : '<?=$this->page_link->list_ajax?>',
            data : {page : p },
            type : 'post',
            async : false,
            dataType : 'html',
            success : function(result) {
                if(b == true) $('.'+obj_name).html(result);
                else $('.'+obj_name).append(result);
                $('input[name="page"]').val(parseInt(p) + 1);
           }

        });

    };
    /* end of scrolling paging */

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
                    location.reload();
                }
            },
            complete : function() {

            }

        });

    }

</script>