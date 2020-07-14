<script type="text/javascript" src="/js/clipboard.min.js"></script>
<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">재고상품 관리 > 재고상품 조회</h4>
    </div>

    <div class="row">

        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="p_name">상품명</option>
                                <option value="p_order_code">상품코드</option>
                                <option value="p_detail">상품내용</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                        <!--<span class="input-group-btn" style="width:auto">-->
                        <!--    <button type="submit" class="btn btn-primary btn-sm">검색</button>-->
                        <!--</span>-->
                    </div>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="srh_proc_yn">처리상태</label>
                <div class="col-sm-10">
                    <div class="input-group">
                            <label><input type="radio" name="srh_proc_yn" value="" checked> 전체</label>&nbsp;&nbsp;
                            <label><input type="radio" name="srh_proc_yn" value="N"> 확인필요</label>&nbsp;&nbsp;
                            <label><input type="radio" name="srh_proc_yn" value="Y"> 확인완료</label>&nbsp;&nbsp;
                            <label><input type="radio" name="srh_proc_yn" value="I"> 무시</label>

                    </div>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="srh_issue_yn">재고부족옵션</label>
                <div class="col-sm-10">
                    <div class="input-group">
                            <label><input type="radio" name="srh_issue_yn" value="" checked> 전체</label>&nbsp;&nbsp;
                            <label><input type="radio" name="srh_issue_yn" value="Y"> 있음</label>&nbsp;&nbsp;
                            <label><input type="radio" name="srh_issue_yn" value="N"> 없음</label>

                    </div>
                </div>
            </div>


            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-left">
                <button type="button" class="btn btn-danger btn-sm" onclick="product_del();">선택삭제</button>
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


<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>

<script>


    function product_del(){

        if( $('input[name="p_num[]"]:checked').length < 1 ){
            alert('재고상품에서 제거할 상품을 선택해주세요.');
            return false;
        }

        if(confirm('선택하신 상품을 재고관리 메뉴에서 제거하시겠습니까 ?') == false) return false;

        var arr = [];

        $('input[name="p_num[]"]:checked').each(function(k,r){
            arr.push($(r).val());
        });

        $.ajax({
            url: '/product/stock_del/',
            type: 'post',
            data : {p_num : arr},
            dataType: 'json',
            success: function (result) {

                if(result.msg) { alert(result.msg); }
                if(result.success) { $('#search_form').submit(); }

            }
        });

    }

    /**
     * 정렬 submit
     * @param sf
     * @param st
     */
    function order_submit(sf, st) {
        $('#search_form input[name="sort_field"]').val(sf);
        $('#search_form input[name="sort_type"]').val(st);
        $('#search_form').submit();
    }//end of form_submit()

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

    $(document).on('click','.showIssue',function(){

        var p_order_code = $(this).data('p_order_code');

        var container = $('<div>');
        $(container).load('/product_stock/option_pop/?p_order_code='+p_order_code);

        modalPop.createPop('재고부족옵션', container);
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();

    });

    $(document).on('click','.pop_proc',function(){

        var proc_yn = $(this).data('proc_yn');
        var p_num = $(this).data('p_num');

        var add_checked_N = proc_yn=="N"?'checked':'';
        var add_checked_Y = proc_yn=="Y"?'checked':'';
        var add_checked_I = proc_yn=="I"?'checked':'';

        var html  = "<ul>";
            html += "   <li style='list-style: none'><label><input type='radio' name='proc_yn' value='Y' "+add_checked_Y+"> 확인완료</label></li>";
            html += "   <li style='list-style: none'><label><input type='radio' name='proc_yn' value='N' "+add_checked_N+"> 확인필요</label></li>";
            html += "   <li style='list-style: none'><label><input type='radio' name='proc_yn' value='I' "+add_checked_I+"> 무시</label></li>";
            html += "</ul>";
            html += "<input type='hidden' name='proc_yn_pnum' value='"+p_num+"'>";

        var container = $('<div>'+html+'</div>');

        modalPop.createPop('처리상태 변경', container);
        modalPop.createButton('변경', 'btn btn-primary btn-sm', change_proc_yn );
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();

    });

    function change_proc_yn(){

        var val = $('input[name="proc_yn"]:checked').val();
        var p_num = $('input[name="proc_yn_pnum"]').val();

        $.ajax({
            url: '/product_stock/set_flag',
            data: {proc_yn : val , p_num : p_num},
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {

                if(result.msg) alert(result.msg);

                if(result.success) {
                    modalPop.hide();
                    $('#search_form').submit();
                }

            }
        });

    }


    //document.ready
    $(function () {

        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

        //목록 체크박스 click
        $(document).on('click', '#all_list_check', function(){
            $('[name="p_num[]"]').prop('checked', $(this).prop('checked'));
        });

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //Ajax Form
        if( $('#search_form').length > 0 ) {
            $('#search_form').ajaxForm({
                //url : '<?php //echo $this->page_link->list_ajax; ?>//',
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

                // $('#search_form').attr('action', $(this).attr('href'));
                // $('#search_form').submit();
                form_submit($(this).attr('href'));
            });
        }//end of if()
    });//en d of document.ready
</script>