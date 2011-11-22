#!/usr/bin/php
<?php

// CodeError.js command line formatter
//
// Written by Patrick Allaert <patrickallaert@php.net>
// Copyright © 2011 Libereco Technologies
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.


// Usage: CodeErrorFormatter.php /path/to/CodeError.js

echo "CodeErrorFormatter 1.0.0 by Patrick Allaert.\n\n";

if ( !isset( $argv[1] ) || !is_file( $argv[1] ) ) {
    die( "Usage:\n\tCodeErrorFormatter.php /path/to/CodeError.js\n\n" );
}

$data = json_decode(file_get_contents($argv[1]), true);

foreach ( $data[1] as $key => $entries ) {
    echo $key, "\n================================================================================\n";
    $previousFile = false;
    foreach ( $entries as $entry ) {
        list( $file, $l1, $c1, $l2, $c2 ) = $entry['c1'];

        // Printing filename
        if ( $previousFile !== $file )
            echo "\n", $file, "\n";

        // Printing line information
        echo "\t", $l1, ":", $c1;
        if ( $l1 !== $l2 || $c1 !== $c2 )
            echo " -> $l2:$c2";

        // Printing details
        echo "\tdetails: ", trim( $entry['d'] ), "\n";

        $previousFile = $file;
    }
    echo "\n";
}
