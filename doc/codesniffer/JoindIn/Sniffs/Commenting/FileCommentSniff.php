<?php
/**
 * Parses and verifies the doc comments for files. Overrides the PEAR standards 
 * implementation to:
 *     1. Remove the requirement for @link in a file level comment
 *     2. Remove the requirement to specificy PHP version 4 or 5
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
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>A PHP version is specified.</li>
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

class JoindIn_Sniffs_Commenting_FileCommentSniff 
    extends PEAR_Sniffs_Commenting_FileCommentSniff
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
        // Changes for joind.in
        $this->tags['author']['required'] = false;
        $this->tags['link']['required'] = false;
        $this->tags['version']['required'] = false;
        
        return parent::process($phpcsFile, $stackPtr);
    }

    /**
     * Check that the PHP version is specified.
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
