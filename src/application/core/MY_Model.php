<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MY_Model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Models
 * @author    Rob Larter <rob@revolveweb.com>
 * @copyright 2009 - 2011 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

class MY_Model extends CI_Model {

    
    public function __construct()
    {
        parent::__construct();
    }
    function MY_Model()
    {
        parent::CI_Model();
    }
    


    /**
     * __get
     *
     * Allows models to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @access private
     */
    function &__get($key)
    {
	   $CI =& get_instance();
	   return $CI->$key;
    }

    /**
     * A method to facilitate easy bulk inserts into a given table.
     * @param string $table_name
     * @param array $column_names A basic array containing the column names
     *  of the data we'll be inserting
     * @param array $rows A two dimensional array of rows to insert into the
     *  database.
     * @param bool $escape Whether or not to escape data
     *  that will be inserted. Default = true.
     * @author Kenny Katzgrau <katzgrau@gmail.com>
     */
    function insert_rows($table_name, $column_names, $rows, $escape = true)
    {
        /* Build a list of column names */
        $columns    = array_walk($column_names, array($this, 'prepare_column_name') );
        $columns    = implode(',', $column_names);

        /* Escape each value of the array for insertion into the SQL string */
        if( $escape ) array_walk_recursive( $rows, array( $this, 'escape_value' ) );

        /* Collapse each rows of values into a single string */
        $length = count($rows);
        for($i = 0; $i < $length; $i++) $rows[$i] = implode(',', $rows[$i]);

        /* Collapse all the rows into something that looks like
         *  (r1_val_1, r1_val_2, ..., r1_val_n),
         *  (r2_val_1, r2_val_2, ..., r2_val_n),
         *  ...
         *  (rx_val_1, rx_val_2, ..., rx_val_n)
         * Stored in $values
         */
        $values = "(" . implode( '),(', $rows ) . ")";

        $sql = "INSERT INTO $table_name ( $columns ) VALUES $values";

        return $this->db->simple_query($sql);
    }

    function escape_value(& $value)
    {
        if( is_string($value) )
        {
            $value = "'" . mysql_real_escape_string($value) . "'";
        }
    }

    function prepare_column_name(& $name)
    {
        $name = "`$name`";
    }
}
