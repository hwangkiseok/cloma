<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />
<script src="/common/js/ckeditor/ckeditor.js"></script>
<script src="/common/js/ckeditor.userconfig.js"></script>
<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품관리 > 등록</h4>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">

                    <form name="main_form" id="main_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>" enctype="multipart/form-data">
                        <input type="hidden" name="opt_token" value="<?=$opt_token?>">

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">카테고리 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_p_cate1">
                                    <?php echo get_input_radio('p_cate1', $aCategoryLists); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">기간한정 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10 form-inline">
                                <div id="field_p_termlimit_yn">
                                    <?php echo get_input_radio('p_termlimit_yn', $this->config->item('product_termlimit_yn'), 'N', $this->config->item('product_termlimit_yn_text_color')); ?>
                                    <span class="help-inline middle txt-default">* 설정된 기간이 지나면 판매종료로 상태가 변경됩니다.</span>
                                </div>
                                <div id="termlimit_date" style="display: none;">
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" name="p_termlimit_datetime1" value="" />
                                            <span class="input-group-btn text-left">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left mgl5">
                                        <label class="inline">
                                            <select name="p_termlimit_datetime1_hour" class="form-control">
                                                <?php echo get_select_option_hour("", "07"); ?>
                                            </select>
                                            :
                                            <select name="p_termlimit_datetime1_min" class="form-control">
                                                <?php echo get_select_option_min("", "00"); ?>
                                            </select>
                                            :00
                                        </label>
                                    </div>
                                    <div class="pull-left text-center" style="width:20px;">~</div>
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" name="p_termlimit_datetime2" value="" />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left mgl5">
                                        <label class="inline">
                                            <select name="p_termlimit_datetime2_hour" class="form-control">
                                                <?php echo get_select_option_hour("", "07"); ?>
                                            </select>
                                            :
                                            <select name="p_termlimit_datetime2_min" class="form-control">
                                                <?php echo get_select_option_min("", "59"); ?>
                                            </select>
                                            :59
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?
                        /*
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">외부 공유 불가여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_p_outside_display_able">
                                    <?php echo get_input_radio('p_outside_display_able', $this->config->item('product_outside_display_able'), 'Y', $this->config->item('product_display_state_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        */
                        ?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">진열상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_p_display_state">
                                    <?php echo get_input_radio('p_display_state', $this->config->item('product_display_state'), 'Y', $this->config->item('product_display_state_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">판매상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_p_sale_state">
                                    <?php echo get_input_radio('p_sale_state', $this->config->item('product_sale_state'), 'Y', $this->config->item('product_sale_state_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">재고상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_p_stock_state">
                                    <?php echo get_input_radio('p_stock_state', $this->config->item('product_stock_state'), 'Y', $this->config->item('product_stock_state_text_color')); ?>
                                </div>
                            </div>
                        </div>

                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품정보제공공시 노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_p_stock_state">
                                    <?php echo get_input_radio('p_info_tab_view', $this->config->item('product_info_tab_view'), 'Y', $this->config->item('product_info_tab_view_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        -->

                        <hr >

                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">아이콘 1차</label>
                            <div class="col-sm-8">
                                <?php foreach($this->config->item('product_display_info1') as $name => $text) { ?>
                                    <label>
                                        <input type="radio" name="p_display_info[]" value="<?php echo $name; ?>" /> <?php echo $text; ?>
                                    </label>
                                <?php } ?>
                                <label>
                                    <input type="radio" name="p_display_info[]" value="" checked /> 해당없음
                                </label>
                            </div>
                        </div>
                        -->
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">배송정보</label>
                            <div class="col-sm-8">
                                <?php echo get_input_radio('p_deliveryprice_type', $this->config->item('product_deliveryprice_type'), 1); ?>
                                &nbsp;&nbsp;&nbsp;
                                <!--
                                <?php foreach($this->config->item('product_display_info4') as $name => $text) { ?>
                                    <label>
                                        <input type="checkbox" name="p_display_info_4[]" value="<?php echo $name; ?>" /> <?php echo $text; ?>
                                    </label>
                                <?php } ?>
                                -->
                            </div>
                        </div>

                        <!--
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
                        -->
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">추가노출</label>
                            <div class="col-sm-8">
                                <?php foreach($this->config->item('product_md_division') as $name => $text) { ?>
                                    <label>
                                        <input type="checkbox" name="pmd_division[]" value="<?php echo $name; ?>" /> <?php echo $text; ?>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">관심수</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="field_p_wish_count" name="p_wish_count" class="form-control" value="0" numberOnly="true" style="width:100px;" />
                                </div>
                                <div class="pull-left mgl10">
                                    <label>
                                        <input type="checkbox" id="field_p_wish_raise_yn" name="p_wish_raise_yn" value="Y" />
                                        1시간마다 증가수
                                    </label>
                                </div>
                                <div class="pull-left mgl10">
                                    <input type="text" id="field_p_wish_raise_count" name="p_wish_raise_count" value="0" class="form-control"  numberOnly="true" style="width:80px;" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">공유수</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="field_p_share_count" name="p_share_count" class="form-control" value="0" numberOnly="true" style="width:100px;" />
                                </div>
                                <div class="pull-left mgl10">
                                    <label>
                                        <input type="checkbox" id="field_p_share_raise_yn" name="p_share_raise_yn" value="Y" />
                                        1시간마다 증가수
                                    </label>
                                </div>
                                <div class="pull-left mgl10">
                                    <input type="text" id="field_p_share_raise_count" name="p_share_raise_count" value="0" class="form-control" numberOnly="true" style="width:80px;" />
                                </div>
                            </div>
                        </div>
                        -->
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">해시태그 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" id="field_p_name" name="p_hash" class="form-control" value="" />
                                <div style="margin-top: 5px">
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="여성의류,여성상의,여성하의">여성패션</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="남성패션,남성의류,남성상의,남성하의">남성패션</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="화장품,뷰티,여성화장품,남성화장품">화장품</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="냉동식품,간편식품,냉동,즉석식품,반찬">냉동식품</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="야채,채소,농장,과일,농산물,반찬">신선식품</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="생선,수산물,해산물,반찬">수산물</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="고기,육류,정육,반찬">육류</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="다이어트식품,다이어트용품">다이어트</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="운동기구,헬스,실내운동,다이어트,운동용품,홈트레이닝,운동용품,홈트레이닝">운동</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="아동의류,아동용품">아동</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="가전제품">가전</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="주방용품,생활잡화">주방용품</button>
                                    <button class="btn btn-default hashtag_ex" type="button" data-tag="홈에스테틱,홈케어">뷰티기계</button>
                                </div>
                                <span class="help-inline middle txt-default">ex:해시태그1,해시태그2</span>
                            </div>
                        </div>

                        <hr />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품명 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" id="field_p_name" name="p_name" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">간략설명</label>
                            <div class="col-sm-10">
                                <textarea id="field_p_summary" name="p_summary" class="form-control" style="width:100%; height:60px;"></textarea>
                            </div>
                        </div>
                        <!--                        <div class="form-group form-group-sm">-->
                        <!--                            <label class="col-sm-2 control-label">상단고정이미지</label>-->
                        <!--                            <div class="col-sm-10 descImg-top-Ajax">-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상세설명</label>
                            <div class="col-sm-10">
                                <textarea id="field_p_detail" name="p_detail" style="width:100%; height:200px; display:none;"></textarea>
                            </div>
                        </div>
                        <!--<div class="form-group form-group-sm">-->
                        <!--    <label class="col-sm-2 control-label">주문링크 <span class="txt-danger">*</span></label>-->
                        <!--    <div class="col-sm-10">-->
                        <!--        <div class="input-group" id="field_p_order_link">-->
                        <!--            <span class="input-group-btn">-->
                        <!--                <select name="p_order_link_protocol" class="form-control br-l3 b-r0" style="width:auto">-->
                        <!--                    <option value="http://">http://</option>-->
                        <!--                    <option value="https://">https://</option>-->
                        <!--                </select>-->
                        <!--            </span>-->
                        <!--            <input type="text" name="p_order_link" class="form-control" />-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품코드 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" id="field_p_order_code" name="p_order_code" class="form-control" />
                            </div>
                        </div>


                        <hr />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">옵션 여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <?php echo get_input_radio('p_option_use', $this->config->item('product_option_use_yn'), 'N', $this->config->item('product_option_use_yn_text_color')); ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm option_section hidden">
                            <label class="col-sm-2 control-label">옵션 타입</label>
                            <div class="col-sm-10">
                                <?php echo get_input_radio('p_option_type', $this->config->item('product_option_type') ); ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm option_section hidden">
                            <label class="col-sm-2 control-label">옵션 차수</label>
                            <div class="col-sm-10">
                                <?php echo get_input_radio('p_option_depth', $this->config->item('product_option_depth')); ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm option_section hidden">
                            <label class="col-sm-2 control-label">옵션 설정</label>
                            <div class="col-sm-10">
                                <button class="btn btn-sm btn-info" type="button" onclick="openOptionSet();">옵션열기</button>
                            </div>
                        </div>

                        <hr />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">공급가격(원가) <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_supply_price">
                                    <input type="text" name="p_supply_price" class="form-control" tabindex="1" />
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                            <label class="col-sm-2 control-label">할인율</label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_discount_rate">
                                    <input type="text" name="p_discount_rate" class="form-control" tabindex="4" readonly="readonly" />
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">기존가격</label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_original_price">
                                    <input type="text" name="p_original_price" class="form-control" value="0" tabindex="2" />
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                            <label class="col-sm-2 control-label">판매마진</label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_margin_price">
                                    <input type="text" name="p_margin_price" class="form-control" tabindex="5" readonly="readonly" />
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">판매가격 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_sale_price">
                                    <input type="text" name="p_sale_price" class="form-control" tabindex="3" />
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                            <label class="col-sm-2 control-label">마진율</label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_margin_rate">
                                    <input type="text" name="p_margin_rate" class="form-control" tabindex="6" readonly="readonly" />
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">과세여부</label>
                            <div class="col-sm-8">
                                <?php echo get_input_radio('p_taxation', $this->config->item('product_taxation'), 1); ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">원산지</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="field_p_origin" name="p_origin" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제조사</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="field_p_manufacturer" name="p_manufacturer" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">공급사</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="field_p_supplier" name="p_supplier" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <?/**
                         * @date 170906
                         * @modify 황기석
                         * @desc 배송비관련정보 추가
                         */ ?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">배송비 및 설명</label>
                            <div class="col-sm-10 form-inline">
                                <div class="input-group mgr5" id="field_p_deliveryprice">
                                    <span class="input-group-addon">배송비(숫자만)</span>
                                    <input type="text" name="p_deliveryprice" class="form-control" style="width:100px;" numberOnly="true" value="" />
                                    <span class="input-group-addon">원</span>
                                </div>
                                <div class="input-group" id="field_detail_add_deliveryprice_text">
                                    <span class="input-group-addon">배송비설명</span>
                                    <input type="text" name="detail_add_deliveryprice_text" class="form-control" style="width:250px;" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">중량/용량</label>
                            <div class="col-sm-10">
                                <div class="pull-left">
                                    <input type="text" id="field_detail_add_weight" name="detail_add_weight" class="form-control" value="" />
                                </div>
                                <span class="help-block mgl10" style="display:inline-block">* 입력예: 150g, 120ml</span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">배송정보</label>
                            <div class="col-sm-10">
                                <div class="pull-left" style="width:50%;">
                                    <input type="text" id="field_detail_add_delivery_info" name="detail_add_delivery_info" class="form-control" value="" />
                                </div>
                                <span class="help-block mgl10" style="display:inline-block">* 입력예: 서울 경기 새벽배송, [월~금] 수령가능</span>
                            </div>
                        </div>

                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">배송비설정</label>
                            <div class="col-sm-4">
                                <div class="input-group" id="field_p_deliveryprice" style="width:150px;">
                                    <input type="text" name="p_deliveryprice" class="form-control" numberOnly="true" value="0" />
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                        </div>
                        -->

                        <?/**/?>


                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품이미지</label>
                            <div class="col-sm-10">
                                <div class="input-group" id="field_p_rep_image" style="width:100%;">
                                    <span class="input-group-addon" style="width:110px">대표이미지 <span class="txt-danger">*</span></span>
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

                                <div class="input-group" id="field_p_rep_image" style="width:100%;">
                                    <span class="input-group-addon" style="width:110px">추가 대표이미지</span>
                                    <input type="file" name="p_rep_image_add[]" class="form-control" />
                                </div>
                                <p class="help-block">* 다중 선택 가능 / 최대 2개, 단일 파일 용량 10MB 미만, 총 용량 20MB 미만 업로드 가능</p>
                                <?php if( $this->config->item('product_rep_image_size') ) { ?>
                                    <p class="help-block">*
                                        <?php foreach ($this->config->item('product_rep_image_size') as $item) { ?>
                                            <?php echo $item[0] . " x " . $item[1]; ?>
                                        <?php } ?>
                                        로 리사이징(썸네일 생성) 됩니다.
                                    </p>
                                <?php } ?>

                                <?
                                /*
                                <div class="input-group mgt10" id="field_p_today_image" style="width:100%;">
                                    <span class="input-group-addon" style="width:110px">세로형이미지</span>
                                    <input type="file" name="p_today_image" class="form-control" />
                                </div>
                                <p class="help-block">* 300 x 345, 600 x 690 등 (비율에 맞게 업로드)</p>

                                <div class="input-group mgt10" id="field_p_banner_image" style="width:100%;">
                                    <span class="input-group-addon" style="width:110px">배너이미지</span>
                                    <input type="file" name="p_banner_image" class="form-control" />
                                </div>
                                <p class="help-block">* 720 x 300</p>
                                */
                                ?>
                                <div class="mgt10" id="field_p_detail_image" style="width:100%;">
                                    <div class="input-group" style="width:100%;">
                                        <!--<span class="input-group-addon">상세이미지1</span>-->
                                        <!--<input type="file" id="field_p_detail_image_1" name="p_detail_image[]" class="form-control" multiple />-->
                                        <span class="input-group-addon" style="width:110px">상세이미지</span>
                                        <input type="file" name="p_detail_image[]" class="form-control" multiple="multiple" />
                                    </div>
                                    <p class="help-block">* 다중 선택 가능 / 이미지 파일(JPG, PNG, GIF)만 가능 / <!--최대 <?php echo $this->config->item('product_detail_image_max_count'); ?>개,--> 단일 파일 용량 10MB 미만, 총 용량 100MB 미만 업로드 가능</p>
                                </div>
                                <!--<div class="pull-right mgt10">-->
                                <!--    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="add_input_detail_image();">상세이미지 추가</button>-->
                                <!--</div>-->
                            </div>
                        </div>

                        <hr />

                        <div class="clearfix"></div>

                        <div class="form-group form-group-sm">
                            <div class="col-sm-offset-2 col-sm-10 col-xs-12 text-right">
                                <a href="<?echo $list_url; ?>" id="btn_list" class="btn btn-default btn-sm mgr5">목록보기</a>
                                <button type="submit" class="btn btn-primary btn-sm">등록완료</button>
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
    var this_form_action = '<?php echo $this->page_link->insert_proc; ?>';
    var list_url = '<?php echo $list_url; ?>';
    var detail_image_max_count = '<?php echo $this->config->item('product_detail_image_max_count'); ?>';

    $(document).ready(function(){

        var sess_id = '<?=$_SESSION['GroupMemberId']?>';

        if(sess_id == 'dmsl123'){
            setTimeout(function(){ setMdImage('pepsi'); },500);
        }else if(sess_id == 'rhgmltjs222'){
            setTimeout(function(){ setMdImage('kehlina'); },500);
        }else if(sess_id == 'yuneh88'){
            setTimeout(function(){ setMdImage('happy'); },500);
        }else if(sess_id == 'io901oi'){
            setTimeout(function(){ setMdImage('maker'); },500);
        }



        //listCall();
    });

    function listCall(){

        var p_top_desc = '<?=$product_row->p_top_desc?>';

        $.ajax({
            url : '/Product_desc/product_desc_list_ajax/',
            type : 'post',
            dataType : 'json',
            success : function (result) {

                var html_top = '';
                var html_btm = '';
                var html_noImg = '';

                html_noImg += '   <div class="alert alert-danger" role="alert">';
                html_noImg += '       등록된 이미지가 없습니다.';
                html_noImg += '   </div>';

                var top = 0;
                var btm = 0;
                var default_desc = 0;
                for(var i = 0 ; i < result.data.length ; i++){

                    if(result.data[i].gubun == '1'){
                        if(result.data[i].default_flag == 'Y') default_desc = result.data[i].p_desc;
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

                if(p_top_desc > 0){
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

<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>
<?=link_src_html("/js/page/product_admin.js", "js");?>
<?=link_src_html("/js/page/product_admin_option.js", "js");?>