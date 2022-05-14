<?php

namespace MediaWiki\Extensions\DumpsOnDemand\Backend;

use Config;
use MediaWiki\Extensions\DumpsOnDemand\Export\OutputSinkFactory;
use WikiMap;
use function file_exists;
use function filemtime;

class LocalFileBackend extends FileBackend {

  private $uploadDirectory;  // value of  $wgUploadDirectory.
  private $uploadPath; //  value of $wgUploadPath.

  /**
   * @param OutputSinkFactory $outputSinkFactory
   * @param Config $config The config from the MainConfig service
   */
  public function __construct( OutputSinkFactory $outputSinkFactory, Config $config ) {
    parent::__construct( $outputSinkFactory );
    $this->uploadDirectory = $config->get( 'UploadDirectory' );
    $this->uploadPath = $config->get( 'UploadPath' );
  }

  /**
   * Determine the file timestamp of the given file.
   *
   * @param string $file
   * @return false|int Unix timestamp or false if the file does not exist
   */
  private function getFileTimestamp( string $file ) {return file_exists( $file ) ? filemtime( $file ) : false;}

  /**
   * Create a file name for given kind of dump file.
   *
   * @param string $kind
   * @return string
   */
  private function createFileName( string $kind ): string {
    $file = WikiMap::getCurrentWikiId() . "_$kind.xml";

    if ( $this->outputSinkFactory->getExtension() !== '' ) {
      $file .= '.' . $this->outputSinkFactory->getExtension();
    }

    return $file;
  }

  public function getAllRevisionsFileTimestamp() {return $this->getFileTimestamp( $this->getAllRevisionsFilePath() );}
  public function getAllRevisionsFileUrl(): string {return $this->uploadPath . '/' . $this->createFileName( 'all_revisions' );}
  public function getAllRevisionsFilePath(): string {return $this->uploadDirectory . '/' . $this->createFileName( 'all_revisions' );}
  public function getCurrentRevisionsFileTimestamp() {return $this->getFileTimestamp( $this->getCurrentRevisionsFilePath() );}
  public function getCurrentRevisionsFileUrl(): string {return $this->uploadPath . '/' . $this->createFileName( 'current_revisions' );}
  public function getCurrentRevisionsFilePath(): string {return $this->uploadDirectory . '/' . $this->createFileName( 'current_revisions' );}
}
