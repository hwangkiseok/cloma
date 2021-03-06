<style>
    .list-group-item .row { margin:0; }
</style>


<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_form" id="pop_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>">
                        <div class="form-group form-group-sm">
                            <div class="input-group" style="width:auto;">
                                <span class="input-group-addon" style="width:110px;">상품검색</span>
                                <input type="text" class="form-control" name="kwd" maxlength="100" style="width:200px;border-radius:0;" placeholder="검색어 입력" />
                                <span class="input-group-btn pull-left"><button type="button" class="btn btn-primary btn-sm" onclick="product_search();">검색</button></span>
                            </div>
                        </div>

                        <div id="pop_product_list" style="max-height:500px;overflow:auto;margin-bottom:10px;"></div>

                        <div class="form-group form-group-sm">
                            <div class="input-group" style="width:100%;" id="field_kp_product_num">
                                <span class="input-group-addon" style="width:110px;">선택한 상품</span>
                                <input type="text" class="form-control" name="p_name" maxlength="100" style="width:100%;" readonly placeholder="상품검색 후 선택하세요." />
                            </div>
                            <input type="hidden" name="kp_product_num" value="" /><!-- 상품번호 -->
                        </div>

                        <div class="form-group form-group-sm">
                            <div class="input-group" style="width:100%;" id="field_kp_prod_type">
                                <span class="input-group-addon" style="width:110px;">상품구분</span>
                                <span class="form-control">
                                    <?=get_input_radio("kp_prod_type", $this->config->item("kakao_product_prod_type"), "G");?>
                                </span>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <div class="input-group" style="width:100%;" id="field_kp_seq">
                                <span class="input-group-addon" style="width:110px;">출력순서</span>
                                <input type="text" class="form-control" name="kp_seq" maxlength="3" style="width:100%;" value="1" />
                            </div>
                            <div class="form-control-static">* 출력순서는 브랜드 상품일때만 적용됨.</div>
                        </div>

                        <hr />

                        <div class="form-group form-group-sm">
                            <div class="input-group" style="width:100%;" id="field_kp_title">
                                <span class="input-group-addon" style="width:110px;">제목</span>
                                <input type="text" class="form-control" name="kp_title" maxlength="100" style="width:100%;" />
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <div class="input-group" style="width:100%;" id="field_kp_content">
                                <span class="input-group-addon" style="width:110px;">내용</span>
                                <textarea class="form-control" name="kp_content" style="height:50px;"></textarea>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <div class="input-group" style="width:100%;" id="field_kp_button_name">
                                <span class="input-group-addon" style="width:110px;">버튼명</span>
                                <input type="text" class="form-control" name="kp_button_name" maxlength="100" style="width:100%;" />
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <div class="input-group" style="width:100%;" id="field_kp_display_state">
                                <span class="input-group-addon" style="width:110px;">노출여부</span>
                                <span class="form-control">
                                    <?=get_input_radio("kp_display_state", $this->config->item("kakao_product_display_state"), "Y", $this->config->item("kakao_product_display_state_text_color"));?>
                                </span>
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
     * 상품 검색
     */
    function product_search(url) {
        var kwd = $(form + ' input[name="kwd"]').val();
        if( empty(kwd) ) {
            $(form + ' input[name="kwd"]').focus();
            return false;
        }
        if( empty(url) ) {
            url = '/product/search_ajax/?page=1';
        }

        loadingBar.show($('#pop_product_list'));

        $('#pop_product_list').show();

        $.ajax({
            url : url,
            data : {kfd:'p_name', kwd:kwd},
            type : 'post',
            //dataType : 'html',
            dataType : 'json',
            success : function (result) {
                var html = get_product_list_html(result.data);
                $('#pop_product_list').html(html);

                loadingBar.hide();
            },
            error : function() {
                loadingBar.hide();
            }
        });
    }//end of product_search()

    /**
     * 상품 출력
     */
    function get_product_list_html(data) {

        if( empty(data) ) {
            return false;
        }

        var html = '';
        html += '<ul class="list-group">';

        $.each(data, function(index, item){
            var disp_text = '<span class="badge badge-primary">진열함</span>';
            var sale_text = '<span class="badge badge-primary">판매중</span>';
            var stock_text = '<span class="badge badge-primary">재고있음</span>';

            if( item.p_display_state == 'N' ) {
                disp_text = '<span class="badge badge-danger">진열안함</span>';
            }
            if( item.p_sale_state == 'N' ) {
                sale_text = '<span class="badge badge-danger">판매종료</span>';
            }
            if( item.p_stock_state == 'N' ) {
                stock_text = '<span class="badge badge-danger">품절</span>';
            }

            html += '<li class="list-group-item">';
            html += '   <div class="row">';
            html += '       <div class="pull-left"><img class="thumbnail" src="' + item.p_rep_image_array[1] + '" alt="" width="60" /></div>';
            html += '       <div class="pull-left mgl5">';
            html += '           <p><b>' + item.p_name + '</b></p>';
            html += '           <p>' + disp_text + ' ' + sale_text + ' ' + stock_text + '</p>';
            html += '       </div>';
            html += '       <div class="pull-right"><button type="button" class="btn btn-primary btn-xs" onclick="product_select(\'' + item.p_num + '\', \'' + item.p_name + '\', \'' + item.p_rep_image_array[1] + '\');">선택</button></div>';
            html += '   </div>';
            html += '</li>';
        });

        html += '</ul>';

        return html;
    }//end of product_list_print()

    /**
     * 상품 선택
     */
    function product_select(p_num, p_name, p_img) {
        info_message_all_clear();

        if( empty(p_num) ) {
            alert('잘못된 접근입니다.');
            return false;
        }

        $(form + ' [name="p_name"]').val(p_name);
        $(form + ' [name="kp_product_num"]').val(p_num);

        $('#pop_product_list').hide();
    }//end of product_select()


    //document.ready
    $(function(){
        $(form + ' input[name="kwd"]').focus();

        $(form + ' input[name="kwd"]').off('keypress').on('keypress', function(e){
            if( e.keyCode == 13 ) {
                product_search();
                e.preventDefault();
            }
        });

        <?php if( $req['div'] == 2 ) { ?>
        $('#category_select').show();
        <?php } ?>

        //submit check
        $(form).on('submit', function(){
            info_message_all_clear();

            // if( !$(form + ' [name="kp_title"]').val() ) {
            //     error_message($('#field_kp_title'), '제목을 입력하세요.');
            //     return false;
            // }
            // if( !$(form + ' [name="kp_content"]').val() ) {
            //     error_message($('#field_kp_content'), '내용을 입력하세요.');
            //     return false;
            // }

            Pace.restart();
        });

        //ajax form
        $('#pop_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            //beforeSubmit: function(formData, jqForm, options) {
            //},
            success: function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                    modalPop.hide();
                }
                else {
                    if( result.error_data ) {
                        $.each(result.error_data, function(key, msg){
                            if( $('#field_' + key).length ) {
                                error_message($('#field_' + key), msg);
                            }
                        });
                    }
                }//end of if()
            },
            complete : function() {
                //$(this).attr('action', this_form_action);
                Pace.stop();
            }
        });//end of ajax_form()
    });//end of document.ready()
</script>