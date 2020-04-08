<style>
    #order_frame {width: 100%;border: none;height: 6000px;overflow-y: visible}
</style>

<form name="order_form" target="order_frame" method="post" action="<?=$target_url?>">
    <input type="hidden" name="shop_id" value="<?=$aInput['shop_id']?>">
    <!--<input type="hidden" name="a_session_id" value="<?=$aInput['a_session_id']?>">-->
    <input type="hidden" name="a_buyer_name" value="<?=$aInput['a_buyer_name']?>">
    <input type="hidden" name="a_buyer_hhp" value="<?=$aInput['a_buyer_hhp']?>">
    <input type="hidden" name="a_receiver_name" value="<?=$aInput['a_receiver_name']?>">
    <input type="hidden" name="a_receiver_hhp" value="<?=$aInput['a_receiver_hhp']?>">
    <input type="hidden" name="a_receiver_zip" value='<?=$aInput['a_receiver_zip']?>'>
    <input type="hidden" name="a_receiver_addr1" value="<?=$aInput['a_receiver_addr1']?>">
    <input type="hidden" name="a_receiver_addr2" value="<?=$aInput['a_receiver_addr2']?>">

    <input type="hidden" name="a_basket_info" value='<?=$aInput['option_info']?>'>
    <input type="hidden" name="a_campaign" value="<?=$aInput['a_campaign']?>">
    <input type="hidden" name="a_referer" value="<?=$aInput['a_referer']?>">
    <input type="hidden" name="partner_buyer_id" value="<?=$aInput['partner_buyer_id']?>">
    <input type="hidden" name="a_code" value="<?=$aInput['a_code']?>">
    <input type="hidden" name="toggle_header" value="<?=$aInput['toggle_header']?>">
    <input type="hidden" name="a_cart_yn" value="<?=$aInput['cart_yn']?>">
    <input type="hidden" name="a_payway_cd" value="1">

</form>

<iframe id="order_frame" name="order_frame"></iframe>

<script type="text/javascript">
    $(function(){
        $('form[name="order_form"]').submit();
    })
</script>


