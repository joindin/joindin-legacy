<?php
/**
 * Parses and verifies the doc comments for classes. Overrides the PEAR standards 
 * imeplementation to remove the requirement for @link in a class comment.
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
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @category  Doc
 * @package   JoindIn_CodeSniffer
 * @author    Rob Allen <rob@akrabat.com>
 * @copyright 2011 Rob Allen
 * @license   BSD see doc/LICENSE
 */
class JoindIn_Sniffs_Commenting_ClassCommentSniff 
    extends PEAR_Sniffs_Commenting_ClassCommentSniff
{

    /**
     * Processes this test, when one of its tokens is encountered.
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
