<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

/*echo "<pre>";	
print_r($arResult);
echo "</pre>"; 
	 
echo count($arResult['IDS']);*/


	 
if (count($arResult['IDS']) > 0 && CModule::IncludeModule('sale'))
{
	$arItemsInCompare = array();
	foreach ($arResult['IDS'] as $ID)
	{
		if (isset(
			$_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$ID]
		))
			$arItemsInCompare[] = $ID;
	}

	$dbBasketItems = CSaleBasket::GetList(
		array(
			"ID" => "ASC"
		),
		array(
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL",
			),
		false,
		false,
		array()
	);

	$arPageItems = array();
	$arPageItemsDelay = array();
	while ($arItem = $dbBasketItems->Fetch())
	{
		if (in_array($arItem['PRODUCT_ID'], $arResult['IDS']))
		{
			if($arItem["DELAY"] == "Y")
				$arPageItemsDelay[] = $arItem['PRODUCT_ID'];
			else
				$arPageItems[] = $arItem['PRODUCT_ID'];
		}
	}
	
	if (count($arPageItems) > 0 || count($arPageItemsDelay) > 0)
	{
		echo '<script type="text/javascript">$(function(){'."\r\n";
		foreach ($arPageItems as $id) 
		{
			echo "disableAddToCart('catalog_add2cart_link_".$id."', 'list', '".GetMessage("CATALOG_IN_CART")."');\r\n";
		}
		foreach ($arPageItemsDelay as $id) 
		{
			echo "disableAddToCart('catalog_add2cart_link_".$id."', 'list', '".GetMessage("CATALOG_IN_CART_DELAY")."');\r\n";
		}
		echo '})</script>';
	}
	
}
?>
