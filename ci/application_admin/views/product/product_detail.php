<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품관리 > 상세</h4>
    </div>

    <div class="row" style="background:#fff;">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">단축URL</label>
                        <div class="col-sm-10">
                            <div class="form-control-static"><a href="<?php echo $product_row['p_short_url']; ?>" target="_blank"><?php echo $product_row['p_short_url']; ?></a></div>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">앱링크(웹)</label>
                        <div class="col-sm-10">
                            <div class="form-control-static"><a href="<?php echo $product_row['p_app_link_url_2']; ?>" target="_blank"><?php echo $product_row['p_app_link_url_2']; ?></a></div>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">앱링크(마켓)</label>
                        <div class="col-sm-10">
                            <div class="form-control-static"><a href="<?php echo $product_row['p_app_link_url']; ?>" target="_blank"><?php echo $product_row['p_app_link_url']; ?></a></div>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">상품카테고리</label>
                        <div class="col-sm-10">
                            <div class="form-control-static" style="padding-top: 0;padding-bottom: 0;"><?php echo implode(" &gt; ", array_filter(array($product_row['p_cate1'], $product_row['p_cate2'], $product_row['p_cate3']))); ?></div>
                        </div>
                    </div>
                    <!--
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">구분 </label>
                        <div class="col-sm-10">
                            <p><?php echo get_config_item_text($product_row['p_category'], 'product_category'); ?></p>
                        </div>
                    </div>
                    -->
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">기간한정 </label>
                        <div class="col-sm-10">
                            <p><?php echo get_config_item_text($product_row['p_termlimit_yn'], 'product_termlimit_yn'); ?></p>
                            <?php if($product_row['p_termlimit_yn'] == 'Y') { ?>
                            <p><?php echo get_date_format($product_row['p_termlimit_datetime1'], "-"); ?> ~ <?php echo get_date_format($product_row['p_termlimit_datetime2'], "-"); ?></p>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">진열상태 </label>
                        <div class="col-sm-10">
                            <p><?php echo get_config_item_text($product_row['p_display_state'], 'product_display_state'); ?></p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">판매상태</label>
                        <div class="col-sm-10">
                            <p><?php echo get_config_item_text($product_row['p_sale_state'], 'product_sale_state'); ?></p>
                        </div>
                    </div>

                    <hr >

                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">아이콘 1차</label>
                        <div class="col-sm-10">
                            <p>
                            <?php
                            $empty_check = true;
                            foreach($this->config->item('product_display_info1') as $name => $text) {
                                if ($product_row->p_display_info_array->{$name} == 'Y') {
                                    echo $text . " | ";
                                    $empty_check = false;
                                }
                            }//end of foreach()

                            if( $empty_check ) {
                                echo "해당없음";
                            }
                            ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">아이콘 2차</label>
                        <div class="col-sm-10">
                            <p>
                            <?php echo get_config_item_text($product_row['p_deliveryprice_type'], 'product_deliveryprice_type'); ?>
                            &nbsp;&nbsp;&nbsp;
                            <?php
                            foreach($this->config->item('product_display_info4') as $name => $text) {
                                if ( $product_row['p_display_info_array'][$name] == 'Y' ) {
                                    echo $text . " | ";
                                }
                            }//end of foreach()
                            ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">특가상품</label>
                        <div class="col-sm-10">
                            <p>
                            <?php
                            foreach($this->config->item('product_display_info3') as $name => $text) {
                                if ( $product_row['p_display_info_array'][$name] == 'Y' ) {
                                    echo $text . " | ";
                                }
                            }//end of foreach()
                            ?>
                                &nbsp;
                            </p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">추가노출</label>
                        <div class="col-sm-10">
                            <p>
                            <?php
                            foreach($this->config->item('product_md_division') as $name => $text) {
                                if (isset($product_md_division_array[$name]) && $product_md_division_array[$name] == 'Y') {
                                    echo $text . " | ";
                                }
                            }//end of foreach()
                            ?>
                                &nbsp;
                            </p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">관심수</label>
                        <div class="col-sm-10">
                            <p>
                                총 <?php echo $product_row['p_wish_count']; ?> 개
                                <?php if ($product_row['p_wish_raise_yn'] == 'Y') { echo $product_row['p_wish_raise_count']; } ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">공유수</label>
                        <div class="col-sm-10">
                            <p>
                                총 <?php echo $product_row['p_share_count']; ?> 개
                                <?php if ($product_row['p_share_raise_yn'] == 'Y') { echo $product_row['p_share_raise_count']; } ?>
                            </p>
                        </div>
                    </div>

                    <hr />
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">해시태그 </label>
                        <div class="col-sm-10">
                            <p><?php echo $product_row['p_hash']?$product_row['p_hash']:'-'; ?></p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">상품명 </label>
                        <div class="col-sm-10">
                            <p><?php echo $product_row['p_name']; ?></p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">상세설명</label>
                        <div class="col-sm-10">
                            <p><?php echo $product_row['p_detail']; ?></p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">상품코드 </label>
                        <div class="col-sm-10">
                            <p><?php echo $product_row['p_order_code']; ?></p>
                        </div>
                    </div>

                    <hr />

                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">공급가격(원가)</label>
                        <div class="col-sm-4">
                            <p><?php echo number_format($product_row['p_supply_price']); ?> 원</p>
                        </div>
                        <label class="col-sm-2 control-label">할인율</label>
                        <div class="col-sm-4">
                            <p><?php echo number_format($product_row['p_discount_rate']); ?> %</p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">기존가격</label>
                        <div class="col-sm-4">
                            <p><?php echo number_format($product_row['p_original_price']); ?> 원</p>
                        </div>
                        <label class="col-sm-2 control-label">판매마진</label>
                        <div class="col-sm-4">
                            <p><?php echo number_format($product_row['p_margin_price']); ?> 원</p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">판매가격 </label>
                        <div class="col-sm-4">
                            <p><?php echo number_format($product_row['p_sale_price']); ?> 원</p>
                        </div>
                        <label class="col-sm-2 control-label">마진율</label>
                        <div class="col-sm-4">
                            <p><?php echo $product_row['p_margin_rate']; ?> %</p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">과세여부</label>
                        <div class="col-sm-10">
                            <p><?php echo get_config_item_text($product_row['p_taxation'], 'product_taxation'); ?></p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">원산지</label>
                        <div class="col-sm-10">
                            <p><?php echo $product_row['p_origin']?$product_row['p_origin']:'-'; ?></p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">제조사</label>
                        <div class="col-sm-10">
                            <p><?php echo $product_row['p_manufacturer']?$product_row['p_manufacturer']:'-'; ?></p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">공급사</label>
                        <div class="col-sm-10">
                            <p><?php echo $product_row['p_supplier']?$product_row['p_supplier']:'-'; ?></p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">배송비설정</label>
                        <div class="col-sm-10">
                            <p><?php echo number_format($product_row['p_deliveryprice']); ?> 원</p>
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">상품이미지</label>
                        <div class="col-sm-10">
                            <p style="border:3px solid #000;"><?php echo create_img_tag_from_json($product_row['p_rep_image'], 1, '100%', '', 'data-type="rep_img"'); ?></p>
                            <?php if( !empty($product_row['p_today_image']) ) { ?>
                            <p style="border:3px solid #000;"><?php echo create_img_tag($product_row['p_today_image'], 1, '100%', '', 'data-type="rep_img"'); ?></p>
                            <?php } ?>
                            <?php
                            $no = 0;
                            foreach ( $product_detail_image_array as $key => $item ) {
                            $no = $key + 1;
                            ?>

                            <p><?php echo create_img_tag_from_json(json_encode_no_slashes($item), 1, '100%', '', 'data-type="detail_img"'); ?></p>
                            <?php
                            }//end of foreach()
                            ?>
                        </div>
                    </div>

                    <hr />

                    <div class="clearfix"></div>

                    <!-- 댓글목록 -->
                    <div class="col-xs-12">
                        <div style="color:#fff;background:#000;display:block;font-size:13px;font-weight:bold;padding:10px;overflow:hidden;">
                            <div class="pull-left" style="padding:7px;">댓글 목록</div>
                        </div>
                        <div id="comment_list" style="color:#000;margin-bottom:30px;"></div>
                    </div>
                    <!-- // 댓글목록 -->

                    <div class="clearfix"></div>

                    <?php if( empty($req['pop']) ) { ?>

                    <div class="form-group form-group-sm">
                        <div class="col-sm-offset-2 col-sm-10 col-xs-12">
                            <div class="pull-right">
                                <a href="<?echo $list_url; ?>" id="btn_list" class="btn btn-default btn-sm mgr5">목록보기</a>
                            </div>
                        </div>
                    </div>

                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * 댓글 목록
     */
    function get_comment_list() {
        $('#comment_list').load('/comment/list_ajax/?tb=product&tb_num=<?php echo $product_row->p_num; ?>&view_type=simple');
    }//end of get_comment_list()

    
    $(function(){
        //댓글 목록 출력
        get_comment_list();

        //ajax page
        $(document).on('click', '#comment_list .pagination.ajax a', function(e){
            e.preventDefault();
            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#comment_list').load($(this).attr('href'));
        });
    });
</script>