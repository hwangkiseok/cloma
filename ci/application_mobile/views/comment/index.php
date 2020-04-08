<!--<div class="box">-->
<!--    <div class="box-in">-->
<!--        <div class="page_tit">-->
<!--            <h2> 나의 댓글 </h2>-->
<!--            <p>고객님께서 <span>작성하신 댓글 내역</span>입니다.</p>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<?php link_src_html("/js/comment.js", "js"); ?>
<!-- 코멘트 사용시 -->
<input type="hidden" name="nCommentLists" value="<?=$nCommentLists?>" />
<input type="hidden" name="comment_p" value="2" />
<input type="hidden" name="cmt_table" value="my" />
<input type="hidden" name="obj_name" value="comment_my_wrap" />
<input type="hidden" name="more" value="1" />
<script type="text/javascript"> var cmt_num = '<?=$_SESSION['session_m_num']?>'; </script>
<div class="comment_my_wrap">
<?=$ext_comment['comment_view']?>
</div>

<script>
    $(function(){

        if(isApp == 'Y'){
            var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
            $('.comment_my_wrap').css({'min-height': min_height+'px','background':'#f0f0f0'});
        }

        $('#container').css('background','#f0f0f0');
    })
</script>