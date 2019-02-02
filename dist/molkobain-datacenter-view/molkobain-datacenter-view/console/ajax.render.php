<?php
/**
 * Copyright (c) 2015 - 2019 Molkobain.
 *
 * This file is part of licensed extension.
 *
 * Use of this extension is bound by the license you purchased. A license grants you a non-exclusive and non-transferable right to use and incorporate the item in your personal or commercial projects. There are several licenses available (see https://www.molkobain.com/usage-licenses/ for more informations)
 */

@include_once('../approot.inc.php');
@include_once('../../approot.inc.php');
@include_once('../../../approot.inc.php');
require_once(APPROOT.'application/application.inc.php');
require_once(APPROOT.'application/webpage.class.inc.php');
require_once(APPROOT.'application/ajaxwebpage.class.inc.php');

try
{
	require_once(APPROOT.'/application/startup.inc.php');
	require_once(APPROOT.'/application/user.preferences.class.inc.php');

	require_once(APPROOT.'/application/loginwebpage.class.inc.php');
	LoginWebPage::DoLoginEx('backoffice', false);

	$oPage = new ajax_page("");
	$oPage->no_cache();

	$sOperation = utils::ReadParam('operation', '');
	$sClass = utils::ReadParam('class', '', false, 'class');
	$iId = (int) utils::ReadParam('id', 0);

	$oObject = MetaModel::GetObject($sClass, $iId);
	$oDatacenterView = \Molkobain\iTop\Extension\DatacenterView\Common\DatacenterViewFactory::BuildFromObject($oObject);

	switch ($sOperation)
	{
		case $oDatacenterView::ENUM_ENDPOINT_OPERATION_RENDERTAB:
			$oPage->SetContentType('text/html');
			$oOutput = $oDatacenterView->Render();

			// HTML
			$oPage->add($oOutput->GetHtml());
			// JS inline
			if(!empty($oOutput->GetJs()))
			{
				$oPage->add_ready_script($oOutput->GetJs());
			}
			// CSS inline
			if(!empty($oOutput->GetCss()))
			{
				$oPage->add_style($oOutput->GetCss());
			}
			// JS & CSS files should have been added when adding tab
			break;

		default:
			$oPage->p("Invalid query.");
	}

	$oPage->output();
} catch (Exception $e)
{
	// Note: Transform to cope with XSS attacks
	echo htmlentities($e->GetMessage(), ENT_QUOTES, 'utf-8');
	IssueLog::Error($e->getMessage()."\nDebug trace:\n".$e->getTraceAsString());
}