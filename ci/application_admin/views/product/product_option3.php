
<form method="post" id="pop_insert_form" action="/product/upsert_option">

    <div style="border:1px solid #ddd;border-radius: 5px;margin-top: -72px; margin-bottom: 8px;padding: 16px;position: fixed;width: calc(100% - 32px);background: #fff;z-index: 10">
        <button class="btn btn-sm btn-success option_add">옵션추가</button>
    </div>

    <table class="table table-hover" style="margin-top: 64px">

        <colgroup>
            <col style="width: 30px" />
            <col style="width: 60px" />
            <col style="width: 150px" />
            <col style="width: 150px" />
            <col style="width: 150px" />
            <col style="width: 120px" />
            <col style="width: 120px" />
            <col style="width: 120px" />
            <col style="width: 100px" />
            <col style="width: 150px" />
            <col />
        </colgroup>

        <thead>
            <tr>
                <th class="active">No.</th>
                <th class="active">순서</th>
                <th class="active">1차 옵션명</th>
                <th class="active">2차 옵션명</th>
                <th class="active">3차 옵션명</th>
                <th class="active">판매가</th>
                <th class="active">소비자가</th>
                <th class="active">공급가</th>
                <th class="active">재고</th>
                <th class="active">추가옵션<br>여부</th>

                <th class="active">옵션이미지</th>
                <th class="active">삭제</th>
            </tr>
        </thead>

        <tbody>
            <tr class="insert">
                <input type="hidden" name="option_id[1]" value="" />
                <td>1</td>
                <td><input type="text" name="sort[1]" value="1" class="form-control" /></td>
                <td><input type="text" name="option_1[1]" value="" class="form-control"  /></td>
                <td><input type="text" name="option_2[1]" value="" class="form-control"  /></td>
                <td><input type="text" name="option_3[1]" value="" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_sale_price[1]" value="" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_org_price[1]" value="" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_supply_price[1]" value="" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_stock[1]" value="" class="form-control"  /></td>
                <td style="text-align: left!important;">
                    <label><input type="radio" name="option_add[1]" value="Y" />&nbsp;추가옵션</label><br>
                    <label><input type="radio" name="option_add[1]" value="N" checked />&nbsp;추가옵션아님</label>
                </td>
                <td>
                    <input type="file" name="option_img[1]" value="" class="form-control" accept="image/*"  />
                    <p class="help-block" style="text-align: left!important;;">- 정책필요</p>
                </td>
                <td>
                    <button class="btn btn-danger btn-xs option_del">삭제</button>
                </td>
            </tr>
        </tbody>
    </table>

</form>
<script>
    $(function(){

        $('#pop_insert_form').on('submit',function(){



        });

        $('#pop_insert_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(formData, jqForm, options) {
            },
            success: function(result) {
                console.log(result);
            },
            complete: function() {
            }
        });//end of ajax_form()


        var add_html  = '<tr class="insert">';
            add_html += '<input type="hidden" name="option_id[{num}]" value="" />';
            add_html += '<td>{num}</td>';
            add_html += '<td><input type="text" name="sort[{num}]" value="{num}" class="form-control" /></td>';
            add_html += '<td><input type="text" name="option_1[{num}]" value="" class="form-control"  /></td>';
            add_html += '<td><input type="text" name="option_2[{num}]" value="" class="form-control"  /></td>';
            add_html += '<td><input type="text" name="option_3[{num}]" value="" class="form-control"  /></td>';
            add_html += '<td><input type="text" numberOnly name="option_sale_price[{num}]" value="" class="form-control"  /></td>';
            add_html += '<td><input type="text" numberOnly name="option_org_price[{num}]" value="" class="form-control"  /></td>';
            add_html += '<td><input type="text" numberOnly name="option_supply_price[{num}]" value="" class="form-control"  /></td>';
            add_html += '<td><input type="text" numberOnly name="option_stock[{num}]" value="" class="form-control"  /></td>';
            add_html += '<td style="text-align: left!important;">';
            add_html += '   <label><input type="radio" name="option_add[{num}]" value="Y" />&nbsp;추가옵션</label><br>';
            add_html += '   <label><input type="radio" name="option_add[{num}]" value="N" checked />&nbsp;추가옵션아님</label>';
            add_html += '</td>';
            add_html += '<td>';
            add_html += '<input type="file" name="option_img[{num}]" value="" class="form-control" accept="image/*"  />';
            add_html += '   <p class="help-block" style="text-align: left!important;">- 정책필요</p>';
            add_html += '</td>';
            add_html += '<td>';
            add_html += '   <button class="btn btn-danger btn-xs option_del">삭제</button>';
            add_html += '</td>';
            add_html += '</tr>';

        $('.option_add').on('click',function(e){
            e.preventDefault();

            var add_num = parseInt($('#pop_insert_form table tbody tr').length)+1;
            var html = add_html.replace(/{num}/g,add_num)

            $('#pop_insert_form table tbody').append(html);
            $('.option_wrap').scrollTop(9999999);
        });

    })


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
                console.log('b');
            }
        }
    });


</script>
