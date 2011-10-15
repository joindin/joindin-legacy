#!/bin/env php
<?php
/**
 * @file
 * Initialise the database, data-structure, and seeds initial content.
 * Your shell login must be able to access the database without a password.
 * Patch as necessary!
 */

// Path to the database directory.
$path = dirname(__FILE__) . '/doc/db';
// Initialisation script.
$init = "{$path}/init_db.sql";
// Array of patches to apply (in order).
$patches = getSortedPatches($path);


# Nuke DB
ex("mysql -e 'DROP DATABASE IF EXISTS joindin; CREATE DATABASE joindin;'");

# Initialise and patch DB
ex("mysql joindin < $init");
foreach ($patches as $patch) {
  ex("mysql joindin < $patch");
}

# Seed the DB with content.
ex("mysql joindin < {$path}/init_data.sql");
ex("mysql joindin < {$path}/seed.sql");


/**
 * Call exec (and echo the call).
 */
function ex($string) {
  echo("{$string}\n");
  exec("$string");
}

/**
 * Get a list of patches to apply (in order).
 */
function getSortedPatches($path) {
  $patches = glob("{$path}/patch*");
  usort($patches, 'sortpatches');
  return $patches;
}


/**
 * usort callback function to sort patches in patch order.
 */
function sortpatches($a, $b) {
  $a = basename($a);
  $b = basename($b);
  $pattern = '/patch([0-9]+)\.sql/';
  if (preg_match($pattern, $a, $match_a) && preg_match($pattern, $b, $match_b)) {
    // $match_x[1] is the patch number.
    $a_val = $match_a[1];
    $b_val = $match_b[1];
    return ($a_val < $b_val) ? -1 : 1;
  }
  return 0;
}
