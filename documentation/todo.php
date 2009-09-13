<?php
/**
 * Generates a list with all @TODO items for Joind.in.
 * Only works on *nix systems.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */

$command = 'grep -R -n --include "*.php" "@todo" .';
$lines = system($command);

$regex = "/^(.*)\s+\*?(.*)$/i";

var_dump($result);
