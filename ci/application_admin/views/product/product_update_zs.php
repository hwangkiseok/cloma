<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>




<h3 style="padding: 20px 0;text-align: center;">------------- 테스트 페이지 -------------</h3>


<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품관리 > 수정</h4>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">

                    <form name="main_form" id="main_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="p_num" value="<?php echo $product_row->p_num; ?>" />
                        <input type="hidden" name="p_top_desc" value="<?=$product_row->p_top_desc?>" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">단축URL</label>
                            <div class="col-sm-10">
                                <div class="form-control-static"><a href="<?php echo $product_row->p_short_url; ?>" target="_blank"><?php echo $product_row->p_short_url; ?></a></div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">앱링크(웹)</label>
                            <div class="col-sm-10">
                                <div class="form-control-static"><a href="<?php echo $product_row->p_app_link_url_2; ?>" target="_blank"><?php echo $product_row->p_app_link_url_2; ?></a></div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">앱링크(마켓)</label>
                            <div class="col-sm-10">
                                <div class="form-control-static"><a href="<?php echo $product_row->p_app_link_url; ?>" target="_blank"><?php echo $product_row->p_app_link_url; ?></a></div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품카테고리</label>
                            <div class="col-sm-10">
                                <div class="form-control-static"><?php echo implode(" &gt; ", array_filter(array($product_row->p_cate1, $product_row->p_cate2, $product_row->p_cate3))); ?></div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">구분 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_p_category">
                                    <?php echo get_input_radio('p_category', $this->config->item('product_category'), $product_row->p_category); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">기간한정 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10 form-inline">
                                <div id="field_p_termlimit_yn">
                                    <?php echo get_input_radio('p_termlimit_yn', $this->config->item('product_termlimit_yn'), $product_row->p_termlimit_yn, $this->config->item('product_termlimit_yn_text_color')); ?>
                                    <span class="help-inline middle txt-default">* 설정된 기간이 지나면 판매안함으로 상태가 변경됩니다.</span>
                                </div>
                                <div id="termlimit_date" style="display:none;">
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;" id="field_p_termlimit_datetime1">
                                            <input type="text" class="form-control" style="width:100px;" name="p_termlimit_datetime1" value="<?php echo get_date_format($product_row->p_termlimit_datetime1, "-"); ?>" />
                                            <span class="input-group-btn text-left">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left mgl5">
                                        <label class="inline">
                                            <select name="p_termlimit_datetime1_hour" class="form-control">
                                                <?php echo get_select_option_hour("", get_hour_format($product_row->p_termlimit_datetime1)); ?>
                                            </select>
                                            :
                                            <select name="p_termlimit_datetime1_min" class="form-control">
                                                <?php echo get_select_option_min("", get_min_format($product_row->p_termlimit_datetime1)); ?>
                                            </select>
                                            :00
                                        </label>
                                    </div>
                                    <div class="pull-left text-center" style="width:20px;">~</div>
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;" id="field_p_termlimit_datetime2">
                                            <input type="text" class="form-control" style="width:100px;" name="p_termlimit_datetime2" value="<?php echo get_date_format($product_row->p_termlimit_datetime2, "-"); ?>" />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left mgl5">
                                        <label class="inline">
                                            <select name="p_termlimit_datetime2_hour" class="form-control">
                                                <?php echo get_select_option_hour("", get_hour_format($product_row->p_termlimit_datetime2)); ?>
                                            </select>
                                            :
                                            <select name="p_termlimit_datetime2_min" class="form-control">
                                                <?php echo get_select_option_min("", get_min_format($product_row->p_termlimit_datetime2)); ?>
                                            </select>
                                            :59
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">진열상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_p_display_state">
                                    <?php echo get_input_radio('p_display_state', $this->config->item('product_display_state'), $product_row->p_display_state, $this->config->item('product_display_state_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">판매상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_p_sale_state">
                                    <?php echo get_input_radio('p_sale_state', $this->config->item('product_sale_state'), $product_row->p_sale_state, $this->config->item('product_sale_state_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">재고상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_p_stock_state">
                                    <?php echo get_input_radio('p_stock_state', $this->config->item('product_stock_state'), $product_row->p_stock_state, $this->config->item('product_stock_state_text_color')); ?>
                                </div>
                            </div>
                        </div>

                        <hr >

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">아이콘 1차</label>
                            <div class="col-sm-8">
                                <?php
                                $empty_check = 'checked';
                                foreach($this->config->item('product_display_info1') as $name => $text) {
                                    $checked = ($product_row->p_display_info_array->{$name} == 'Y') ? 'checked' : '';
                                    if( $checked == 'checked' ) {
                                        $empty_check = '';
                                    }
                                ?>
                                    <label>
                                        <input type="radio" name="p_display_info[]" value="<?php echo $name; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                                    </label>
                                <?php } ?>
                                <label>
                                    <input type="radio" name="p_display_info[]" value="" <?php echo $empty_check; ?> /> 해당없음
                                </label>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">아이콘 2차</label>
                            <div class="col-sm-8">
                                <?php echo get_input_radio('p_deliveryprice_type', $this->config->item('product_deliveryprice_type'), $product_row->p_deliveryprice_type); ?>
                                &nbsp;&nbsp;&nbsp;
                                <?php
                                foreach($this->config->item('product_display_info4') as $name => $text) {
                                    $checked = ($product_row->p_display_info_array->{$name} == 'Y') ? 'checked' : '';
                                ?>
                                    <label>
                                        <input type="checkbox" name="p_display_info_4[]" value="<?php echo $name; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">특가상품</label>
                            <div class="col-sm-8">
                                <?php
                                foreach($this->config->item('product_display_info3') as $name => $text) {
                                    $checked = ($product_row->p_display_info_array->{$name} == 'Y') ? 'checked' : '';
                                    ?>
                                    <label>
                                        <input type="checkbox" name="p_display_info[]" value="<?php echo $name; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">추가노출</label>
                            <div class="col-sm-8">
                                <?php
                                foreach($this->config->item('product_md_division') as $name => $text) {
                                    $checked = (isset($product_md_division_array[$name]) && $product_md_division_array[$name] == 'Y') ? 'checked' : '';
                                ?>
                                    <label>
                                        <input type="checkbox" name="pmd_division[]" value="<?php echo $name; ?>" <?php echo $checked ; ?> /> <?php echo $text; ?>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">관심수</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="field_p_wish_count" name="p_wish_count" class="form-control" value="<?php echo $product_row->p_wish_count; ?>" numberOnly="true" style="width:100px;" />
                                </div>
                                <div class="pull-left mgl10">
                                    <label>
                                        <input type="checkbox" id="field_p_wish_raise_yn" name="p_wish_raise_yn" value="Y" <?php echo ($product_row->p_wish_raise_yn == 'Y') ? 'checked' : ''; ?> />
                                        1시간마다 증가수
                                    </label>
                                </div>
                                <div class="pull-left mgl10">
                                    <input type="text" id="field_p_wish_raise_count" name="p_wish_raise_count" value="<?php echo $product_row->p_wish_raise_count; ?>" class="form-control"  numberOnly="true" style="width:80px;" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">공유수</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="field_p_share_count" name="p_share_count" class="form-control" value="<?php echo $product_row->p_share_count; ?>" numberOnly="true" style="width:100px;" />
                                </div>
                                <div class="pull-left mgl10">
                                    <label>
                                        <input type="checkbox" id="field_p_share_raise_yn" name="p_share_raise_yn" value="Y" <?php echo ($product_row->p_share_raise_yn == 'Y') ? 'checked' : ''; ?> />
                                        1시간마다 증가수
                                    </label>
                                </div>
                                <div class="pull-left mgl10">
                                    <input type="text" id="field_p_share_raise_count" name="p_share_raise_count" value="<?php echo $product_row->p_share_raise_count; ?>" class="form-control" numberOnly="true" style="width:80px;" />
                                </div>
                            </div>
                        </div>

                        <hr />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품명 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" id="field_p_name" name="p_name" class="form-control" value="<?php echo $product_row->p_name; ?>" />
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">간략설명</label>
                            <div class="col-sm-10">
                                <textarea id="field_p_summary" name="p_summary" class="form-control" style="width:100%; height:60px;"><?php echo $product_row->p_summary; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상단고정이미지</label>
                            <div class="col-sm-10 descImg-top-Ajax">
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상세설명</label>
                            <div class="col-sm-10">
                                <textarea id="field_p_detail" name="p_detail" style="width:100%; height:200px; display:none;"><?php echo $product_row->p_detail; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">공구폼상품코드 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" id="field_p_order_code" name="p_order_code" class="form-control" value="<?php echo $product_row->p_order_code; ?>" />
                            </div>
                        </div>

                        <hr />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">공급가격(원가) <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_supply_price">
                                    <input type="text" name="p_supply_price" class="form-control" value="<?php echo $product_row->p_supply_price; ?>" tabindex="1" />
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                            <label class="col-sm-2 control-label">할인율</label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_discount_rate">
                                    <input type="text" name="p_discount_rate" class="form-control" value="<?php echo $product_row->p_discount_rate; ?>" tabindex="4" readonly="readonly" />
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">기존가격</label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_original_price">
                                    <input type="text" name="p_original_price" class="form-control" value="<?php echo $product_row->p_original_price; ?>" tabindex="2" />
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                            <label class="col-sm-2 control-label">판매마진</label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_margin_price">
                                    <input type="text" name="p_margin_price" class="form-control" value="<?php echo $product_row->p_margin_price; ?>" tabindex="5" readonly="readonly" />
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">판매가격 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_sale_price">
                                    <input type="text" name="p_sale_price" class="form-control" value="<?php echo $product_row->p_sale_price; ?>" tabindex="3" />
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                            <label class="col-sm-2 control-label">마진율</label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_margin_rate">
                                    <input type="text" name="p_margin_rate" class="form-control" value="<?php echo $product_row->p_margin_rate; ?>" tabindex="6" readonly="readonly" />
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">과세여부</label>
                            <div class="col-sm-8">
                                <?php echo get_input_radio('p_taxation', $this->config->item('product_taxation'), $product_row->p_taxation); ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">원산지</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="field_p_origin" name="p_origin" class="form-control" value="<?php echo $product_row->p_origin; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제조사</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="field_p_manufacturer" name="p_manufacturer" class="form-control" value="<?php echo $product_row->p_manufacturer; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">공급사</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="field_p_supplier" name="p_supplier" class="form-control" value="<?php echo $product_row->p_supplier; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">배송비설정</label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_deliveryprice" style="width:150px;">
                                    <input type="text" name="p_deliveryprice" class="form-control" numberOnly="true" value="<?php echo $product_row->p_deliveryprice; ?>" />
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품이미지</label>
                            <div class="col-sm-10">
                                <div class="input-group" id="field_p_rep_image" style="width:100%;">
                                    <span class="input-group-addon" style="width:110px;">대표이미지 <span class="txt-danger">*</span></span>
                                    <input type="file" name="p_rep_image" class="form-control" />
                                </div>
                                <?php if( $this->config->item('product_rep_image_size') ) { ?>
                                    <p class="help-block">*
                                        <?php foreach ($this->config->item('product_rep_image_size') as $item) { ?>
                                            <?php echo $item[0] . " x " . $item[1]; ?>
                                        <?php } ?>
                                        로 리사이징(썸네일 생성) 됩니다.
                                    </p>
                                <?php } ?>
                                <div class="mgt5 mgb10">
                                    <?php echo create_img_tag_from_json($product_row->p_rep_image, 1, '100', '', 'data-type="rep_img"'); ?>
                                </div>

                                <div class="input-group mgt10" id="field_p_today_image" style="width:100%;">
                                    <span class="input-group-addon" style="width:110px">세로형이미지</span>
                                    <input type="file" name="p_today_image" class="form-control" />
                                </div>
                                <p class="help-block">* 300 x 345, 600 x 690 등 (비율에 맞게 업로드)</p>
                                <div class="mgt5 mgb10 pTodayImage">
                                    <?php if( !empty($product_row->p_today_image) ) { ?>
                                    <?php echo create_img_tag($product_row->p_today_image, "", '100', '', 'data-type="rep_img"'); ?>
                                    <a href="#none" onclick="delete_product_image('<?php echo $product_row->p_num; ?>', '1', '');" class="btn btn-danger btn-xs">삭제</a>
                                    <?php } ?>
                                </div>

                                <div class="input-group mgt10" id="field_p_banner_image" style="width:100%;">
                                    <span class="input-group-addon" style="width:110px">배너이미지</span>
                                    <input type="file" name="p_banner_image" class="form-control" />
                                </div>
                                <p class="help-block">* 720 x 300</p>
                                <div class="mgt5 mgb10 pBannerImage">
                                    <?php if( !empty($product_row->p_banner_image) ) { ?>
                                        <?php echo create_img_tag($product_row->p_banner_image, "", '100', '', 'data-type="rep_img"'); ?>
                                        <a href="#none" onclick="delete_product_image('<?php echo $product_row->p_num; ?>', '4', '');" class="btn btn-danger btn-xs">삭제</a>
                                    <?php } ?>
                                </div>

                                <div class="mgt10" id="field_p_detail_image">
                                    <div class="input-group" style="width:100%;">
                                        <span class="input-group-addon" style="width:110px">상세이미지</span>
                                        <input type="file" name="p_detail_image[]" class="form-control" multiple="multiple" />
                                    </div>
                                    <p class="help-block">* 다중 선택 가능 / 이미지 파일(JPG, PNG, GIF)만 가능 / 최대 <?php echo $this->config->item('product_detail_image_max_count'); ?>개, 단일 파일 용량 10MB 미만, 총 용량 100MB 미만 업로드 가능</p>

                                    <p class="text-center"><button type="button" onclick="delete_product_image('<?php echo $product_row->p_num; ?>', '3', '');" class="btn btn-danger btn-xs">모두삭제</button></p>



                                    <div class="mgt10 mgb10" id="detail_image_list" style="border:1px solid #ccc;border-radius:4px;padding:10px;width:100%;overflow:auto;">

                                        <?php
                                        foreach ( $product_detail_image_array as $key => $item ) {
                                            $img_thumb = $item[1];
                                            $img_org = $item[0];
                                            ?>

                                            <div class="col-md-2 col-sm-12 col-xs-12 mgt10 text-center list-item" style="" data-key="<?php echo $key; ?>">
                                                <a href="#none" onclick="new_win_open('<?php echo $img_org; ?>', 'img_pop', 800, 600); " style="font-size:20px;"><i class="fa fa-search-plus"></i></a>
                                                <a href="#none" style="font-size:20px;"><i class="fa fa-sort"></i></a>
                                                <img src="<?php echo $img_thumb; ?>" style="width:200px;" alt="" />
                                                <a href="#none" onclick="delete_product_image('<?php echo $product_row->p_num; ?>', '2', this);" class="btn btn-danger btn-xs">삭제</a>
                                            </div>

                                            <?php
                                        }//end of foreach()
                                        ?>

                                    </div>


                                    <p class="text-center"><button type="button" onclick="delete_product_image('<?php echo $product_row->p_num; ?>', '3', '');" class="btn btn-danger btn-xs mgt10">모두삭제</button></p>
                                </div>

                                <!--<div class="pull-right mgt10">-->
                                <!--    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="add_input_detail_image();">상세이미지 추가</button>-->
                                <!--</div>-->
                            </div>
                        </div>

                        <hr />

                        <div class="clearfix"></div>

                        <div class="form-group form-group-sm">
                            <div class="col-sm-offset-2 col-sm-10 col-xs-12">
                                <button type="button" class="btn btn-danger btn-sm pull-left" onclick="delete_product();">삭제하기</button>
                                <div class="pull-right">
                                    <a href="<?echo $list_url; ?>" id="btn_list" class="btn btn-default btn-sm mgr5">목록보기</a>
                                    <button type="submit" class="btn btn-primary btn-sm">수정완료</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<br />
<br />
<br />

<script>
    var p_num = '<?php echo $product_row->p_num; ?>';
    var this_form_action = '<?php echo $this->page_link->update_proc; ?>';
    var list_url = '<?php echo $list_url; ?>';
    var delete_url = '<?php echo $this->page_link->delete_proc; ?>/?<?php echo $this->input->server('QUERY_STRING'); ?>';
    var detail_image_max_count = '<?php echo $this->config->item('product_detail_image_max_count'); ?>';
</script>

<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>
<script src="/plugins/smarteditor2/js/HuskyEZCreator.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/smarteditor2/js/HuskyEZCreator.js"); ?>" charset="utf-8"></script>
<?php echo link_src_html("/js/page/product.js", "js"); ?>

<script>
    $(function(){
        <?php if( $product_row->p_termlimit_yn == 'Y') { ?>
        $('#termlimit_date').show();
        <?php } ?>



        //sortable
        $('#detail_image_list').sortable({
            revert: true,
            stop : function(event, ui){
                detail_img_order_update(p_num);
            }
        });
        $('#detail_image_list').disableSelection();

        <?/**
         * @date 170809
         * @writer 황기석
         * @desc 반드시 sortable 함수 아래 있을것 / sortable 최장이미지의 세로사이즈를 모든 item에 적용
         */ ?>
        var max_img_height = 0;
        $('.list-item').each(function(){
            var tmp_height = $(this).find('img').height();
            if(max_img_height < tmp_height){
                max_img_height = tmp_height;
            }
        }).promise().done(function () {
            $('.list-item').css('min-height',parseInt(max_img_height,10)+50+'px');
        });
        <?/* END */?>

        listCall();
    });



    function listCall(){

        var p_top_desc = '<?=$product_row->p_top_desc?>';
        var p_btm_desc = '<?=$product_row->p_btm_desc?>';

        $.ajax({
            url : '/Product_desc/product_desc_list_ajax/',
            type : 'post',
            dataType : 'json',
            success : function (result) {

                var html_top = '';
                var html_noImg = '';

                html_noImg += '   <div class="alert alert-danger" role="alert">';
                html_noImg += '       등록된 이미지가 없습니다.';
                html_noImg += '   </div>';

                var top = 0;
                for(var i = 0 ; i < result.data.length ; i++){

                    if(result.data[i].gubun == '1'){

                        html_top += '<div class="col-md-2">';
                        html_top += '   <div class="thumbnail">';
                        html_top += '       <img src="'+result.data[i].url+'">';
                        html_top += '       <div class="caption text-center">';
                        html_top += '           <h5>'+result.data[i].org_name+'</h5>';
                        html_top += '           <p>';
                        html_top += '               <a class="btn btn-danger select-img select-top-Img" p_desc="'+result.data[i].p_desc+'" role="button">선택</a>';
                        html_top += '           </p>';
                        html_top += '       </div>';
                        html_top += '   </div>';
                        html_top += '</div>';
                        top++;

                    }
                }

                if(top > 0){
                    $('.descImg-top-Ajax').html(html_top);
                }else{
                    $('.descImg-top-Ajax').html(html_noImg);
                }

                if(p_top_desc){
                    $(".select-top-Img[p_desc='"+p_top_desc+"']").removeClass('btn-danger').addClass('btn-success').html('<i class="glyphicon glyphicon-ok"></i>선택됨');
                }

            }

        });

    }

    $(document).on('click','.select-img',function(){

        var obj;

        if($(this).hasClass('select-top-Img')){
            obj = $('.select-top-Img');
            inputObj = $('input[name="p_top_desc"]');
        } else if ($(this).hasClass('select-btm-Img')) {
            obj = $('.select-btm-Img');
            inputObj = $('input[name="p_btm_desc"]');
        } else {
            alert('이미지 선택 에러. 새로고침 후 다시시도 해주세요 !');
            return false;
        }

        if($(this).hasClass('btn-danger')){
            /* btn init*/
            $(obj).removeClass('btn-success').addClass('btn-danger');
            $(obj).html('선택');
            /* btn init*/
            $(this).removeClass('btn-danger').addClass('btn-success');
            $(this).html('<i class="glyphicon glyphicon-ok"></i>선택됨');
            $(inputObj).val($(this).attr('p_desc'));
        }else{
            $(this).removeClass('btn-success').addClass('btn-danger');
            $(this).html('선택');
            $(inputObj).val($(this).attr(''));
        }

    });

</script>
