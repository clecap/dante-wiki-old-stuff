<?php

class JSBreadCrumbsHooks {

  public static function onSiteNoticeAfter ( &$siteNotice, $skin) {
	  $siteNotice .= '<div id="breadcrumbinsert" style="height:20px;position: relative; top: -10px;text-align:left;overflow:hidden;background-color: #f6f6f6;border-color: #dcdcdc;border-radius: 3px;border-style: solid;border-width: 1px;padding-left:0.2em;"></div>';
		//	$siteNotice .= '<div id="breadcrumbinsert" class="breadcrumbClass" style="height:20px;position: relative; top: -10px;text-align:left;overflow:hidden;"></div>';
	}

	public static function onOutputPageBeforeHTML( OutputPage &$out, &$text ) {
  $out->addModules ( ["ext.JSBreadCrumbs"] );  
 	$out->addModuleStyles ( "ext.JSBreadCrumbs" );   // added CHC
	
  }

	/**
	 * Implements BeforePageDisplay hook.
	 * See https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
	 * Initializes variables to be passed to JavaScript.
	 *
	 * @param OutputPage $output OutputPage object
	 * @param Skin $skin Skin object that will be used to generate the page
	 */
	public static function onBeforePageDisplay( $output, $skin ) {
		$vars = [];
		$vars['SiteMaxCrumbs'] = 5;
		$vars['GlobalMaxCrumbs'] =  20;       
		$vars['ShowAction'] = false;
		$vars['ShowSite'] = false;
		$vars['Domain'] = false;
		$vars['Horizontal'] = true;
		$vars['CSSSelector'] = "";
		$vars['LeadingDescription'] =  ""; 
		$vars['MaxLength'] = 40;
	
		$title = $output->getTitle();
		if ( self::getDisplayTitle( $title, $displayTitle ) ) {$pagename = $displayTitle;} 
		else {$pagename = $title->getPrefixedText();}	
		$vars['PageName'] = $pagename;
		$action = Action::getActionName( $output->getContext() );
		
		if ( $action === 'view' ) {$vars['Action'] = '';} else {
			$message = wfMessage( $action );
			if ( $message->isBlank() ) {$vars['Action'] = $action;} else {$vars['Action'] = $message->parse();}
		}

		$output->addJSConfigVars ( 'JSBreadCrumbs', $vars );
		$output->addModules      ( 'ext.JSBreadCrumbs' );
		$output->addModuleStyles ( "ext.JSBreadCrumbs" ); 
	}


	/**
	 * Get displaytitle page property text.
	 *
	 * @since 1.0
	 * @param Title $title the Title object for the page
	 * @param string &$displaytitle to return the display title, if set
	 * @return bool true if the page has a displaytitle page property that is
	 * different from the prefixed page name, false otherwise
	 */
	private static function getDisplayTitle( Title $title, &$displaytitle ) {
		$pagetitle = $title->getPrefixedText();
		$title = $title->createFragmentTarget( '' );
		if ( $title instanceof Title && $title->canExist() ) {
			$values = PageProps::getInstance()->getProperties( $title, 'displaytitle' );
			$id = $title->getArticleID();
			if ( array_key_exists( $id, $values ) ) {
				$value = $values[$id];
				if ( trim( str_replace( '&#160;', '', strip_tags( $value ) ) ) !== '' &&
					$value !== $pagetitle ) {
					$displaytitle = $value;
					return true;
				}
			}
		}
		return false;
	}
}
