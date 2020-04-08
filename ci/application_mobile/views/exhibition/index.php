
<style>

    .exhibition_list {_padding: 8px 16px!important;}
    .exhibition_list .tit_area {}
    .exhibition_list .tit_area .date{color: #aaa;}
    .exhibition_list .go_btn {position: absolute ;right: 0;top: 8px;}
    .exhibition_list .go_btn a {border-radius: 8px;}
</style>

<?if(count($aExhibition) > 0) {?>

    <? foreach ($aExhibition as $r) {?>


        <div class="box exhibition_wrap" role="button" data-seq="<?=$r['seq']?>">
            <div class="exhibition_list box-in">

                <img src="<?=$r['banner_img']?>" width="100%" alt="<?=$r['thema_name']?>" />
                <div style="position: relative;margin: 16px 0 8px 0 ;">
                    <span><b><?=$r['thema_name']?></b></span><br>
                    <span class="tit_area">
                    <?if( $r['view_type'] == 'B' ) {?>
                        <p>상시 기획전</p>
                    <?}else{?>
                        <p class="date"><em class="no_font"><?=view_date_format($r['start_date'])?></em> ~ <em class="no_font"><?=view_date_format($r['end_date'])?></em></p>
                    <?}?>
                    </span>
                        <span class="go_btn">
                        <a role="button" class="btn btn-border-red">상품목록보기</a>
                    </span>
                </div>

            </div>

        </div>

    <? } ?>

<? } ?>

<script type="text/javascript">

    $(function(){

        $('.exhibition_wrap').on('click',function () {

            var seq = $(this).data('seq');
            go_link('/exhibition/list?seq='+seq);

        });

    });


</script>
