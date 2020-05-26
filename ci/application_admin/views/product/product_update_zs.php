<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/common/js/ckeditor/ckeditor.js"></script>
<script src="/common/js/ckeditor.userconfig.js"></script>

<style>
    @media (max-width: 400px) {
        .editor_wrap { -webkit-overflow-scrolling:touch; -webkit-box-sizing:content-box; }
        .editor_wrap iframe { -webkit-overflow-scrolling:touch; -webkit-box-sizing:content-box; }
        .editor_wrap textarea { min-width:260px !important; }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품관리 > 수정 DEV</h4>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">

                    <form name="main_form" id="main_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>"  enctype="multipart/form-data">
                        <input type="hidden" name="p_num" value="<?php echo $product_row['p_num']; ?>" />

                        <div class="form-group form-group-sm">

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
                            <!--
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">상품카테고리</label>
                                <div class="col-sm-10">
                                    <div class="form-control-static"><?php echo implode(" &gt; ", array_filter(array($product_row['p_cate1'], $product_row['p_cate2'], $product_row['p_cate3']))); ?></div>
                                </div>
                            </div>
                            -->

                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">카테고리 <span class="txt-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div id="field_p_cate1">
                                        <?php echo get_input_radio('p_cate1', $aCategoryLists , $product_row['p_cate1']); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">기간한정 <span class="txt-danger">*</span></label>
                                <div class="col-sm-10 form-inline">
                                    <div id="field_p_termlimit_yn">
                                        <?php echo get_input_radio('p_termlimit_yn', $this->config->item('product_termlimit_yn'), $product_row['p_termlimit_yn'], $this->config->item('product_termlimit_yn_text_color')); ?>
                                        <span class="help-inline middle txt-default">* 설정된 기간이 지나면 판매안함으로 상태가 변경됩니다.</span>
                                    </div>
                                    <div id="termlimit_date" style="display:none;">
                                        <div class="pull-left">
                                            <div class="input-group date" style="width:133px;" id="field_p_termlimit_datetime1">
                                                <input type="text" class="form-control" style="width:100px;" name="p_termlimit_datetime1" value="<?php echo get_date_format($product_row['p_termlimit_datetime1'], "-"); ?>" />
                                                <span class="input-group-btn text-left">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                            </div>
                                        </div>
                                        <div class="pull-left mgl5">
                                            <label class="inline">
                                                <select name="p_termlimit_datetime1_hour" class="form-control">
                                                    <?php echo get_select_option_hour("", get_hour_format($product_row['p_termlimit_datetime1'])); ?>
                                                </select>
                                                :
                                                <select name="p_termlimit_datetime1_min" class="form-control">
                                                    <?php echo get_select_option_min("", get_min_format($product_row['p_termlimit_datetime1'])); ?>
                                                </select>
                                                :00
                                            </label>
                                        </div>
                                        <div class="pull-left text-center" style="width:20px;">~</div>
                                        <div class="pull-left">
                                            <div class="input-group date" style="width:133px;" id="field_p_termlimit_datetime2">
                                                <input type="text" class="form-control" style="width:100px;" name="p_termlimit_datetime2" value="<?php echo get_date_format($product_row['p_termlimit_datetime2'], "-"); ?>" />
                                                <span class="input-group-btn">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                            </div>
                                        </div>
                                        <div class="pull-left mgl5">
                                            <label class="inline">
                                                <select name="p_termlimit_datetime2_hour" class="form-control">
                                                    <?php echo get_select_option_hour("", get_hour_format($product_row['p_termlimit_datetime2'])); ?>
                                                </select>
                                                :
                                                <select name="p_termlimit_datetime2_min" class="form-control">
                                                    <?php echo get_select_option_min("", get_min_format($product_row['p_termlimit_datetime2'])); ?>
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
                                        <?php echo get_input_radio('p_outside_display_able', $this->config->item('product_outside_display_able'), $product_row['p_outside_display_able'], $this->config->item('product_display_state_text_color')); ?>
                                    </div>
                                </div>
                            </div>
                            */
                            ?>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">진열상태 <span class="txt-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div id="field_p_display_state">
                                        <?php echo get_input_radio('p_display_state', $this->config->item('product_display_state'), $product_row['p_display_state'], $this->config->item('product_display_state_text_color')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">판매상태 <span class="txt-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div id="field_p_sale_state">
                                        <?php echo get_input_radio('p_sale_state', $this->config->item('product_sale_state'), $product_row['p_sale_state'], $this->config->item('product_sale_state_text_color')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">재고상태 <span class="txt-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div id="field_p_stock_state">
                                        <?php echo get_input_radio('p_stock_state', $this->config->item('product_stock_state'), $product_row['p_stock_state'], $this->config->item('product_stock_state_text_color')); ?>
                                    </div>
                                </div>
                            </div>

                            <!--
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">구매옵션선호도 노출여부 <span class="txt-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div id="field_p_stock_state">
                                        <?php echo get_input_radio('p_option_buy_cnt_view', $this->config->item('product_option_buy_cnt_view'), $product_row['p_option_buy_cnt_view'], $this->config->item('product_option_buy_cnt_view_text_color')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">상품정보제공공시 노출여부 <span class="txt-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div id="field_p_stock_state">
                                        <?php echo get_input_radio('p_info_tab_view', $this->config->item('product_info_tab_view'), $product_row['p_info_tab_view'], $this->config->item('product_info_tab_view_text_color')); ?>
                                    </div>
                                </div>
                            </div>
                            -->
                            <hr />

                            <!--
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">아이콘 1차</label>
                                <div class="col-sm-8">
                                    <?php
                            $empty_check = 'checked';
                            foreach($this->config->item('product_display_info1') as $name => $text) {
                                $checked = ($product_row['p_display_info_array'][$name] == 'Y') ? 'checked' : '';
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
                            -->


                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">배송정보</label>
                                <div class="col-sm-8">
                                    <?php echo get_input_radio('p_deliveryprice_type', $this->config->item('product_deliveryprice_type'), $product_row['p_deliveryprice_type']); ?>
                                    &nbsp;&nbsp;&nbsp;
                                    <!--
                                    <?php
                                    foreach($this->config->item('product_display_info4') as $name => $text) {
                                        $checked = ($product_row['p_display_info_array'][$name] == 'Y') ? 'checked' : '';
                                        ?>
                                        <label>
                                            <input type="checkbox" name="p_display_info_4[]" value="<?php echo $name; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
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
                                $checked = ($product_row['p_display_info_array'][$name] == 'Y') ? 'checked' : '';
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
                            <!--
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">관심수</label>
                                <div class="col-sm-8">
                                    <div class="pull-left">
                                        <input type="text" id="field_p_wish_count" name="p_wish_count" class="form-control" value="<?php echo $product_row['p_wish_count']; ?>" numberOnly="true" style="width:100px;" />
                                    </div>
                                    <div class="pull-left mgl10">
                                        <label>
                                            <input type="checkbox" id="field_p_wish_raise_yn" name="p_wish_raise_yn" value="Y" <?php echo ($product_row['p_wish_raise_yn'] == 'Y') ? 'checked' : ''; ?> />
                                            1시간마다 증가수
                                        </label>
                                    </div>
                                    <div class="pull-left mgl10">
                                        <input type="text" id="field_p_wish_raise_count" name="p_wish_raise_count" value="<?php echo $product_row['p_wish_raise_count']; ?>" class="form-control"  numberOnly="true" style="width:80px;" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">공유수</label>
                                <div class="col-sm-8">
                                    <div class="pull-left">
                                        <input type="text" id="field_p_share_count" name="p_share_count" class="form-control" value="<?php echo $product_row['p_share_count']; ?>" numberOnly="true" style="width:100px;" />
                                    </div>
                                    <div class="pull-left mgl10">
                                        <label>
                                            <input type="checkbox" id="field_p_share_raise_yn" name="p_share_raise_yn" value="Y" <?php echo ($product_row['p_share_raise_yn'] == 'Y') ? 'checked' : ''; ?> />
                                            1시간마다 증가수
                                        </label>
                                    </div>
                                    <div class="pull-left mgl10">
                                        <input type="text" id="field_p_share_raise_count" name="p_share_raise_count" value="<?php echo $product_row['p_share_raise_count']; ?>" class="form-control" numberOnly="true" style="width:80px;" />
                                    </div>
                                </div>
                            </div>


                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">메인배너 강제오픈여부</label>
                                <div class="col-sm-8">
                                    <label class="control-label bannerViewFlag-btn-area">
                                        <?if($product_row['p_main_banner_view'] == 'Y'){?>
                                            <a class="btn btn-info btn-xs" onclick="setBannerViewFlag('Y');" >오픈중</a>
                                        <?}else{?>
                                            <a class="btn btn-warning btn-xs" onclick="setBannerViewFlag('N');">오픈안함</a>
                                        <?}?>
                                    </label>
                                </div>
                            </div>
-->
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">해쉬태그 </label>
                                <div class="col-sm-8">
                                    <input type="text" id="field_p_hash" name="p_hash" class="form-control" value="<?php echo $product_row['p_hash']; ?>" />
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
                                    <span class="help-inline middle txt-default">ex:해쉬태그1,해쉬태그2</span>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">상품명 <span class="txt-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" id="field_p_name" name="p_name" class="form-control" value="<?php echo $product_row['p_name']; ?>" />
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">간략설명</label>
                                <div class="col-sm-10">
                                    <textarea id="field_p_summary" name="p_summary" class="form-control" style="width:100%; height:60px;"><?php echo $product_row['p_summary']; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">상세설명</label>
                                <div class="col-sm-10 editor_wrap">
                                    <textarea id="field_p_detail" name="p_detail" style="width:100%; height:300px;display:none;" title="p_detail"><?php echo $product_row['p_detail']; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">공구폼상품코드 <span class="txt-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" id="field_p_order_code" name="p_order_code" class="form-control" value="<?php echo $product_row['p_order_code']; ?>" />
                                </div>
                            </div>

                            <hr />


                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">옵션 사용여부 <span class="txt-danger">*</span></label>
                                <div class="col-sm-10">
                                    <?php echo get_input_radio('p_option_use', $this->config->item('product_option_use_yn'), $product_row['p_option_use'], $this->config->item('product_option_use_yn_text_color')); ?>
                                </div>
                            </div>
                            <div class="form-group form-group-sm option_section <?if($product_row['p_option_use'] != 'Y'){?>hidden<?}?>">
                                <label class="col-sm-2 control-label">옵션 타입</label>
                                <div class="col-sm-10">
                                    <?php echo get_input_radio('p_option_type', $this->config->item('product_option_type'), $product_row['p_option_type']); ?>
                                </div>
                            </div>
                            <div class="form-group form-group-sm option_section <?if($product_row['p_option_use'] != 'Y'){?>hidden<?}?>">
                                <label class="col-sm-2 control-label">옵션뎁스</label>
                                <div class="col-sm-10">
                                    <?php echo get_input_radio('p_option_depth', $this->config->item('product_option_depth'), $product_row['p_option_depth']); ?>
                                </div>
                            </div>
                            <div class="form-group form-group-sm option_section <?if($product_row['p_option_use'] != 'Y'){?>hidden<?}?>">
                                <label class="col-sm-2 control-label">옵션설정</label>
                                <div class="col-sm-10">
                                    <button class="btn btn-sm btn-info openOptionSet">옵션열기</button>
                                </div>
                            </div>


                            <script>

                                function isOptionChk(){

                                    var ret = true;
                                    var p_num = '<?=$product_row['p_num']?>';

                                    $.ajax({
                                        url : '/product/option',
                                        data : {p_num : p_num},
                                        type : 'post',
                                        dataType : 'json',
                                        async : false,
                                        success : function(result) {
                                            if(result.length > 0) ret = false
                                        }
                                    });

                                    if( ret == false ) {
                                        alert('해당상품의 옵션관련 정보를 변경하시려면 기존 옵션데이터 삭제 후 진행해주세요.');

                                        <?
                                        /*
                                        @TODO 중간에 변경가능성이 있어 ajax로 값을 가져올수 있도록 처리
                                        */
                                        ?>
                                        $('input[name="p_option_type"][value="<?=$product_row['p_option_type']?>"]').prop('checked',true);
                                        $('input[name="p_option_depth"][value="<?=$product_row['p_option_depth']?>"]').prop('checked',true);
                                    }

                                }

                                $(function(){

                                    $('input[name="p_option_use"]').on('change',function(){

                                        if($(this).val() == 'Y'){
                                            $('.option_section').removeClass('hidden');
                                        }else{
                                            $('.option_section').addClass('hidden');
                                        }


                                    });

                                    $('input[name="p_option_type"],input[name="p_option_depth"]').on('change',function(){
                                        isOptionChk();
                                    });

                                    $('.openOptionSet').on('click',function(e){
                                        e.preventDefault()

                                        var option_type     = $('input[name="p_option_type"]:checked').val();
                                        var option_depth    = $('input[name="p_option_depth"]:checked').val();
                                        var p_num           = '<?=$product_row['p_num']?>';

                                        if(option_depth == undefined){
                                            alert('옵션 차수를 선택해주세요');
                                            return false;
                                        }

                                        var popup_path  = '/product/option_pop?depth='+option_depth;
                                            popup_path += '&p_num='+p_num;
                                            popup_path += '&view_type='+option_type;

                                        var container = $('<div class="option_wrap" style="max-height: 680px;overflow-y: auto;">');
                                        $(container).load(popup_path);

                                        modalPop.createPop('옵션설정', container);
                                        modalPop.createButton('설정', 'btn btn-primary btn-sm', function(){
                                            $('#pop_insert_form').submit();
                                        });
                                        modalPop.createCloseButton('닫기', 'btn btn-default btn-sm');
                                        modalPop.show({'dialog_class':'modal-xlg','backdrop' : 'static'});

                                    });

                                    $(document).on('change','#pop_insert_form input[type="file"]',function(){
                                        readURL(this);
                                    })

                                    function readURL(input) {
                                        if (input.files && input.files[0]) {
                                            var reader = new FileReader();

                                            reader.onload = function (e) {

                                                if($(input).parent().parent().find('img').length > 0){
                                                    $(input).parent().parent().find('img').attr('src', e.target.result);
                                                }else{
                                                    $(input).parent().parent().find('.option_img .thumbnail').html('<img src="'+e.target.result+'" alt="" />');
                                                }

                                            }

                                            reader.readAsDataURL(input.files[0]);
                                        }
                                    }

                                    $(document).on('click','.option_del',function(e){
                                        e.preventDefault();

                                        var b = true;
                                        if($('#pop_insert_form table tbody tr').length < 2){
                                            if(confirm("현재 옵션이 1개입니다.\n삭제하시겠습니까?") == false) b = false;
                                        }

                                        if(b == true){
                                            if( $(this).parent().parent().hasClass('insert') == true ){
                                                $(this).parent().parent().remove();
                                            }else{//기 옵션데이터 삭제처리

                                                if(confirm("이미 저장된 옵션입니다.\n삭제 하시겠습니까?") == true){

                                                    $.ajax({
                                                        url: '/product/delete_option/',
                                                        data: {option_id : $(this).parent().parent().find('[name="option_id[]"]').val()},
                                                        type: 'post',
                                                        dataType: 'json',
                                                        success: function (result) {
                                                            if(result.msg) alert(result.msg);
                                                            if(result.success == true) get_option_page();
                                                        }

                                                    });

                                                }

                                            }
                                        }
                                    });


                                });
                            </script>

                            <hr />


                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">공급가격(원가) <span class="txt-danger">*</span></label>
                                <div class="col-sm-4">
                                    <div class="input-group" id="field_p_supply_price">
                                        <input type="text" name="p_supply_price" class="form-control number_style" value="<?php echo number_format($product_row['p_supply_price']); ?>" tabindex="1" />
                                        <span class="input-group-addon">원</span>
                                    </div>
                                </div>
                                <label class="col-sm-2 control-label">할인율</label>
                                <div class="col-sm-4">
                                    <div class="input-group" id="field_p_discount_rate">
                                        <input type="text" name="p_discount_rate" class="form-control" value="<?php echo $product_row['p_discount_rate']; ?>" tabindex="4" readonly="readonly" />
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">기존가격</label>
                                <div class="col-sm-4">
                                    <div class="input-group" id="field_p_original_price">
                                        <input type="text" name="p_original_price" class="form-control number_style" value="<?php echo number_format($product_row['p_original_price']); ?>" tabindex="2" />
                                        <span class="input-group-addon">원</span>
                                    </div>
                                </div>
                                <label class="col-sm-2 control-label">판매마진</label>
                                <div class="col-sm-4">
                                    <div class="input-group" id="field_p_margin_price">
                                        <input type="text" name="p_margin_price" class="form-control" value="<?php echo number_format($product_row['p_margin_price']); ?>" tabindex="5" readonly="readonly" />
                                        <span class="input-group-addon">원</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">판매가격 <span class="txt-danger">*</span></label>
                                <div class="col-sm-4">
                                    <div class="input-group" id="field_p_sale_price">
                                        <input type="text" name="p_sale_price" class="form-control number_style" value="<?php echo number_format($product_row['p_sale_price']); ?>" tabindex="3" />
                                        <span class="input-group-addon">원</span>
                                    </div>
                                </div>
                                <label class="col-sm-2 control-label">마진율</label>
                                <div class="col-sm-4">
                                    <div class="input-group" id="field_p_margin_rate">
                                        <input type="text" name="p_margin_rate" class="form-control" value="<?php echo $product_row['p_margin_rate']; ?>" tabindex="6" readonly="readonly" />
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">과세여부</label>
                                <div class="col-sm-8">
                                    <?php echo get_input_radio('p_taxation', $this->config->item('product_taxation'), $product_row['p_taxation']); ?>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">원산지</label>
                                <div class="col-sm-8">
                                    <div class="pull-left">
                                        <input type="text" id="field_p_origin" name="p_origin" class="form-control" value="<?php echo $product_row['p_origin']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">제조사</label>
                                <div class="col-sm-8">
                                    <div class="pull-left">
                                        <input type="text" id="field_p_manufacturer" name="p_manufacturer" class="form-control" value="<?php echo $product_row['p_manufacturer']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">공급사</label>
                                <div class="col-sm-8">
                                    <div class="pull-left">
                                        <input type="text" id="field_p_supplier" name="p_supplier" class="form-control" value="<?php echo $product_row['p_supplier']; ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">배송비 및 설명</label>
                                <div class="col-sm-10 form-inline">
                                    <div class="input-group mgr5" id="field_p_deliveryprice">
                                        <span class="input-group-addon">배송비(숫자만)</span>
                                        <input type="text" name="p_deliveryprice" class="form-control" style="width:100px;" numberOnly="true" value="<?php echo $product_row['p_deliveryprice']; ?>" />
                                        <span class="input-group-addon">원</span>
                                    </div>
                                    <div class="input-group" id="field_detail_add_deliveryprice_text">
                                        <span class="input-group-addon">배송비설명</span>
                                        <input type="text" name="detail_add_deliveryprice_text" class="form-control" style="width:250px;" value="<?php echo $product_row['p_detail_add_array']['deliveryprice_text']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">중량/용량</label>
                                <div class="col-sm-10">
                                    <div class="pull-left">
                                        <input type="text" id="field_detail_add_weight" name="detail_add_weight" class="form-control" value="<?php echo $product_row['p_detail_add_array']['weight']; ?>" />
                                    </div>
                                    <span class="help-block mgl10" style="display:inline-block">* 입력예: 150g, 120ml</span>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">배송정보</label>
                                <div class="col-sm-10">
                                    <div class="pull-left" style="width:50%;">
                                        <input type="text" id="field_detail_add_delivery_info" name="detail_add_delivery_info" class="form-control" value="<?php echo $product_row['p_detail_add_array']['delivery_info']; ?>" />
                                    </div>
                                    <span class="help-block mgl10" style="display:inline-block">* 입력예: 서울 경기 새벽배송, [월~금] 수령가능</span>
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
                                        <?php echo create_img_tag_from_json($product_row['p_rep_image'], 1, '100', '', 'data-type="rep_img"'); ?>
                                    </div>


                                    <div class="input-group" id="field_p_rep_image_add" style="width:100%;">
                                        <span class="input-group-addon" style="width:110px;">추가 대표이미지 </span>
                                        <input type="file" name="p_rep_image_add[]" class="form-control" multiple="multiple" />
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
                                    <?if($product_row['p_rep_image_add'] != ''){ $p_rep_image_add = json_decode($product_row['p_rep_image_add'],true);  ?>

                                        <div class="mgt10 mgb10" id="p_rep_image_add_list" style="border:1px solid #ccc;border-radius:4px;padding:10px;width:100%;overflow:auto;">


                                            <? foreach ($p_rep_image_add as $key => $row) {?>

                                                <div class="col-md-2 col-sm-12 col-xs-12 mgt10 text-center list-item-2" style="" data-key="<?php echo $key; ?>">
                                                    <a href="#none" onclick="new_win_open('<?php echo $row[0]; ?>', 'img_pop', 800, 600); " style="font-size:20px;"><i class="fa fa-search-plus"></i></a>
                                                    <a href="#none" style="font-size:20px;"><i class="fa fa-sort"></i></a>
                                                    <img src="<?php echo $row[0]; ?>?t=<?=@filemtime(DOCROOT . $row[1]);?>" style="width:200px;" alt="" />
                                                    <a href="#none" onclick="delete_product_image('<?php echo $product_row['p_num']; ?>', '5', this);" class="btn btn-danger btn-xs">삭제</a>
                                                </div>

                                            <?}?>

                                        </div>

                                    <?}?>
                                    <div class="input-group mgt10" id="field_p_today_image" style="width:100%;">
                                        <span class="input-group-addon" style="width:110px">정사각형 이미지</span>
                                        <input type="file" name="p_today_image" class="form-control" />
                                    </div>
                                    <p class="help-block">* 1:1 비율의 이미지로 업로드 </p>
                                    <div class="mgt5 mgb10 pTodayImage">
                                        <?php if( !empty($product_row['p_today_image']) ) { ?>
                                            <?php echo create_img_tag($product_row['p_today_image'], "", '100', '', 'data-type="rep_img"'); ?>
                                            <a href="#none" onclick="delete_product_image('<?php echo $product_row['p_num']; ?>', '1', '');" class="btn btn-danger btn-xs">삭제</a>
                                        <?php } ?>
                                    </div>

                                    <div class="input-group mgt10" id="field_p_banner_image" style="width:100%;">
                                        <span class="input-group-addon" style="width:110px">세로배너이미지</span>
                                        <input type="file" name="p_banner_image" class="form-control" />
                                    </div>
                                    <!--<p class="help-block">* 720 x 300</p>-->
                                    <div class="mgt5 mgb10 pBannerImage">
                                        <?php if( !empty($product_row['p_banner_image']) ) { ?>
                                            <?php echo create_img_tag($product_row['p_banner_image'], "", '100', '', 'data-type="rep_img"'); ?>
                                            <a href="#none" onclick="delete_product_image('<?php echo $product_row['p_num']; ?>', '4', '');" class="btn btn-danger btn-xs">삭제</a>
                                        <?php } ?>
                                    </div>

                                    <div class="mgt10" id="field_p_detail_image">
                                        <div class="input-group" style="width:100%;">
                                            <span class="input-group-addon" style="width:110px">상세이미지</span>
                                            <input type="file" name="p_detail_image[]" class="form-control" multiple="multiple" />
                                        </div>
                                        <p class="help-block">* 다중 선택 가능 / 이미지 파일(JPG, PNG, GIF)만 가능 / <!--최대 <?php echo $this->config->item('product_detail_image_max_count'); ?>개,--> 단일 파일 용량 10MB 미만, 총 용량 100MB 미만 업로드 가능</p>

                                        <p class="text-center"><button type="button" onclick="delete_product_image('<?php echo $product_row['p_num']; ?>', '3', '');" class="btn btn-danger btn-xs">모두삭제</button></p>
                                        <div class="mgt10 mgb10" id="detail_image_list" style="border:1px solid #ccc;border-radius:4px;padding:10px;width:100%;overflow:auto;">

                                            <?php
                                            foreach ( $product_detail_image_array as $key => $item ) {
                                                $img_thumb = $item[1];
                                                $img_org = $item[0];
                                                ?>

                                                <?/*<div class="col-md-12 col-sm-12 col-xs-12 mgt10 text-center list-item" style="" data-key="<?php echo $key; ?>">*/?>
                                                <div class="col-md-2 col-sm-12 col-xs-12 mgt10 text-center list-item" style="" data-key="<?php echo $key; ?>">
                                                    <a href="#none" onclick="new_win_open('<?php echo $img_org; ?>', 'img_pop', 800, 600); " style="font-size:20px;"><i class="fa fa-search-plus"></i></a>
                                                    <a href="#none" style="font-size:20px;"><i class="fa fa-sort"></i></a>
                                                    <img src="<?php echo $img_thumb; ?>?t=<?=@filemtime(DOCROOT . $img_thumb);?>" style="width:200px;" alt="" />
                                                    <a href="#none" onclick="delete_product_image('<?php echo $product_row['p_num']; ?>', '2', this);" class="btn btn-danger btn-xs">삭제</a>
                                                </div>

                                                <?php
                                            }//end of foreach()
                                            ?>

                                        </div>
                                        <p class="text-center"><button type="button" onclick="delete_product_image('<?php echo $product_row['p_num']; ?>', '3', '');" class="btn btn-danger btn-xs mgt10">모두삭제</button></p>
                                    </div>

                                </div>
                            </div>

                            <hr />

                            <div class="clearfix"></div>

                            <div class="col-sm-12">
                                <div style="color:#fff;background:#000;display:block;font-size:13px;font-weight:bold;padding:10px;overflow:hidden;">
                                    <div class="pull-left" style="padding:7px;">자동댓글</div>
                                </div>
                            </div>
                            <div class="col-sm-10 col-sm-offset-2" style="margin-top: 10px; ">
                                <p class="alert alert-info" style="margin-left: -15px!important;">
                                    - 자동댓글은 판매시작 후 일정 시간이 지난 후 등록이 자동으로 되도록 한다.<br>
                                    - 판매시작 후 몇 분 뒤 등록 - 분단위 시간이 기입가능하며 기입하지 않는 경우 랜덤한 시간(10분~60분)이 자동으로 저장된다.
                                </p>
                            </div>

                            <div class="form-group form-group-sm">

                                <label class="col-sm-2 control-label">자동등록 댓글</label>
                                <div class="col-sm-1">
                                    <input class="form-control" type="text" name="reg_name" value=""  placeholder="등록자명" title="등록자명">
                                </div>
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" name="auto_cmt_cont" value="" placeholder="댓글내용" title="댓글내용">
                                </div>
                                <div class="col-sm-2">
                                    <input class="form-control" type="number" name="reg_min" value=""  placeholder="판매시작 후 몇 분뒤 등록" title="판매시작 후 몇 분뒤 등록">
                                </div>
                                <div class="col-sm-1">
                                    <button class="btn btn-primary insertAutoComment" type="button">댓글등록</button>
                                </div>
                            </div>

                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">자동댓글 리스트</label>
                                <div class="col-sm-10">
                                    <div class="table-responsive" style="padding-right: 15px">
                                        <table id="auto_cmt_table" class="table table-hover table-bordered dataTable">
                                            <colgroup>
                                                <col style="width:5%" />
                                                <col style="width:10%" />
                                                <col style="width:50%" />
                                                <col style="width:10%" />
                                                <col style="width:15%" />
                                                <col style="width:10%" />

                                            </colgroup>
                                            <thead>
                                            <tr role="row" class="active">
                                                <td>No.</td>
                                                <td>등록자명</td>
                                                <td>댓글내용</td>
                                                <td>처리여부</td>
                                                <td>자동등록시간</td>
                                                <td>삭제</td>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>


                            <!-- 댓글목록 -->
                            <div class="col-xs-12">
                                <div style="color:#fff;background:#000;display:block;font-size:13px;font-weight:bold;padding:10px;overflow:hidden;">
                                    <div class="pull-left" style="padding:7px;">댓글 목록</div>
                                    <div class="pull-right"><button type="button" class="btn btn-primary btn-sm" onclick="comment_best_order_update();">베스트순서변경</button></div>
                                </div>
                                <div id="comment_list" style="color:#000;margin-bottom:30px;"></div>
                            </div>
                            <!-- // 댓글목록 -->

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
    var p_num = '<?php echo $product_row['p_num']; ?>';
    var this_form_action = '<?php echo $this->page_link->update_proc; ?>';
    var list_url = '<?php echo $list_url; ?>';
    var delete_url = '<?php echo $this->page_link->delete_proc; ?>/?<?php echo $this->input->server('QUERY_STRING'); ?>';
    var detail_image_max_count = '<?php echo $this->config->item('product_detail_image_max_count'); ?>';
</script>

<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>
<script src="/plugins/smarteditor2/js/HuskyEZCreator.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/smarteditor2/js/HuskyEZCreator.js"); ?>" charset="utf-8"></script>
<?=link_src_html("/js/page/product.js", "js");?>


<script type="text/javascript">

    function getAutoCmt(){

        var p_num            = '<?=$product_row['p_num']?>';

        $.ajax({
            url : '/product/get_auto_comment',
            data : {p_num:p_num},
            type : 'post',
            dataType : 'json',
            success : function (result) {
                if(result.success == true){
                    //자동댓글 리스트에 추가

                    var html  = "";

                    if(result.data.length > 0){

                        var i = 1;
                        $.each(result.data,function(k,r){

                            var proc_str = '<span class="text-danger">처리전</span>';
                            var reg_min_str= '랜덤 자동등록';
                            if(r.proc_flag == 'Y') proc_str = '<span class="text-primary">처리완료<br><i>('+r.proc_date_str+')</i></span>';
                            if(r.reg_min > 0) reg_min_str= '판매시작 후 '+ number_format(r.reg_min)+'분뒤 자동 등록';

                            html += "<tr>";
                            html += "   <td>"+i+"</td>";
                            html += "   <td>"+r.reg_name+"</td>";
                            html += "   <td>"+r.auto_cmt_cont+"</td>";
                            html += "   <td>"+proc_str+"</td>";
                            html += "   <td>"+reg_min_str+"</td>";

                            html += "   <td><button class='btn btn-danger btn-xs delAutoComment' data-seq='"+r.seq+"' >삭제</button></td>";
                            html += "</tr>";

                            i++;

                        });

                        $('#auto_cmt_table tbody').html(html);

                    }else{

                        html += "<tr>";
                        html += "   <td colspan='6' class='text-center'>자동 등록된 댓글이 없읍니다.</td>";
                        html += "</tr>";

                        $('#auto_cmt_table tbody').html(html);

                    }

                }

                if(empty(result.msg) == false) alert(result.msg);

            }
        });

    }

    $(document).on('click','.delAutoComment',function(e){
        e.preventDefault();

        if(confirm('해당 자동댓글을 삭제 하시겠습니까?') == true){

            var seq = $(this).data('seq');

            if(empty(seq) == true){
                alert('필수 정보 누락(seq)');
                return false;
            }

            var obj = {seq:seq};

            $.ajax({
                url : '/product/del_auto_comment',
                data : obj,
                type : 'post',
                dataType : 'json',
                success : function (result) {

                    if(result.success == true) getAutoCmt();
                    if(empty(result.msg) == false) alert(result.msg);

                }
            });

        }

    });

    //메인배너 노출 변경 함수
    function setBannerViewFlag(flag){

        if(flag == 'Y'){
            var cf = confirm('현재 오픈중입니다.메인배너에서 제외하시겠습니까?');
        }else{
            var cf = confirm('현재 오픈하지 않고 있습니다.메인배너에서 추가하시겠습니까?');
        }

        if(cf == true){

            $.ajax({
                url: '/product/setMainBannerFlag',
                data: {p_num : '<?=$product_row->p_num?>' , flag : flag},
                type: 'post',
                dataType: 'json',
                cache: false,
                success: function (result) {

                    if(result.message) alert(result.message);
                    if(result.status === "000"){
                        location.reload();
                    }
                }

            });
        }

    }


    /**
     * 댓글 목록
     */
    function get_comment_list() {
        $('#comment_list').load('/comment/list_ajax/?tb=product&tb_num=<?php echo $product_row['p_num']; ?>&view_type=simple');
    }//end of get_comment_list()

    /**
     * 댓글 베스트 순서 변경
     */
    function comment_best_order_update () {
        var cmt_num = {};
        $.each($('[name*="best_order[]"]'), function(){
            cmt_num[$(this).attr('data-num')] = $(this).val();
        });

        Pace.restart();

        $.ajax({
            url : '/comment/best_order_proc',
            data : {data:cmt_num},
            type : 'post',
            dataType : 'json',
            success : function (result) {
                if( result.message_type == 'alert' && !empty(result.message) ) {
                    alert(result.message);
                }

                if( result.status == status_code['success'] ) {
                    get_comment_list();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of comment_best_order_update()


    /**
     * 상품 예전 댓글 갯수 출력
     */
    function get_product_old_comment_count() {
        if( empty(p_num) ) {
            return false;
        }

        $.ajax({
            url : '/product/old_comment_count',
            data : {p_num:p_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message_type == 'alert' && !empty(result.message) ) {
                    alert(result.message);
                }

                $('#old_comment_count').html(result.data.old_comment_count);
            },
            error : function () {
                alert('<?php echo echo_lang('site_error_unknown'); ?>');
            }
        });

    }//end of get_product_old_comment_count()

    /**
     * 백업된 상품 댓글 복구
     */
    function product_comment_restore(p_num) {
        if( empty(p_num) ) {
            return false;
        }

        if( !confirm('예전 상품 댓글을 복구하시겠습니까?\n(완료 후 페이지가 새로고침 됩니다.)') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '/product/comment_restore',
            data : {p_num:p_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message_type == 'alert' && !empty(result.message) ) {
                    alert(result.message);
                }

                //$('#old_comment_count').html(result.data.old_commnet_count);
                location.reload();
            },
            error : function () {
                alert('<?php echo echo_lang('site_error_unknown'); ?>');
            }
        });
    }//end of product_comment_restore()


    $(function(){

        //댓글 목록 출력
        get_comment_list();

        getAutoCmt();

        $('.insertAutoComment').on('click',function(e){
            e.preventDefault();

            var auto_cmt_cont    = $('input[name="auto_cmt_cont"]').val();
            var reg_min          = $('input[name="reg_min"]').val();
            var reg_name         = $('input[name="reg_name"]').val();

            var p_num            = '<?=$product_row['p_num']?>';

            if(empty(auto_cmt_cont) == true){
                alert('자동 등록할 댓글 내용을 입력해주세요');
                $('input[name="auto_cmt_cont"]').focus();
                return false;
            }

            if(empty(reg_min) == true) reg_min = '0';

            if(reg_min > 0 && reg_min < 10){
                alert('댓글등록시간을 지정하시려면 최소 10분 이상으로 등록해주세요!');
                $('input[name="reg_min"]').focus();
                return false;
            }

            var obj = {auto_cmt_cont:auto_cmt_cont , reg_min:reg_min , p_num:p_num , reg_name : reg_name };

            $.ajax({
                url : '/product/set_auto_comment',
                data : obj,
                type : 'post',
                dataType : 'json',
                success : function (result) {
                    if(result.success == true){
                        //input값 초기화
                        $('input[name="auto_cmt_cont"]').val('');
                        $('input[name="reg_min"]').val('');
                        $('input[name="reg_name"]').val('');
                        getAutoCmt();
                    }

                    if(empty(result.msg) == false) alert(result.msg);

                }
            });

        });

        //ajax page
        $(document).on('click', '#comment_list .pagination.ajax a', function(e){
            e.preventDefault();
            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#comment_list').load($(this).attr('href'));
        });

        <?php if( $product_row['p_termlimit_yn'] == 'Y') { ?>
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


        //sortable
        $('#p_rep_image_add_list').sortable({
            revert: true,
            stop : function(event, ui){
                detail_img_order_update_rep(p_num);
            }
        });
        $('#p_rep_image_add_list').disableSelection();

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
            $('.list-item').css('min-height',parseInt(max_img_height,10)+70+'px');
        });
        <?/* END */?>


        //listCall();

        //상품 예전 댓글 갯수 출력
        //get_product_old_comment_count();
    });

</script>
