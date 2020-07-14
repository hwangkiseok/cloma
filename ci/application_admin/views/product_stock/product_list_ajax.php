<style>
    .url_content_wrap { width:100%; padding:0; text-align:right; list-style:none; }
    .url_content_wrap li { margin-top:5px; }
    .url_content_wrap .btn { font-size:11px; }
</style>

<button id="dummyClipboard" data-clipboard-text="" style="display: none;"></button>

<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th><input type="checkbox" id="all_list_check" /></th>
            <th>No.</th>
            <!--<th class="<?php /*echo $sort_array['p_order'][1];*/?>" onclick="form_submit('sort_field=p_order&sort_type=<?php /*echo $sort_array['p_order'][0]; */?>');">순서</th>-->
            <th class="<?php echo $sort_array['p_category'][1];?>" onclick="order_submit('p_category', '<?php echo $sort_array['p_category'][0]; ?>');">카테고리</th>
            <th class="<?php echo $sort_array['p_display_state'][1];?>" onclick="order_submit('p_display_state', '<?php echo $sort_array['p_display_state'][0]; ?>');">진열상태</th>
            <th class="<?php echo $sort_array['p_sale_state'][1];?>" onclick="order_submit('p_sale_state', '<?php echo $sort_array['p_sale_state'][0]; ?>');">판매상태</th>
            <th class="<?php echo $sort_array['p_stock_state'][1];?>" onclick="order_submit('p_stock_state', '<?php echo $sort_array['p_stock_state'][0]; ?>');">재고상태</th>
            <th class="<?php echo $sort_array['p_regdatetime'][1];?>" onclick="order_submit('p_regdatetime', '<?php echo $sort_array['p_regdatetime'][0]; ?>');">등록일</th>
            <th class="<?php echo $sort_array['p_termlimit_yn'][1];?>" onclick="order_submit('p_termlimit_yn', '<?php echo $sort_array['p_termlimit_yn'][0]; ?>');">기간한정</th>
            <th class="<?php echo $sort_array['p_name'][1];?>" onclick="order_submit('p_name', '<?php echo $sort_array['p_name'][0]; ?>');">상품명<br />URL(웹/<span style="color:royalblue" title="앱설치 안됐으면 웹으로 이동">앱링크(웹)</span>/<span style="color:rebeccapurple" title="앱설치 안됐으면 마켓으로 이동">앱링크(마켓)</span>)</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['p_sale_price'][1];?>" onclick="order_submit('p_sale_price', '<?php echo $sort_array['p_sale_price'][0]; ?>');">판매가</th>

            <th>관리</th>

            <th>재고 부족 옵션</th>
            <th>처리상태</th>
            <th>처리일자<br>(처리일시)</th>


        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count['cnt'] - ($list_per_page * ($page-1));

        foreach($product_list as $row) {

            $view_click_rate = "0.00";      //진입:클릭률
            $view_order_rate = "0.00";      //클릭:주문율
            $come_order_rate = "0.00";      //클릭:주문율
            if( $row['p_view_count'] > 0 ) {
                $view_click_rate = number_format(($row['p_click_count'] / $row['p_view_count']) * 100, 2);
                $come_order_rate = number_format(($row['p_order_count'] / $row['p_view_count']) * 100, 2);
            }
            if( $row['p_click_count'] > 0 ) {
                $view_order_rate = number_format(($row['p_order_count'] / $row['p_click_count']) * 100, 2);
            }
        ?>

            <tr role="row">
                <td><input type="checkbox" name="p_num[]" value="<?php echo $row['p_num']; ?>" /></td>
                <td><?php echo number_format($list_number); ?><br />(<?php echo $row['p_num']; ?>)</td>
                <td>
                    <?php echo implode(" &gt; ", array_filter(array($row['p_cate1'], $row['p_cate2'], $row['p_cate3']))); ?>
                </td>
                <td>
                    <?php if( $row['p_display_state'] == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="product_update_toggle('<?php echo $row['p_num']; ?>', 'p_display_state');"><?php echo get_config_item_text($row['p_display_state'], 'product_display_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="product_update_toggle('<?php echo $row['p_num']; ?>', 'p_display_state');"><?php echo get_config_item_text($row['p_display_state'], 'product_display_state', false); ?></button>
                    <?php } ?>
                    <br>
                </td>
                <td>
                    <?php if( $row['p_sale_state'] == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="product_update_toggle('<?php echo $row['p_num']; ?>', 'p_sale_state');"><?php echo get_config_item_text($row['p_sale_state'], 'product_sale_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="product_update_toggle('<?php echo $row['p_num']; ?>', 'p_sale_state');"><?php echo get_config_item_text($row['p_sale_state'], 'product_sale_state', false); ?></button>
                    <?php } ?>
                </td>
                <td>
                    <?php if( $row['p_stock_state'] == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="product_update_toggle('<?php echo $row['p_num']; ?>', 'p_stock_state');"><?php echo get_config_item_text($row['p_stock_state'], 'product_stock_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="product_update_toggle('<?php echo $row['p_num']; ?>', 'p_stock_state');"><?php echo get_config_item_text($row['p_stock_state'], 'product_stock_state', false); ?></button>
                    <?php } ?>
                </td>
                <td><?php echo substr(get_date_format($row['p_regdatetime']), 2, 8); ?></td>
                <td>
                    <?php echo get_config_item_text($row['p_termlimit_yn'], 'product_termlimit_yn'); ?>
                    <?php
                    //판매시작일 ~ 판매종료일시
                    if( $row['p_termlimit_yn'] == "Y" ) {
                        echo "<br />" . substr(get_datetime_format($row['p_termlimit_datetime1']), 2, 14) . " <br />~ " . substr(get_datetime_format($row['p_termlimit_datetime2']), 2, 14);
                    }
                    ?>
                </td>
                <td>
                    <b><a href="#none" style="font-size:13px;" onclick="product_detail_win('<?php echo $row['p_num']; ?>')"><?php echo $row['p_name']; ?></a></b><br />
                    <div class="mgt5">
<!--                        <a href="#none" class="btn btn-default btn-xs" onclick="$('.url_content_wrap').toggle();">단축URL 불러오기</a>-->
                        <a href="#none" class="btn btn-default btn-xs shortUrl-Load">단축URL 불러오기</a>
                        <ul class="url_content_wrap" style="display:none;">
                            <li>
                                일반 ::
                                <a href="#none" class="btn btn-default btn-xs zclipCopy" onclick="get_product_shorten_url('<?=$row['p_num'];?>', '', 1);">WEB</a>
                                <a href="#none" class="btn btn-default btn-xs zclipCopy" onclick="get_product_shorten_url('<?=$row['p_num'];?>', '', 2);">APP(웹)</a>
                                <a href="#none" class="btn btn-default btn-xs zclipCopy" onclick="get_product_shorten_url('<?=$row['p_num'];?>', '', 3);">APP(마켓)</a>
                            </li>

                            <?php
                            foreach($this->config->item("product_ref_site") as $site => $name) {
                                $type_1_html = '<a href="#none" class="btn btn-default btn-xs zclipCopy" onclick="get_product_shorten_url(\'' . $row['p_num'] . '\', \'' . $site . '\', 1);">WEB</a>';
                                $type_2_html = '<a href="#none" class="btn btn-default btn-xs zclipCopy" onclick="get_product_shorten_url(\'' . $row['p_num'] . '\', \'' . $site . '\', 2);">APP(웹)</a>';
                                $type_3_html = '<a href="#none" class="btn btn-default btn-xs zclipCopy" onclick="get_product_shorten_url(\'' . $row['p_num'] . '\', \'' . $site . '\', 3);">APP(마켓)</a>';
                                $type_4_html = '<a href="#none" class="btn btn-default btn-xs zclipCopy" onclick="copy_clipboard(\''.$this->config->item('site_http').'/product/detail/'.$row['p_num'].'/?ref_site='.$site.'\');">원문</a>';

                                $type_check_array = $this->config->item($site, "product_ref_site_type");

                                if( $type_check_array[0] == "N" ) {
                                    $type_1_html = "";
                                }
                                if( $type_check_array[1] == "N" ) {
                                    $type_2_html = "";
                                }
                                if( $type_check_array[2] == "N" ) {
                                    $type_3_html = "";
                                }
                                if( $type_check_array[3] == "N" ) {
                                    $type_4_html = "";
                                }
                                ?>

                                <li>
                                    <?php echo $name; ?> ::
                                    <?php echo $type_1_html; ?>
                                    <?php echo $type_2_html; ?>
                                    <?php echo $type_3_html; ?>
                                    <?php echo $type_4_html; ?>
                                </li>

                            <?php }//endforeach; ?>

                        </ul>
                    </div>
                </td>
                <td><?php echo create_img_tag_from_json($row['p_rep_image'], 1, 100);?></td>
                <td><div><span class="label label-danger" style="font-size:100%;"><?php echo number_format($row['p_sale_price']); ?></span></div></td>
                <td>
                    <a href="/product/update/?p_num=<?php echo $row['p_num']; ?>&<?php echo $GV; ?>" class="btn btn-primary btn-xs" target="_blank">수정</a><br />
                    <a href="#none" class="btn btn-default btn-xs mgt5" onclick="copy_clipboard('<?php echo $this->config->item('site_http'); ?>/product/detail/?p_num=<?php echo $row['p_num']; ?>');">URL복사</a>
                </td>
                <td>
                    <?if($row['issue_yn'] == 'Y'){?>
                        <button class="btn btn-xs btn-warning showIssue" data-p_order_code="<?=$row['p_order_code']?>">있음</button>
                    <?}else{?>
                        <button class="btn btn-xs btn-info">없음</button>
                    <?}?>
                </td>
                <td>
                    <?if($row['proc_yn'] == 'Y'){?>
                        <button class="btn btn-xs btn-primary pop_proc" data-proc_yn="Y" data-p_num="<?=$row['p_num']?>">확인완료</button>
                    <?}else if($row['proc_yn'] == 'I'){?>
                        <button class="btn btn-xs btn-danger pop_proc" data-proc_yn="I" data-p_num="<?=$row['p_num']?>">무시</button>
                    <?}else{?>
                        <button class="btn btn-xs btn-success pop_proc" data-proc_yn="N" data-p_num="<?=$row['p_num']?>">확인필요</button>
                    <?}?>
                </td>
                <td><?=$row['au_name']?><br>(<?=view_date_format($row['mod_date'])?>)</td>
            </tr>

        <?php
            $list_number--;
        }//end of foreach()
        ?>
        </tbody>
    </table>
</div>

<div class="row text-center">
    <?php echo $pagination; ?>
</div>