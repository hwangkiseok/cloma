<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품MD관리 > 목록</h4>
    </div>

    <div class="row well pd10">
        <div class="row">
            <div id="md_count_buttons" class="col-sm-10 col-xs-9" style="line-height:29px;">
                <span><a href="#none" class="btn btn-info btn-sm active" onclick="form_submit('md_div=');">전체 <b id="md_count_total">0</b> 건</a></span>
                <?php foreach($this->config->item('product_md_division') as $key => $item ) { ?>
                <span><a href="#none" class="btn btn-info btn-sm" onclick="form_submit('md_div=<?php echo $key; ?>');"><?php echo $item; ?> <b id="md_count_<?php echo $key; ?>">0</b> 건</a></span>
                <?php } ?>
            </div>
            <!--<div class="col-sm-2 col-xs-3 text-right">-->
            <div class="pull-right text-right mgr15">
                <a href="#none" onclick="pop_insert_product_md();" class="btn btn-primary btn-sm">상품MD등록</a>
            </div>
        </div>
    </div>

    <div class="row">
        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="md_div" value="<?php echo $req['md_div']; ?>" />
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-left">
                <button type="button" class="btn btn-primary btn-sm" onclick="md_order_update();">진열순서변경</button>
            </div>
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="product_list" style="position:relative;"></div>
    </div>
</div>

<script>
    /**
     * form submit
     */
    function form_submit(str) {
        var arr1 = str.split('&');
        for(var i in arr1) {
            if( !empty(arr1[i]) ) {
                var arr2 = arr1[i].split('=');
                var name = arr2[0];
                var val = arr2[1];

                $('#search_form [name="' + name + '"]').val(val);
            }
        }//end of for()

        $('#search_form').submit();
    }//end of form_submit()

    /**
     * 상품 MD 등록 팝업
     */
    function pop_insert_product_md() {
        //modal 출력
        var container = $("<div>");
        $(container).load('<?php echo $this->page_link->base; ?>/insert_pop');

        modalPop.createPop("상품 MD 등록", container);
        modalPop.createButton("등록", "btn btn-primary btn-sm", function(){
            $('#pop_form').submit();
        });
        modalPop.createCloseButton("취소", "btn btn-default btn-sm", function(){});
        modalPop.show();
    }//end of pop_insert_product_md()

    /**
     * MD 삭제
     * @param pmd_num
     */
    function md_delete(md_div, p_num) {
        if( !confirm('삭제하시겠습니까?') ) {
             return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {md_div:md_div, p_num:p_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message_type == 'alet' ) {
                    alert(result.message);
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of md_delete()

    /**
     * 상품 MD 순서변경
     */
    function md_order_update () {
        var div_pdt = {};
        $.each($('[name*="pmd_order[]"]'), function(){
            div_pdt[$(this).attr('data-num')] = $(this).val();
        });

        Pace.restart();

        $.ajax({
            url : '/product_md/order_proc',
            data : {data:div_pdt},
            type : 'post',
            dataType : 'json',
            success : function (result) {
                if( result.message_type == 'alert' ) {
                    alert(result.message);
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of md_order_update()


    //document.ready
    $(function () {
        $('#md_count_buttons a.btn').on('click', function(){
            $('#md_count_buttons a.btn').removeClass('active');
            $(this).addClass('active');
        });

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //목록 체크박스 click
        $(document).on('click', '#all_list_check', function(){
            $('[name*="num["]').prop('checked', $(this).prop('checked'));
        });

        //Ajax Form
        if( $('#search_form').length > 0 ) {
            $('#search_form').ajaxForm({
                type: 'post',
                dataType: 'html',
                beforeSubmit: function(formData, jqForm, options) {
                    loadingBar.show($('#product_list'));
                },
                success: function(result) {
                    $('#product_list').html(result);
                },
                complete: function() {
                    loadingBar.hide();
                }
            });//end of ajax_form()

            $('#search_form').submit();

            //ajax page
            $(document).on('click', '#product_list .pagination.ajax a', function(e){
                e.preventDefault();

                if( $(this).attr('href') == '#none' ) {
                    return false;
                }

                $('#search_form').attr('action', $(this).attr('href'));
                $('#search_form').submit();
            });
        }//end of if()
    });//en d of document.ready
</script>