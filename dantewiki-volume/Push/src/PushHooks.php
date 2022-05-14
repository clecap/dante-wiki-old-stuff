<?php

use MediaWiki\MediaWikiServices;

// Static class, treating all mediawiki hooks handled by the Push extension.

final class PushHooks {

  // Adds a link to Admin Links page.
  public static function addToAdminLinks( $admin_links_tree ) {
    $ioSection = $admin_links_tree->getSection( wfMessage( 'adminlinks_importexport' )->text() );
    $mainRow = $ioSection->getRow( 'main' );
    $mainRow->addItem( ALItem::newFromSpecialPage( 'Push' ) );
    return true;
  }


//
public static function onSkinTemplateNavigationUniversal( SkinTemplate $sktemplate, array &$links ) {
  $title = $sktemplate->getTitle();
  $user = $sktemplate->getUser();
    // TODO get rid of eg prefix
  $config = new GlobalVarConfig( 'egPush' );
  $pushTargets = $config->get( 'Targets' );
  $pushShowTab = $config->get( 'ShowTab' );
  $request = $sktemplate->getRequest();
  $pm = MediaWikiServices::getInstance()->getPermissionManager();
  if ( $title && $title->getNamespace() !== NS_SPECIAL && $title->exists() && $pm->userHasRight( $user, 'push' ) && count( $pushTargets ) > 0 ) 
    {$location = $pushShowTab ? 'views' : 'actions';
     $links[$location]['push'] = ['title' => 'Push page to other wikis',  'text' => wfMessage( 'push-tab-text' )->text(), 'class' => $request->getVal( 'action' ) == 'push' ? 'selected' : '', 'href' => $title->getLocalURL( 'action=push' )];
    }
  }



public static function onGetPreferences( $user, &$preferences ) {       

/*
   $preferences['mypref'] = ['type' => 'toggle', 'label' => 'tog-mypref', 'section' => 'push-preference',];  // a system message
   		// A set of radio buttons. Notice that in the 'options' array, the keys are the text (not system messages), and the values are the HTML values.  They keys/values might be the opposite of what you expect. PHP's array_flip()  can be helpful here.
  
  $preferences['mypref2'] = ['type' => 'radio', 'label' => 'tog-mypref2', // a system message
   		  'section' => 'push-preference',
   			// Array of options. Key = text to display. Value = HTML <option> value.
   			'options' => ['Pick me please' => 'choice1', 'No, pick me!'   => 'choice2'],
   			'default' => 'choice1',  // A 'default' key is required!
   			'help-message' => 'tog-help-mypref2', // a system message (optional)
   		];
       
   $preferences['mypref3'] = ['section' => 'push-preference', 'type' => 'select',
      'label' => 'Left Position of Category Window',
      'options' => [
          'Option 0' => 0, // depends on how you see it but keys and values are kind of mixed here
          'Option 1' => 1, // "Option 1" is the displayed content, "1" is the value
          'Option 2' => 'option2id', // HTML Result = <option value="option2id">Option 2</option>
      ],
      "default" => 0
    ];
  
   $preferences['mypref4'] = ['section' => 'push-preference', 'type' => 'submit', 'label' => 'tog-mypref5', 'buttonlabel' => 'Label' ];
       
       
*/       
       
  $preferences['Target1'] = ['section' => 'push-preference/first', 'type' => 'text',    'label' => 'Target Wiki Name (arbitrary name, only used for identification)'   ]; 
  $preferences['Url1']    = ['section' => 'push-preference/first', 'type' => 'text',    'label' => 'Target Wiki URL (URL to access wiki, for example: https://www.example.org/)'   ];       
  $preferences['User1']   = ['section' => 'push-preference/first', 'type' => 'text',    'label' => 'Username at this Wiki' ];        
  $preferences['Pass1']   = ['section' => 'push-preference/first', 'type' => 'text',    'label' => 'Password at this Wiki' ];        
       
  $preferences['Target2'] = ['section' => 'push-preference/second', 'type' => 'text',    'label' => 'Target Wiki Name (arbitrary name, only used for identification)'   ];
  $preferences['Url2']    = ['section' => 'push-preference/second', 'type' => 'text',    'label' => 'Target Wiki URL (URL to access wiki, for example: https://www.example.org/)'   ];        
  $preferences['User2']   = ['section' => 'push-preference/second', 'type' => 'text',    'label' => 'Username at this Wiki' ];        
  $preferences['Pass2']   = ['section' => 'push-preference/second', 'type' => 'text',    'label' => 'Password at this Wiki' ];        

  $preferences['Target3'] = ['section' => 'push-preference/third', 'type' => 'text',    'label' => 'Target Wiki Name (arbitrary name, only used for identification)'   ]; 
  $preferences['Url3']    = ['section' => 'push-preference/third', 'type' => 'text',    'label' => 'Target Wiki URL (URL to access wiki, for example: https://www.example.org/)'   ];       
  $preferences['User3']   = ['section' => 'push-preference/third', 'type' => 'text',    'label' => 'Username at this Wiki' ];        
  $preferences['Pass3']   = ['section' => 'push-preference/third', 'type' => 'text',    'label' => 'Password at this Wiki' ];        

  $preferences['Target4'] = ['section' => 'push-preference/fourth', 'type' => 'text',    'label' => 'Target Wiki Name (arbitrary name, only used for identification)'   ];
  $preferences['Url4']    = ['section' => 'push-preference/fourth', 'type' => 'text',    'label' => 'Target Wiki URL (URL to access wiki, for example: https://www.example.org/)'   ];        
  $preferences['User4']   = ['section' => 'push-preference/fourth', 'type' => 'text',    'label' => 'Username at this Wiki' ];        
  $preferences['Pass4']   = ['section' => 'push-preference/fourth', 'type' => 'text',    'label' => 'Password at this Wiki' ];        

      
}  


}
