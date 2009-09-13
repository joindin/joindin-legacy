<?php
/**
 * Abstract Class CommentModel
 * @package Core
 * @subpackage Models
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** Akismet */
require_once BASEPATH . 'application/libraries/Akismet.php';
/** Defensio */
require_once BASEPATH . 'application/libraries/Defensio.php';
/** Spam */
require_once BASEPATH . 'application/libraries/SpamBlacklist.php';
        
/**
 * Base model for comment models. This integrates several spam checking 
 * libraries into the model to check if comments are spam.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
abstract class CommentModel extends DomainModel {

    /**
     * Whether this comment is marked as spam or not.
     * @var boolean
     */
    protected $_spam = null;

    /** **/
    
    /**
     * Returns if the comment is marked as spam.
     * @return boolean
     */
    public function isSpam()
    {
        return $this->_spam;
    }
    
    /**
     * @see DomainModel::postValidate
     */
    protected function postValidate($success)
    {
        //return $this->_checkForSpam();
    }
    
    /**
     * Checks a new comments for spam.
     * @return boolean
     */
    protected function _checkForSpam()
    {
        if('' !== $this->getIdentifier() || null !== $this->_spam) {
            return false;
        }
        
        // Check Akismet
        $akismet = new Akismet();
		$akismetSpam = $akismet->check(
		    '/1.1/comment-check',
		    array (
		        'comment_type' => 'comment',
			    'comment_content' => $this->_get('comment')
		    )
		);
        
        // Check Defensio
        $defensio = new Defensio();
        $defensioSpam = $defensio->check(
            $this->_get('author_name'),
            $this->_get('comment')
        );
        
        // Check the spam blacklist
        $blacklist = new SpamBlacklist();
        $blacklistSpam = $blacklist->check(
            'regex',
            $this->_get('comment')
        );
        
        if(((boolean) $akismetSpam) || ((boolean) $defensioSpam) || ((boolean) $blacklistSpam)) {
            $this->_spam = true;
        }
        else {
            $this->_spam = false;
        }
        
        return $this->_spam;
    }
    
}
