<?php

namespace MediaWiki\Extensions\DumpsOnDemand\Backend;

use DumpOutput;
use MediaWiki\Extensions\DumpsOnDemand\Export\OutputSinkFactory;

/**
 * Represents a file backend with which DumpsOnDemand can communicate.
 */
abstract class FileBackend {
  protected $outputSinkFactory;

  public function __construct( OutputSinkFactory $outputSinkFactory ) {$this->outputSinkFactory = $outputSinkFactory;}

  abstract public function getAllRevisionsFileTimestamp();          // Returns the timestamp of the dump with all revisions; int|false Unix timestamp or false if the file does not exist.
  abstract public function getAllRevisionsFileUrl(): string;        // Returns the url of the dump with all revisions.
  abstract public function getAllRevisionsFilePath(): string;       // Returns the writable file path of the dump with all revisions, to be passed to the output sink created by the OutputSinkFactory.
  abstract public function getCurrentRevisionsFileTimestamp();      // Returns the timestamp of the dump with the current revisions. int|false Unix timestamp or false if the file does not exist.
  abstract public function getCurrentRevisionsFileUrl(): string;    //  Returns the url of the dump with the current revisions.
  abstract public function getCurrentRevisionsFilePath(): string;   // Returns the writable file path of the dump with the current revisions, to be passed to the output sink created by @see getOutputSink

  public function getOutputSink( string $file ): DumpOutput {
    return $this->outputSinkFactory->makeNewSinkForFile( $file );
  }  //  * Creates an output sink for the given file.

  /**
   * Returns a message that describes the used file type.
   * When no compression is used, an empty string will be returned to indicate that no message
   * should be shown.
   *
   * @return string
   */
  public function getFileTypeDescriptionMessage(): string {
    $extension = $this->outputSinkFactory->getExtension();

    if ( $extension === '' ) {return '';}

    // Used messages:
    // - dumpsondemand-filetype-gz
    // - dumpsondemand-filetype-bz2
    return "dumpsondemand-filetype-$extension";
  }
}
