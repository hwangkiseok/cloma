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

<style>

    .qna {background: #eff0f4!important;}
    .qna .qna_question2{background:#fff;border-radius: 10px;border: 1px solid #ccc;box-shadow: 3px 3px 3px rgba(0,0,0,0.1);}
    .qna .qna_question2 {padding: 0!important;}
    .qna .qna_question2>div {padding: 16px;}

    .qna .qna_question2 .q_top {border-bottom: 1px solid #ddd;}
    .qna .qna_question2 .q_top .btn {padding: 2px;font-size: 12px;margin-left: 10px;}
    .qna .qna_question2 .q_img {padding-top: 0!important;padding-bottom: 0!important;}
    .qna .qna_question2 .q_img span{width: 70px;height: 70px;overflow: hidden;display: inline-block}

    .qna .qna_question2 .del_wrap {padding-top: 10px!important;;}
    .qna .qna_question2 .del_wrap span{color: #aaa;padding-bottom: 1px;border-bottom: 1px solid #aaa; display: inline-block; text-align: center;}
    .qna .qna_answer {background: #fff;border: 1px solid #aaa;width: 90%; margin: -10px auto 0 auto;padding: 16px;box-shadow: 3px 3px 3px rgba(0,0,0,0.1);}
    .qna .qna_answer .a_cont {padding: 15px 0 ;}
    .qna .qna_answer .a_cont img {width: 100%}
    .qna .qna_answer .a_top .icon_answer { background: url(https://www.cloma.co.kr/images/mb_icon_set_img.png) no-repeat -59px -152px; width: 20px; height: 20px;background-size: 80px!important; display: inline-block;}

    .qna .qna_answer .a_top .close_answer {float: right;
        width: 26px;
        height: 26px;
        vertical-align: top;
        background-size: 96px!important;
        background: url(https://www.cloma.co.kr/images/mb_icon_set_img.png) no-repeat -1px -205px;
    }


    .qna .qna_answer .a_btm .writer {font-weight: bold;}
    .qna .date {font-size: 12px;color:#aaa}





</style>

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

    <? foreach ($qna_list as $k => $r) { $img_arr = json_decode($r['bq_file'],true); ?>

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

<?}?>
</div>

<input type="hidden" name="page" value="2" title="페이지" />
<input type="hidden" name="more" value="<? if($req['page'] == $total_page){?>0<?} else{?>1<? } ?>" title="리스트가 더 있는지 여부" />

<script type="text/javascript">

    $(function(){

        if(isApp == 'Y'){
            var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
            $('.qna_list').css({'min-height': min_height+'px' , 'background-color' : '#eff0f4'});
        }else{
            $('#container').css({'background-color' : '#eff0f4'});
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

    $(document).on('click','.del_qna',function(){

        if(confirm('해당 질문을 삭제하시겠습니까?') == false) return false;

        $('input[name="page"]').val(1);
        var seq = $(this).data('seq');
        $.ajax({
            url : '<?=$this->page_link->delete_proc?>',
            data : {seq : seq },
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if(result.msg) alert(result.msg);
                if(result.success) ajaxPaging(true);
            }

        });

    })

    $(document).on('click','.show_answer',function(){

        $('.qna_answer_warp').hide();

        $(this).parent().parent().parent().find('.qna_answer_warp').show();
        var t_obj = $(this).parent().parent().parent().find('.qna_answer_warp .qna_answer');
        var l = ( parseInt($('.qna_list').width()) - parseInt($(t_obj).width()) - 32  ) / 2 ;

        $('.qna_answer').css('left',l+'px');

    });

    $(document).on('click','.close_answer',function(){
        $('.qna_answer_warp').hide();
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