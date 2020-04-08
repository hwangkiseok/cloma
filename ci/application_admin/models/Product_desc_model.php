<?php
/**
 * 상품설명이미지 관련모델
 */
class Product_desc_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 상품 설명이미지
     * @param $arrayParams    : 업로드된 이미지 info
     * @return void
     */
    public function img_upsert($arrayParams){

        for ($i = 0; $i <  count($arrayParams); $i++) { $row = $arrayParams[$i];


            $aBindParams = array($row['FILE_GUBUN'],$row['URL'],$row['ORG_NAME'],$row['ENC_NAME'],$row['FILE_TYPE'],$row['FILE_SIZE']);

            $sql = "INSERT INTO product_desc_img (
                        gubun
                        ,url
                        ,org_name
                        ,enc_name
                        ,file_type
                        ,file_size
                    ) 
                    VALUES (
                          ?
                        , ?
                        , ?
                        , ?
                        , ?
                        , ?
                    );";

            $this->db->query($sql,$aBindParams);

        }

    }

    public function img_chk($arrayParams){

        $aBindParams = array($arrayParams['p_desc'],$arrayParams['open_flag']);
        $sql = "update product_desc_img set open_flag = case when p_desc = ? then ? else 'N' end where gubun = '2'";
        return $this->db->query($sql,$aBindParams);

    }

    public function img_default($arrayParams){

        $aBindParams = array($arrayParams['p_desc'],$arrayParams['default_flag']);
        $sql = "update product_desc_img set default_flag = case when p_desc = ? then ? else 'N' end where gubun = '1'";
        return $this->db->query($sql,$aBindParams);

    }

    public function img_del($param){

        $aBindParams = array($param);
        $sql = " delete from product_desc_img where p_desc = ? ";
        return $this->db->query($sql,$aBindParams);

    }

    public function getLists(){

        $sql = " select * from product_desc_img ";
        return $this->db->query($sql)->result();

    }


}//end of class Product_model