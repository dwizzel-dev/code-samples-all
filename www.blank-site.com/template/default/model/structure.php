<?php
/**
@auth:	Dwizzel
@date:	XX-XX-XXXX
@info:	structure data

@more:
		AboutPage
		CheckoutPage
		CollectionPage
		ContactPage
		ItemPage
		ProfilePage
		QAPage
		SearchResultsPage

		schema:pagination
		SiteNavigationElement - schema.org
		breadcrumb - schema.org
		category - schema.org

*/

//--------------------------------------------------------

if(isset($arrOutput['structure'])){
	if($arrOutput['structure'] == 'medicalepage'){
		//structure data for medical page
		$arrOutput['structure'] = array(	
			'lang' => $oGlob->get('lang_prefix'),
			'name' => $arrOutput['meta']['title'],
			'description' => $arrOutput['meta']['description'],
			'shema' => 'http://health-lifesci.schema.org/MedicalWebPage',
			);

	}else if($arrOutput['structure'] == 'webpage'){
		//structure data for satndard webpage
		$arrOutput['structure'] = array(	
			'lang' => $oGlob->get('lang_prefix'),
			'name' => $arrOutput['meta']['title'],
			'description' => $arrOutput['meta']['description'],
			'shema' => 'http://schema.org/WebPage',
			);
	}else if($arrOutput['structure'] == 'searchpage'){
		//structure data for satndard webpage
		$arrOutput['structure'] = array(	
			'lang' => $oGlob->get('lang_prefix'),
			'name' => $arrOutput['meta']['title'],
			'description' => $arrOutput['meta']['description'],
			'shema' => 'https://schema.org/SearchResultsPage',
			);
		}
	//le reste de l'infos
	$arrOutput['structure']['organisation'] = array(
		'url' => 'http://www.blank-site.com',
		'email' => '',
		'telephone' => '',
		'logo' => 'http://www.blank-site.com/images/default/logo.png',
		'legalName' => 'Physiotec',
		);	
	$arrOutput['structure']['specialty'] = array(
		'name' => 'physiotherapy',
		'description' => '(HEP) home exercise program for physiotherapy',
		);	
	$arrOutput['structure']['copyrightHolder'] = array(
		'url' => 'http://www.blank-site.com',
		'email' => '',
		'telephone' => '',
		'logo' => 'http://www.blank-site.com/images/default/logo.png',
		'legalName' => 'Physiotec',
		);
	}

//END