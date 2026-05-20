<?php
require_once("../includes/config.php");
function getAllCustomerCategory(){
    global $link1;
    $arr = [];
    $cus_query = "SELECT customer_type FROM customer_type WHERE status='1' ORDER BY customer_type";
    $resupt = mysqli_query($link1, $cus_query);

    if($resupt && mysqli_num_rows($resupt) > 0){
        while($row = mysqli_fetch_assoc($resupt)){
            $arr[] = $row;
        }
    }
    return json_encode($arr);
}
function getAlProduct($access_product){
    global $link1;
    $arr = [];
    $product="SELECT * FROM product_master where status = '1'   and product_id in (".$access_product.") order by product_name";
    $resupt = mysqli_query($link1, $product);
    if($resupt && mysqli_num_rows($resupt) > 0){
        while($row = mysqli_fetch_assoc($resupt)){
            $arr[] = $row;
        }
    }
    return json_encode($arr);
}

function getBrand($access_brand)
{
    global $link1;
    $arr = [];
    $dept_query="SELECT * FROM brand_master where status = '1'  and brand_id in (".$access_brand.")   order by brand";
    $check_dept=mysqli_query($link1,$dept_query);
    while($br_dept = mysqli_fetch_array($check_dept)){
        $arr[] = $br_dept;
    }
    return json_encode($arr);
}