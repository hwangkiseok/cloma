<style>
    .list-group { max-height:280px; overflow-y:auto; }
    .list-group-item { padding:5px; }
    .list-group-item .row { margin:0; padding:5px; }
    .list-group-item .row p { margin:0; }
    #product_list_group .left { padding-right:5px; }
    #product_list_group .right { padding-left:5px; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_form" id="pop_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="cmd_num" value="<?php echo $category_md_row->cmd_num; ?>" />

                        <input type="hidden" name="cmd_division" value="4">
                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">구분 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_cmd_division">
                                    <?php echo get_input_radio('cmd_division', $this->config->item('category_md_division'), ''); ?>
                                </div>
                            </div>
                        </div>
                        -->
                        <div id="product_cate_wrap" class="form-group form-group-sm" >
                            <label class="col-sm-2 control-label">상품카테고리 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_cmd_product_cate">
                                    <?php
                                    $cmd_product_cate_array = explode(",", $category_md_row->cmd_product_cate);
                                    foreach ($product_cate_array as $key => $item) {
                                        $checked = "";
                                        if( in_array($item, $cmd_product_cate_array) ) {
                                            $checked = 'checked="checked"';
                                        }
                                        ?>

                                        <label class="mgr10"><input type="checkbox" name="cmd_product_cate[]" value="<?php echo $item; ?>" <?php echo $checked; ?> /> <?php echo $item; ?></label>

                                    <?php }//endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">카테고리명 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_cmd_name">
                                    <input type="text" name="cmd_name" class="form-control" value="<?php echo $category_md_row->cmd_name; ?>" />
                                </div>
                            </div>
                        </div>
                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">목록 이미지 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_cmd_image">
                                    <input type="file" name="cmd_image" class="form-control" value="" />
                                    <p class="help-block">* 변경시에만 입력</p>
                                    <p><?php echo create_img_tag($category_md_row->cmd_image, 0, 200); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">아이콘 이미지<br>메인상단<span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_cmd_image">
                                    <input type="file" name="cmd_icon" class="form-control" value="" />
                                    <p class="help-block">* 변경시에만 입력</p>
                                    <p><?php echo create_img_tag($category_md_row->cmd_icon, 0, 30); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">아이콘 이미지<br>메인하단/맞춤쇼핑 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_cmd_image">
                                    <input type="file" name="cmd_icon2" class="form-control" value="" />
                                    <p class="help-block">* 변경시에만 입력</p>
                                    <p><?php echo create_img_tag($category_md_row->cmd_icon2, 0, 30); ?></p>
                                </div>
                            </div>
                        </div>



                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">카테고리 하단 띠배너</label>
                            <div class="col-sm-10">
                                <div id="field_cmd_zone_banner">
                                    <input type="file" name="cmd_zone_banner" class="form-control" value="" />
                                    <p class="help-block">* 변경시에만 입력</p>
                                    <p><?php echo create_img_tag($category_md_row->cmd_zone_banner, 0, 30); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">카테고리 하단 띠배너 URL</label>
                            <div class="col-sm-10">
                                <div id="field_cmd_zone_banner_url">
                                    <input type="text" name="cmd_zone_banner_url" class="form-control" value="<?php echo $category_md_row->cmd_zone_banner_url; ?>" />
                                </div>
                            </div>
                        </div>
                        -->


                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">활성여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_cmd_state">
                                    <?php echo get_input_radio("cmd_state",  $this->config->item("category_md_state"), $category_md_row->cmd_state, $this->config->item("category_md_state_text_color")); ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var form = '#pop_form';

    /**
     * 상품카테고리 보이기/안보이기
     */
    // function set_product_cate() {
    //     $('#product_cate_wrap').hide();
    //     if( $(form + ' [name="cmd_division"]:checked').val() == '4' ) {
    //         $('#product_cate_wrap').show();
    //     }
    // }//end of set_product_cate()


    //document.ready
    $(function(){
        // set_product_cate();
        //
        // //구분 : 상품카테고리 선택시
        // $(form + ' [name="cmd_division"]').on('change', function(){
        //     set_product_cate();
        // });

        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(formData, jqForm, options) {
                // if( $(form + ' [name="cmd_division"]:checked').length < 1 ) {
                //     error_message($('#field_cmd_division'), '카테고리를 선택하세요.');
                //     return false;
                // }
                // if( $(form + ' [name="cmd_division"]:checked').val() == '4' && !$(form + ' [name="cmd_product_cate[]"]:checked').length ) {
                //     error_message($('#field_cmd_product_cate'), '상품카테고리를 선택하세요.');
                //     return false;
                // }
                if( !$(form + ' [name="cmd_name"]').val() ) {
                    error_message($('#field_cmd_name'), '카테고리명을 입력하세요.');
                    return false;
                }
            },
            success: function(res) {
                if( res.message ) {
                    if( res.message_type == 'alert' ) {
                        alert(res.message);
                    }
                }

                if( res.status == status_code['success'] ) {
                    //location.replace(list_url);
                    $('#search_form').submit();
                    modalPop.hide();
                }
                else {
                    if( res.error_data ) {
                        $.each(res.error_data, function(key, msg){
                            if( $('#field_' + key).length ) {
                                error_message($('#field_' + key), msg);
                            }
                        });
                    }
                }//end of if()
            },
            complete : function() {
                //$(this).attr('action', this_form_action);
            }
        });//end of ajax_form()

        //ajax page
        $(document).on('click', '#pop_product_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            product_search($(this).attr('href'));
        });
    });//end of document.ready()
</script>