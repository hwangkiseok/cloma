<style>
    #order_frame {width: 100%;border: none;height: 6000px;overflow-y: visible}
</style>

<form name="order_form" target="order_frame" method="post" action="<?=$target_url?>" >
    <input type="hidden" name="item_no" value="<?=$aInput['item_no']?>">
    <input type="hidden" name="a_campaign" value="<?=$aInput['a_campaign']?>">
    <input type="hidden" name="a_referer" value="<?=$aInput['a_referer']?>">
    <input type="hidden" name="partner_buyer_id" value="<?=$aInput['partner_buyer_id']?>">
    <input type="hidden" name="a_code" value="<?=$aInput['a_code']?>">
    <input type="hidden" name="toggle_header" value="<?=$aInput['toggle_header']?>">
    <input type="hidden" name="a_option_info" value='<?=$aInput['option_info']?>'>
    <input type="hidden" name="partner_seller_id" value="<?=$this->config->item('form_api_id')?>">
    <input type="hidden" name="a_buy_count" value="<?=$aInput['buy_count']?>">

    <input type="hidden" name="a_buyer_name" value="<?=$aLastOrder['buyer_name']?>">
    <input type="hidden" name="a_buyer_hhp" value="<?=$aLastOrder['buyer_hhp']?>">
    <input type="hidden" name="a_receiver_name" value="<?=$aLastOrder['receiver_name']?>">
    <input type="hidden" name="a_receiver_hhp" value="<?=$aLastOrder['receiver_tel']?>">
    <input type="hidden" name="a_receiver_zip" value="<?=$aLastOrder['receiver_zip']?>">
    <input type="hidden" name="a_receiver_addr1" value="<?=$aLastOrder['receiver_addr1']?>">
    <input type="hidden" name="a_receiver_addr2" value="<?=$aLastOrder['receiver_addr2']?>">
    <input type="hidden" name="a_cart_yn" value="<?=$aInput['cart_yn']?>">
    <input type="hidden" name="a_payway_cd" value="1">

</form>

<iframe id="order_frame" name="order_frame"></iframe>

<script type="text/javascript">
    $(function(){
        $('form[name="order_form"]').submit();
    })
</script>


