<div class="table-responsive">

    <table class="table table-hover table-bordered <?php if( $req['rctly'] != "Y" ) { ?>dataTable<?php } ?>">
        <thead>
        <tr role="row" class="active">
            <th><input type="checkbox" id="all_chk"></th>
            <th style="width:40px;">No.</th>
            <th style="width:150px;" class="<?php echo $sort_array['table_num_name'][1];?>" onclick="form_submit('sort_field=table_num_name&sort_type=<?php echo $sort_array['table_num_name'][0]; ?>');">리뷰확인</th>
            <th>리뷰내용</th>
            <th style="min-width: 100px">추천상태</th>
            <th style="width:75px;" class="<?php echo $sort_array['re_name'][1];?>" onclick="form_submit('sort_field=re_name&sort_type=<?php echo $sort_array['re_name'][0]; ?>');">작성자</th>
            <th style="width:75px;" class="<?php echo $sort_array['m_order_count'][1];?>" onclick="form_submit('sort_field=m_order_count&sort_type=<?php echo $sort_array['m_order_count'][0]; ?>');">주문내역</th>
            <th style="width:75px;" class="<?php echo $sort_array['re_blind'][1];?>" onclick="form_submit('sort_field=re_blind&sort_type=<?php echo $sort_array['re_blind'][0]; ?>');">참고</th><?/*블라인드*/?>
            <th style="width:75px;" class="<?php echo $sort_array['re_recommend'][1];?>" onclick="form_submit('sort_field=re_recommend&sort_type=<?php echo $sort_array['re_recommend'][0]; ?>');">메인노출</th><?/*블라인드*/?>
            <th style="width:75px;" class="<?php echo $sort_array['re_display_state'][1];?>" onclick="form_submit('sort_field=re_display_state&sort_type=<?php echo $sort_array['re_display_state'][0]; ?>');">전체공개</th>
            <th style="width:75px;">적립금</th>
            <th style="width:140px;" class="<?php echo $sort_array['re_regdatetime'][1];?>" onclick="form_submit('sort_field=re_regdatetime&sort_type=<?php echo $sort_array['re_regdatetime'][0]; ?>');">등록일시</th>
            <th style="width:60px;">관리</th>
        </tr>
        </thead>
        <tbody>

        <?php

        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($review_list as $row) {
            $aImg = json_decode($row->re_img);
        ?>
            <tr role="row" class="cmt-item" data-flag_set="<?=$row->re_blind.'||'.$row->re_display_state.'||'.$row->re_recommend?>" >
                <td><input type="checkbox" id="re_list_<?php echo $row->re_num; ?>" name="re_list" value="<?php echo $row->re_num;; ?>" img-cnt="<?php echo count($aImg); ?>" reward_yn="<?php echo $row->re_reward; ?>" <?php echo $row->re_reward == 'Y' ? 'disabled' : ''; ?>></td>
                <td><?php echo number_format($list_number); ?></td>
                <td>
                    <a href="#none" onclick="new_win_open('/review/list/?tb=review&tb_num=<?php echo $row->re_table_num; ?>&view_type=simple&pop=1', 'review_list_win', 1200, 800); "><?php echo $row->table_num_name; ?></a><br>
                    <!--<a href="#none" onclick="new_win_open('<?php echo $this->config->item('product', "comment_table_detail_url") . $row->re_table_num; ?>', '', '1000', '800');">[상품상세보기]</a>-->
                </td>
                <td class="comm" style="text-align:left;<?php if($row->re_admin == 'Y') { ?>color:#0000ff;<?php }//endif; ?>">
                    <?if(count($aImg) > 0){?>
                        <? foreach ($aImg as $val) {?>
                            <a href="#none" onclick="new_win_open('<?=$val?>', 'img_pop', 800, 600);" style="display: inline-block;" class="thumbnail">
                                <img src="<?=$val?>" width="100" alt="">
                            </a>
                        <?}?>
                        <br>
                    <?}?>
                    <?php echo nl2br($row->re_content); ?>
                </td>
                <td>
                    <?if($row->re_grade == 'A'){?>
                        완전 추천해요!
                    <?}else if($row->re_grade == 'B'){?>
                        추천해요!
                    <?}else if($row->re_grade == 'C'){?>
                        아쉬워요
                    <?}?>
                </td>
                <td>
                    <a href="#none" onclick="member_update_pop('<?php echo $row->re_member_num; ?>');">
                        <?
                        if( $row->re_admin == "Y" ) {
                            echo $row->re_name;
                        } else {
                            if($row->m_nickname){
                                echo $row->m_nickname;
                            }else{
                                echo '[닉네임없음]';
                            }

                        }
                        ?>
                    </a>
                </td>

                <td>
                    <?
                    $returnUrl = '/totalAdmin/_attach/_order.list.php?autocomplete_o_ohp='.$row->m_authno.'&m_key='.$row->m_key;
                    $returnUrl = seed_encrypt($returnUrl);
                    if($row->m_key != ''){ ?>
                        <a role="button" class="btn btn-info btn-xs" onclick="window.open('<?=$this->config->item("order_site_http");?>/api/auto_login.php?data=<?=$returnUrl.'&referer='.urlencode($this->config->item("site_http"))?>');" target="_blank">주문내역</a>
                    <?}else{?>
                        <a role="button" class="btn btn-info btn-xs" onclick="alert('필수입력정보 누락 [m_key]');">주문내역</a>
                    <? } ?>
                </td>

                <td>
                    <?if($row->re_blind == 'N'){?>
                        <button class="btn btn-success btn-xs" onclick="setReviewFlag('re_blind','Y','<?=$row->re_num?>',this);" >정상</button>
                    <?}else{?>
                        <button class="btn btn-danger btn-xs" onclick="setReviewFlag('re_blind','N','<?=$row->re_num?>',this);" >참고</button>
                    <?}?>
                </td>
                <td>
                    <?if($row->re_recommend == 'Y'){?>
                        <button class="btn btn-success btn-xs" onclick="setReviewFlag('re_recommend','N','<?=$row->re_num?>',this);" >메인</button>
                    <?}else{?>
                        <button class="btn btn-danger btn-xs" onclick="setReviewFlag('re_recommend','Y','<?=$row->re_num?>',this);" >일반</button>
                    <?}?>
                </td>

                <td>
                    <?if($row->re_display_state == 'Y'){?>
                        <button class="btn btn-success btn-xs" onclick="setReviewFlag('re_display_state','N','<?=$row->re_num?>',this);" >공개</button>
                    <?}else{?>
                        <button class="btn btn-danger btn-xs" onclick="setReviewFlag('re_display_state','Y','<?=$row->re_num?>',this);" >비공개</button>
                    <?}?>
                </td>
                <td id="point_givent_yn_<?php echo $row->re_num; ?>">
                    <?php if($row->re_reward == 'Y') echo "지급"; else echo "미지급"; ?>
                </td>
                <td><?php echo get_datetime_format($row->re_regdatetime); ?></td>

                <?php if( empty($req['view_type']) ) { ?>
                    <td>
                        <button type="button" class="btn btn-primary btn-xs" style="width:50px;" onclick="review_update_pop('<?php echo $row->re_num; ?>', '<?php echo $row->re_admin; ?>')">수정</button><br />
                    </td>
                <?php } ?>
            </tr>

            <?php
            $list_number--;
        }//end of foreach()
        ?>
        </tbody>
    </table>
