<?php
/**
 * Parses and verifies the doc comments for classes. Overrides the PEAR standards 
 * implementation to relax PEAR requirements that aren't required for Joind.in
 *
 * @category Doc
 * @package  JoindIn_CodeSniffer
 * @license  BSD see doc/LICENSE
 */

if (class_exists('PEAR_Sniffs_Commenting_ClassCommentSniff', true) === false) {
    $error = 'Class PEAR_Sniffs_Commenting_ClassCommentSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Parses and verifies the doc comments for classes.
 *
 * @category Doc
 * @package  JoindIn_CodeSniffer
 * @license  BSD see doc/LICENSE
 */
class JoindIn_Sniffs_Commenting_ClassCommentSniff 
    extends PEAR_Sniffs_Commenting_ClassCommentSniff
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
        // Relaxations for joind.in
        $this->tags['author']['required'] = false;
        $this->tags['link']['required'] = false;
        $this->tags['version']['required'] = false;
        
        return parent::process($phpcsFile, $stackPtr);
    }

}

?>
