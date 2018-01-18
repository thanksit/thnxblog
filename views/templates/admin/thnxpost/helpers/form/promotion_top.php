<?php
$url = $_GET['url'];
$val = rand(1,2);
if($val == 1){
	$src = $url."promo_top.png";
	$href = "https://themeforest.net/item/Platinum-fashion-shop-prestashop-theme/14100073?ref=thanksit";
}elseif($val == 2){
	$src = $url."promo_top_1.png";
	$href = "https://themeforest.net/item/great-store-ecommerce-prestashop-theme/18303739?ref=thanksit";
}else{
	$src= $url."promo_top.png";
	$href= "https://themeforest.net/item/Platinum-fashion-shop-prestashop-theme/14100073?ref=thanksit";
}
echo "<div class='".$val." col-lg-12' style='margin-top:5px;margin-bottom:5px;'><a target='_blank' title='Click Here To Get This' href='".$href."'><img style='max-width:100%;height:auto;' src='".$src."' alt='promotional banner'></a></div>";

?>