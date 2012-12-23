<?php
/**
 * Language model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Language model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Lang_model extends Model
{
    /**
     * Retrieves the languages from the database
     *
     * @return mixed
     */
    public function getLangs()
    {
        $this->db->from('lang');
        $this->db->order_by('lang_name');
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Check to see if two-letter abbreviation is valid. Returns the
     * id of the language if it is valid, false otherwise
     *
     * @param string $lang Language abbreviation to check
     *
     * @return boolean|integer
     */
    public function isLang($lang)
    {
        $q   = $this->db->get_where(
            'lang',
            array('lower(lang_abbr)' => strtolower($lang))
        );
        $ret = $q->result();

        return (empty($ret)) ? false : $ret[0]->ID;
    }
}
