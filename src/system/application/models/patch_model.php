<?php

class Patch_model extends Model {

    function Patch_model() {
        parent::Model();
    }

    /**
	 * check_patches
	 * 
	 * Checks to see if the database is fully patched. Should only be called when in dev mode
	 * 
	 * @return $patch integer the number of patches missing from the database
	 */
    function check_patches() {
    	$query = $this->db->query("SELECT `patch_number` FROM patch_history ORDER BY patch_number DESC");

        $result = ($query->num_rows() > 0) ? $query->row() : 0;

        $patch_folder = opendir('../doc/db');

        //As we are reading from a directory directly, we need to remove . and .. as well as not counting non-patch files
		$return = -8;

		while ($tmp = readdir($patch_folder)) {
			$return++;
		}

		return $return - (int)$result->patch_number;
	}
}
