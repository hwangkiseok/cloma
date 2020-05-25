
<form method="post" id="pop_insert_form" action="/product/upsert_option">

    <input type="hidden" name="p_num" value="<?=$aProductInfo['p_num']?>" />
    <input type="hidden" name="data_obj" value="" />

    <div style="border:1px solid #ddd;border-radius: 5px;margin-top: -72px; margin-bottom: 8px;padding: 16px;position: fixed;width: calc(100% - 32px);background: #fff;z-index: 10">
        <button class="btn btn-sm btn-success option_add">옵션추가</button>
    </div>

    <table class="table table-hover" style="margin-top: 64px">

        <colgroup>
            <col style="width: 30px" />
            <col style="width: 60px" />
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
                <input type="hidden" name="option_id[]" value="" />
                <td>1</td>
                <td><input type="text" name="sort[]" value="1" class="form-control" /></td>
                <td><input type="text" name="option_1[]" value="" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_sale_price[]" value="" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_org_price[]" value="" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_supply_price[]" value="" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_stock[]" value="" class="form-control"  /></td>
                <td style="text-align: left!important;">
                    <select name="option_add[]"  class="form-control">
                        <option value="N">추가옵션 아님</option>
                        <option value="Y">추가옵션</option>
                    </select>
                </td>
                <td>
                    <input type="file" name="option_img[0]" value="" class="form-control" accept="image/*"  />
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

            var option_name1 = [];
            var sale_price = [];
            var org_price = [];
            var supply_price = [];
            var stock = [];
            var add = [];

            $('input[name="option_1[]"]').each(function(){
                option_name1.push($(this).val());
            });
            $('input[name="option_sale_price[]"]').each(function(){
                sale_price.push($(this).val());
            });
            $('input[name="option_org_price[]"]').each(function(){
                org_price.push($(this).val());
            });
            $('input[name="option_supply_price[]"]').each(function(){
                supply_price.push($(this).val());
            });
            $('input[name="option_stock[]"]').each(function(){
                stock.push($(this).val());
            });
            $('select[name="option_add[]"]').each(function(){
                add.push($(this).val());
            });

            var data_obj = new Object;

            data_obj.option_name1 = option_name1;
            data_obj.sale_price = sale_price;
            data_obj.org_price = org_price;
            data_obj.supply_price = supply_price;
            data_obj.stock = stock;
            data_obj.add = add;
            data_obj.tot_cnt = option_name1.length;

            $('input[name="data_obj"]').val(JSON.stringify(data_obj));

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
        add_html += '<input type="hidden" name="option_id[]" value="" />';
        add_html += '<td>{num}</td>';
        add_html += '<td><input type="text" name="sort[]" value="{num}" class="form-control" /></td>';
        add_html += '<td><input type="text" name="option_1[]" value="" class="form-control"  /></td>';
        add_html += '<td><input type="text" numberOnly name="option_sale_price[]" value="" class="form-control"  /></td>';
        add_html += '<td><input type="text" numberOnly name="option_org_price[]" value="" class="form-control"  /></td>';
        add_html += '<td><input type="text" numberOnly name="option_supply_price[]" value="" class="form-control"  /></td>';
        add_html += '<td><input type="text" numberOnly name="option_stock[]" value="" class="form-control"  /></td>';
        add_html += '<td style="text-align: left!important;">';
        add_html += '   <select name="option_add[]" class="form-control">';
        add_html += '       <option value="N">추가옵션 아님</option>';
        add_html += '       <option value="Y">추가옵션</option>';
        add_html += '   </select>';
        add_html += '</td>';
        add_html += '<td>';
        add_html += '<input type="file" name="option_img[{curr_num}]" value="" class="form-control" accept="image/*"  />';
        add_html += '   <p class="help-block" style="text-align: left!important;">- 정책필요</p>';
        add_html += '</td>';
        add_html += '<td>';
        add_html += '   <button class="btn btn-danger btn-xs option_del">삭제</button>';
        add_html += '</td>';
        add_html += '</tr>';

        $('.option_add').on('click',function(e){
            e.preventDefault();

            var curr_num = parseInt($('#pop_insert_form table tbody tr').length);
            var add_num = curr_num+1;
            var html = add_html.replace(/{num}/g,add_num);
                html = html.replace(/{curr_num}/g,curr_num);

            $('#pop_insert_form table tbody').append(html);
            $('.option_wrap').scrollTop(9999999);
        });

    })

</script>
