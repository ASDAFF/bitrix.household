<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;
	

$iblockID=0;
$c=1;

$wizard =&$this->GetWizard();

$iblockCode = "stoves_".WIZARD_SITE_ID; 
$iblockType = "catalog"; 
$iblockID = false;

$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
if ($rsIBlock && $arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"]; 
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		CIBlock::Delete($arIBlock["ID"]); 
		$iblockID = false; 
	}
}

if ($wizard->GetVar("installDemoData")<>"Y")
{
	if(!$iblockID)
	{
		$rsIBlock = CIBlock::GetList(array(), array("CODE" => "stoves", "TYPE" => $iblockType));
		if ($rsIBlock && $arIBlock = $rsIBlock->Fetch())		
			$iblockID = $arIBlock["ID"]; 
	}
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."_index.php", Array("STOVES_IBLOCK_ID" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."bestprice.php", Array("STOVES_IBLOCK_ID" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."hit.php", Array("STOVES_IBLOCK_ID" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."novelty.php", Array("STOVES_IBLOCK_ID" => $iblockID));

	return;
}
$add_path=$wizard->GetVar("install_data");
$iblockXMLFile =WIZARD_SERVICE_RELATIVE_PATH.'/xml/stoves.xml'; 
$iblockXMLFilePrice =WIZARD_SERVICE_RELATIVE_PATH.'/xml/stoves_prices.xml'; 


if ($iblockID == false)
{
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"tmp_household_stoves",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions = Array(
			"1" => "X",
			"2" => "R"
		)
	);
	
	if ($iblockID < 1)
		return;
	
	$iblockID2 = WizardServices::ImportIBlockFromXML(
		$iblockXMLFilePrice,
		"tmp_household_stoves",
		$iblockType."_prices",
		WIZARD_SITE_ID,
		$permissions = Array(
			"1" => "X",
			"2" => "R"
		)
	);
	
	$iblock = new CIBlock;
		
	$arFields = Array(
				'XML_ID' => $iblockCode,
				'EXTERNAL_ID' => $iblockCode,
				'CODE' => $iblockCode,			
			);
	
	$iblock->Update($iblockID, $arFields);
}
else
{
	$arSites = array(); 
	$db_res = CIBlock::GetSite($iblockID);
	while ($res = $db_res->Fetch())
		$arSites[] = $res["LID"]; 
	if (!in_array(WIZARD_SITE_ID, $arSites))
	{
		$arSites[] = WIZARD_SITE_ID;
		$iblock = new CIBlock;
		$iblock->Update($iblockID, array("LID" => $arSites));
	}
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/stoves/index.php", Array("STOVES_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."_index.php", Array("STOVES_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."bestprice.php", Array("STOVES_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."hit.php", Array("STOVES_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."novelty.php", Array("STOVES_IBLOCK_ID" => $iblockID));

?>