</div>
<script>

    $(document).ready(function () {

        $("#all_chk").on('click', function() {

            //$('input[name="re_list"]').prop('checked', this.checked);
            if($(this).prop("checked") == true) {

                $("input[name='re_list']").each(function() {

                    if ($(this).attr('reward_yn') != 'Y') {
                        $('#re_list_' + $(this).val()).prop("checked", true);
                    }
                });

            } else {
                $('input[name="re_list"]').prop("checked",false);
            }
        });
    });

    // 적립금 일괄 적용
    function batch_apply_point() {

        var reward_type = $('input[name="sel_reward_type"]:checked').val(); // 3: 텍스트, 4: 포토

        var lists = new Array();
        var reward_txt = ''; // alert창의 적립금 타입명
        var photo_cnt = 0; // 포토리뷰 카운트
        var text_cnt = 0; // 텍스트리뷰 카운트
        var i = 0;

        $("input[name='re_list']:checked").each(function() {
            //lists.push($(this).val());

            var img_cnt = parseInt($(this).attr('img-cnt'));

            if(img_cnt == 0) {
                text_cnt++;
            }

            if(img_cnt > 0) {
                photo_cnt++;
            }

            if($(this).attr('reward_yn') != 'Y') {
                lists[i] = $(this).val();
            }

            i++;
        });

        if(reward_type == '3') { // 텍스트

            reward_txt = '텍스트';
            if(photo_cnt > 0) {
                alert('이미지리뷰가 포함되어 있습니다. 다시선택해 주세요.');
                lists =  '';

                return false;
            }

        } else if(reward_type == '4') { // 이미지

            reward_txt = '이미지';
            if(text_cnt > 0) {
                alert('텍스트리뷰가 포함되어 있습니다. 다시선택해 주세요.');
                lists =  '';

                return false;
            }
        }

        var res = confirm('선택한 ' + reward_txt + ' 리뷰에 적립금을 적용하시겠습니까?');
        if(res) {
            $.ajax({
                url : '/point/batchReviewApply',
                data : { re_list : lists, re_type : reward_type },
                type : 'post',
                dataType : 'json',
                success : function(result) {
                    $('#search_form').submit();
                    alert(result.msg);
                },
                complete : function() {

                }
            });
        } else {
            return false;
        }
    }
</script>
<?php if( $req['rctly'] != "Y" ) { ?>

    <div class="row text-center">
        <?php echo $pagination; ?>
    </div>

<?php } ?>