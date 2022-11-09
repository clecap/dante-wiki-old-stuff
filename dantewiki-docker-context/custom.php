#
# Stuff to be added to LocalSettings.php in any case
#


$wgEmergencyContact = "apache@localhost";
$wgPasswordSender = "apache@localhost";



$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "";
$wgRightsText = "";
$wgRightsIcon = "";




$wgEnableUploads = true;



$wgAppleTouchIcon = "/apple-touch-icon.png";

#
# for the mobile frontend and its skins
#
wfLoadSkin( 'MinervaNeue' );
wfLoadExtension( 'Cite' );
wfLoadExtension( 'MobileFrontend' );
$wgDefaultSkin = 'minerva';