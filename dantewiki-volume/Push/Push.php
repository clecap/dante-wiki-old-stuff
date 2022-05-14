<?php

if ( function_exists( 'wfLoadExtension' ) ) {
  wfLoadExtension( 'Push' );
// Keep i18n globals so mergeMessageFileList.php doesn't break
  $wgMessagesDirs['Push'] = __DIR__ . '/i18n';
  $wgExtensionMessagesFiles['PushAliases'] = __DIR__ . '/Push.alias.php';
  wfWarn('Deprecated PHP entry point used for Push extension. Please use wfLoadExtension instead, ' . 'see https://www.mediawiki.org/wiki/Extension_registration for more details.');
return;
}

die( 'This version of the Push extension requires MediaWiki 1.29+' );
