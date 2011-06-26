<?php
/**
 * Parses and verifies the doc comments for files. Overrides the PEAR standards 
 * implementation to relax PEAR requirements that aren't required for Joind.in
 *
 * @category Doc
 * @package  JoindIn_CodeSniffer
 * @license  BSD see doc/LICENSE
 */

if (class_exists('PEAR_Sniffs_Commenting_FileCommentSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception(
        'Class PEAR_Sniffs_Commenting_FileCommentSniff not found'
    );
}

/**
 * Parses and verifies the doc comments for files.
 *
 * @category Doc
 * @package  JoindIn_CodeSniffer
 * @license  BSD see doc/LICENSE
 */

class JoindIn_Sniffs_Commenting_FileCommentSniff 
    extends PEAR_Sniffs_Commenting_FileCommentSniff
{
                
    /**
     * Processes this test, when one of its tokens is encountered. Overrides to relax some
     * requirements.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // Changes for joind.in
        $this->tags['author']['required'] = false;
        $this->tags['link']['required'] = false;
        $this->tags['version']['required'] = false;
        
        return parent::process($phpcsFile, $stackPtr);
    }

    /**
     * Override to stop the check that the PHP version is specified.
     *
     * @param int    $commentStart Position in the stack where the comment started.
     * @param int    $commentEnd   Position in the stack where the comment ended.
     * @param string $commentText  The text of the function comment.
     *
     * @return void
     */
    protected function processPHPVersion($commentStart, $commentEnd, $commentText)
    {
        // do nothing as we don't need a PHP version tag for joind.in.
    }
    
}

?>
