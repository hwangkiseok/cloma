<style>
    .table th { background-color:#f5f5f5; }
</style>

<div class="table-responsive">
    <table class="table table-hover table-bordered <?php if( $req['sort_yn'] == "Y") { ?>dataTable<?php } ?>" style="font-size: 11px;">
        <thead>
        <tr role="row" class="active">
            <th rowspan="2" class="<?php echo $sort_array['t_date'][1];?>" onclick="form_submit('sort_field=t_date&sort_type=<?php echo $sort_array['t_date'][0]; ?>');">날짜</th>
            <th rowspan="2" class="<?php echo $sort_array['t_view_app'][1];?>" onclick="form_submit('sort_field=t_view_app&sort_type=<?php echo $sort_array['t_view_app'][0]; ?>');">앱(T_A)<br>/ 앱(T_I)</th>
            <th rowspan="2" class="<?php echo $sort_array['t_uniq_view_app'][1];?>" onclick="form_submit('sort_field=t_uniq_view_app&sort_type=<?php echo $sort_array['t_uniq_view_app'][0]; ?>');">앱(UV_A)<br>/ 앱(UV_I)</th>
            <th rowspan="2" class="<?php echo $sort_array['t_view_web'][1];?>" onclick="form_submit('sort_field=t_view_web&sort_type=<?php echo $sort_array['t_view_web'][0]; ?>');">웹(T)</th>
            <th rowspan="2" class="<?php echo $sort_array['t_uniq_view_web'][1];?>" onclick="form_submit('sort_field=t_uniq_view_web&sort_type=<?php echo $sort_array['t_uniq_view_web'][0]; ?>');">웹(UV)</th>
            <!--<th class="<?php echo $sort_array['t_join'][1];?>" onclick="form_submit('sort_field=t_join&sort_type=<?php echo $sort_array['t_join'][0]; ?>//');">가입</th>-->
            <!--<th class="<?php /*echo $sort_array['t_join_sns'][1];*/?>" onclick="form_submit('sort_field=t_join_sns&sort_type=<?php /*echo $sort_array['t_join_sns'][0]; */?>');">가입(SNS)</th>-->
            <th colspan="4">회원수</th>
            <th rowspan="2" class="<?php echo $sort_array['t_first_buy_match'][1];?>" onclick="form_submit('sort_field=t_join_total&sort_type=<?php echo $sort_array['t_first_buy_match'][0]; ?>');">첫결제</th>
            <th rowspan="2" class="<?php echo $sort_array['t_product_view'][1];?>" onclick="form_submit('sort_field=t_product_view&sort_type=<?php echo $sort_array['t_product_view'][0]; ?>');">상품</th>
            <th rowspan="2" class="<?php echo $sort_array['t_product_view_app'][1];?>" onclick="form_submit('sort_field=t_product_view_app&sort_type=<?php echo $sort_array['t_product_view_app'][0]; ?>');">상품(앱A)<br>/ 상품(앱I)</th>
            <th rowspan="2" class="<?php echo $sort_array['t_product_view_web'][1];?>" onclick="form_submit('sort_field=t_product_view_web&sort_type=<?php echo $sort_array['t_product_view_web'][0]; ?>');">상품(웹)</th>
            <th rowspan="2" class="<?php echo $sort_array['t_product_click'][1];?>" onclick="form_submit('sort_field=t_product_click&sort_type=<?php echo $sort_array['t_product_click'][0]; ?>');">구매</th>
            <th rowspan="2" class="<?php echo $sort_array['t_product_click_app'][1];?>" onclick="form_submit('sort_field=t_product_click_app&sort_type=<?php echo $sort_array['t_product_click_app'][0]; ?>');">구매(앱A)<br>/ 구매(앱I)</th>
            <th rowspan="2" class="<?php echo $sort_array['t_product_click_web'][1];?>" onclick="form_submit('sort_field=t_product_click_web&sort_type=<?php echo $sort_array['t_product_click_web'][0]; ?>');">구매(웹)</th>
            <th rowspan="2" class="<?php echo $sort_array['t_order'][1];?>" onclick="form_submit('sort_field=t_order&sort_type=<?php echo $sort_array['t_order'][0]; ?>');">주문</th>
            <th rowspan="2" class="<?php echo $sort_array['t_order_app'][1];?>" onclick="form_submit('sort_field=t_order_app&sort_type=<?php echo $sort_array['t_order_app'][0]; ?>');">주문(앱A)<br>/ 주문(앱I)</th>
            <th rowspan="2" class="<?php echo $sort_array['t_order_web'][1];?>" onclick="form_submit('sort_field=t_order_web&sort_type=<?php echo $sort_array['t_order_web'][0]; ?>');">주문(웹)</th>
            <th rowspan="2" class="<?php echo $sort_array['t_product_wish'][1];?>" onclick="form_submit('sort_field=t_product_wish&sort_type=<?php echo $sort_array['t_product_wish'][0]; ?>');">찜</th>
            <th rowspan="2" class="<?php echo $sort_array['t_product_share'][1];?>" onclick="form_submit('sort_field=t_product_share&sort_type=<?php echo $sort_array['t_product_share'][0]; ?>');">공유</th>
            <th rowspan="2" class="<?php echo $sort_array['t_qna'][1];?>" onclick="form_submit('sort_field=t_qna&sort_type=<?php echo $sort_array['t_qna'][0]; ?>');">1:1</th>
            <th rowspan="2" class="<?php echo $sort_array['t_attend'][1];?>" onclick="form_submit('sort_field=t_attend&sort_type=<?php echo $sort_array['t_attend'][0]; ?>');">출석</th>
            <th rowspan="2" class="<?php echo $sort_array['t_attend_accrue'][1];?>" onclick="form_submit('sort_field=t_attend_accrue&sort_type=<?php echo $sort_array['t_attend_accrue'][0]; ?>');">연속출석</th>
            <th rowspan="2" class="<?php echo $sort_array['t_attend_winner'][1];?>" onclick="form_submit('sort_field=t_attend_winner&sort_type=<?php echo $sort_array['t_attend_winner'][0]; ?>');">출석달성</th>

            <th rowspan="2" class="<?php echo $sort_array['t_attend_winner'][1];?>" onclick="form_submit('sort_field=t_review_cnt&sort_type=<?php echo $sort_array['t_review_cnt'][0]; ?>');">리뷰<br>텍스트 / 이미지</th>

            <!--<th class="<?php echo $sort_array['t_everyday'][1];?>" onclick="form_submit('sort_field=t_everyday&sort_type=<?php echo $sort_array['t_everyday'][0]; ?>//');">매일응모</th>-->
        </tr>
        <tr>
            <th>총가입</th>
            <th title="(총가입대비%)">임시</th>
            <th title="(총가입대비%)">정상</th>
            <th>탈퇴</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($stat_list as $row) {
        ?>

        <tr role="row">
            <td><?php echo get_date_format($row->t_date, "-"); ?> (<?php echo $row->week; ?>)</td>
            <td>
                <?php echo number_format($row->t_view_app); ?> <br>
                / <?php echo number_format($row->t_view_app_i); ?>
            </td>
            <td>
                <?php echo number_format($row->t_uniq_view_app); ?> <br>
                / <?php echo number_format($row->t_uniq_view_app_i); ?>
            </td>
            <td><?php echo number_format($row->t_view_web); ?></td>
            <td><?php echo number_format($row->t_uniq_view_web); ?></td>
            <!--<td><?php /*echo number_format($row->t_join); */?></td>-->
            <!--<td><?php /*echo number_format($row->t_join_sns); */?></td>-->
            <td><?php echo number_format($row->t_join_total); ?></td>
            <td>
                <?php echo number_format($row->t_join_tmp); ?>
                <br>
                (<?=($row->t_join_total) ? number_format($row->t_join_tmp / $row->t_join_total * 100, 2) : "0.00";?>%)
            </td>
            <td>
                <?php echo number_format($row->t_join_1); ?>
                <br>
                (<?=($row->t_join_total) ? number_format($row->t_join_1 / $row->t_join_total * 100, 2) : "0.00";?>%)
            </td>
            <td><?php echo number_format($row->t_join_del); ?></td>
            <td>
<!--                --><?php //echo number_format($row->t_first_buy_match); ?><!--(--><?php //echo number_format($row->t_first_buy_match_coupon); ?><!--)<br>-->
<!--                --><?php //echo number_format(($row->t_first_buy_match/$row->t_join_total) * 100 ,2)?><!--% (--><?php //echo number_format(($row->t_first_buy_match_coupon/$row->t_join_total) * 100 ,2)?><!--%)-->
                <?php echo number_format($row->t_first_buy_match); ?>(<?php echo $row->t_first_buy_match ? number_format(($row->t_first_buy_match/$row->t_join_total) * 100 ,2) : 0 ?>%)<br>
            </td>
            <td><?php echo number_format($row->t_product_view); ?></td>
            <td>
                <?php echo number_format($row->t_product_view_app); ?><br>
                / <?php echo number_format($row->t_product_view_app_i); ?>
            </td>
            <td><?php echo number_format($row->t_product_view_web); ?></td>
            <td><?php echo number_format($row->t_product_click); ?></td>
            <td>
                <?php echo number_format($row->t_product_click_app); ?> <br>
                / <?php echo number_format($row->t_product_click_app_i); ?>
            </td>
            <td><?php echo number_format($row->t_product_click_web); ?></td>
            <td><?php echo number_format($row->t_order); ?></td>
            <td>
                <?php echo number_format($row->t_order_app); ?> <br>
                / <?php echo number_format($row->t_order_app_i); ?>
            </td>
            <td><?php echo number_format($row->t_order_web); ?></td>
            <td><?php echo number_format($row->t_product_wish); ?></td>
            <td><?php echo number_format($row->t_product_share); ?></td>
            <td><?php echo number_format($row->t_qna); ?></td>
            <td><?php echo number_format($row->t_attend); ?></td>
            <td><?php echo number_format($row->t_attend_accrue); ?></td>
            <td><?php echo number_format($row->t_attend_winner); ?></td>
            <td><?php echo number_format($row->t_review_cnt); ?> / <?php echo number_format($row->t_img_review_cnt); ?></td>
<!--            <td>--><?php //echo number_format($row->t_everyday); ?><!--</td>-->
        </tr>

        <?php
        }//end of foreach()
        ?>

        <tr role="row" style="background:#f3f3f3;">
            <td>합계</td>
            <td><?php echo number_format($count_array['t_view_app']); ?></td>
            <td><?php echo number_format($count_array['t_uniq_view_app']); ?></td>
            <td><?php echo number_format($count_array['t_view_web']); ?></td>
            <td><?php echo number_format($count_array['t_uniq_view_web']); ?></td>
            <!--td><?php /*echo number_format($count_array['t_join']); */?></td>-->
            <!--<td><?php /*echo number_format($count_array['t_join_sns']); */?></td>-->
            <td><?php echo number_format($count_array['t_join_total']); ?></td>
            <td><?php echo number_format($count_array['t_join_tmp']); ?></td>
            <td><?php echo number_format($count_array['t_join_1']); ?></td>
            <td><?php echo number_format($count_array['t_join_del']); ?></td>
            <td><?php echo number_format($count_array['t_first_buy_match']); ?>(<?php echo number_format($count_array['t_first_buy_match_coupon']); ?>)</td>
            <td><?php echo number_format($count_array['t_product_view']); ?></td>
            <td><?php echo number_format($count_array['t_product_view_app']); ?></td>
            <td><?php echo number_format($count_array['t_product_view_web']); ?></td>
            <td><?php echo number_format($count_array['t_product_click']); ?></td>
            <td><?php echo number_format($count_array['t_product_click_app']); ?></td>
            <td><?php echo number_format($count_array['t_product_click_web']); ?></td>
            <td><?php echo number_format($count_array['t_order']); ?></td>
            <td><?php echo number_format($count_array['t_order_app']); ?></td>
            <td><?php echo number_format($count_array['t_order_web']); ?></td>
            <td><?php echo number_format($count_array['t_product_wish']); ?></td>
            <td><?php echo number_format($count_array['t_product_share']); ?></td>
            <td><?php echo number_format($count_array['t_qna']); ?></td>
            <td><?php echo number_format($count_array['t_attend']); ?></td>
            <td><?php echo number_format($count_array['t_attend_accrue']); ?></td>
            <td><?php echo number_format($count_array['t_attend_winner']); ?></td>
            <td><?php echo number_format($count_array['t_review_cnt']); ?> / <?php echo number_format($count_array['t_img_review_cnt']); ?></td>
<!--            <td>--><?php //echo number_format($count_array['t_everyday']); ?><!--</td>-->
        </tr>
        </tbody>
    </table>
</div>

<script>
    <?php if( $req['sort_yn'] != "Y" ) { ?>
    function form_submit() {
        return false;
    }
    <?php } ?>

    //document.ready
    $(function(){
        <?php if( $req['sort_yn'] != "Y" ) { ?>
         $('tr').on('click', function(e){
             e.preventDefault();
             return false;
         });
        <?php } ?>
    });//end of document.ready
</script>