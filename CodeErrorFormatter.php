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


// Usage: CodeErrorFormatter.php [--checkstyle] /path/to/CodeError.js

$messageMapping = array(
    "BadPHPIncludeFile" => "Bad include: %s",
    "PHPIncludeFileNotFound" => "Include not found: %s",
    "UnknownClass" => "Class %s is unknown",
    "UnknownBaseClass" => "Base class %s is unknown",
    "UnknownFunction" => "Unknown function %s",
    "UseEvaluation" => "Usage of eval()",
    "UseUndeclaredVariable" => "Variable %s is not declared",
    "UseUndeclaredGlobalVariable" => "Global variable %s is not declared",
    "UseUndeclaredConstant" => "Constant %s is not declared",
    "UnknownObjectMethod" => "Call to unknown method: %s",
    "InvalidMagicMethod" => "Magic method %s is invalid",
    "BadConstructorCall" => "Bad call to constructor: %s",
    "DeclaredVariableTwice" => "Variable is declared twice: %s",
    "DeclaredConstantTwice" => "Constant is declared twice: %s",
    "BadDefine" => "Bad define: %s",
    "RequiredAfterOptionalParam" => "Required parameters after optional parameters: %s",
    "RedundantParameter" => "Redundant parameter: %s",
    "TooFewArgument" => "Too few arguments in function or method call: %s",
    "TooManyArgument" => "Too many arguments in function or method call: %s",
    "BadArgumentType" => "Bad argument type: %s",
    "StatementHasNoEffect" => "Statement %s has no effect",
    "UseVoidReturn" => "Usage of void return value from %s",
    "MissingObjectContext" => "Trying to use \$this in static context",
    "MoreThanOneDefault" => "More than one default in switch statement",
    "InvalidArrayElement" => "Invalid array element: %s",
    "InvalidDerivation" => "Invalid inheritance: %s",
    "InvalidOverride" => "Invalid override: %s",
    "ReassignThis" => "Reassignment of \$this",
    "MissingAbstractMethodImpl" => "Implementation of abstract methods missing: %s",
    "BadPassByReference" => "Bad pass-by-reference: %s",
    "ConditionalClassLoading" => "Class %s is conditionally loaded",
    "GotoUndefLabel" => "GOTO to invalid label %s",
    "GotoInvalidBlock" => "GOTO to invalid block: %s",
    "AbstractProperty" => "Attribute %s is abstract",
    "UnknownTrait" => "Trait %s is unknown",
    "MethodInMultipleTraits" => "Method %s is declared in multiple traits",
    "UnknownTraitMethod" => "Trait method %s is unknown",
    "InvalidAccessModifier" => "Access modified %s is invalid",
    "CyclicDependentTraits" => "Cyclic dependency between traits: %s",
    "InvalidTraitStatement" => "Invalid trait statement: %s",
    "RedeclaredTrait" => "Trait %s is declared twice",
    "InvalidInstantiation" => "Invalid instantiation: %s",
);

$options = getopt( ":", array( "checkstyle" ) );

$checkstyleMode = isset( $options["checkstyle"] );

if ( $checkstyleMode ) {
    $fileArg = 2;
} else {
    $fileArg = 1;
    echo "CodeErrorFormatter 1.1.0 by Patrick Allaert.\n\n";
}

if ( !isset( $argv[$fileArg] ) || !is_file( $argv[$fileArg] ) ) {
    die( "Usage:\n\tCodeErrorFormatter.php [--checkstyle] /path/to/CodeError.js\n\n" );
}

if ( $checkstyleMode )
    echo "<checkstyle>\n";

$data = json_decode(file_get_contents($argv[$fileArg]), true);

foreach ( $data[1] as $key => $entries ) {
    if ( !$checkstyleMode )
        echo $key, "\n================================================================================\n";

    $previousFile = false;
    foreach ( $entries as $entry ) {
        list( $file, $l1, $c1, $l2, $c2 ) = $entry['c1'];

        // Printing filename
        if ( $previousFile !== $file )
            if ( $checkstyleMode ) {
                if ( $previousFile !== false )
                    echo "  </file>\n";
                echo '  <file name="', $file, '">', "\n";
            } else
                echo "\n", $file, "\n";

        // Printing line information
        if ( $checkstyleMode ) {
            echo '    <error line="', $l1, '" column="', $c1,'" message="';
            printf( $messageMapping[$key], trim( $entry['d'] ) );
            echo '" severity="error" source="HipHop.PHP.Analysis.', $key,'">', "\n";
        } else {
            echo "\t", $l1, ":", $c1;
            if ( $l1 !== $l2 || $c1 !== $c2 )
                echo " -> $l2:$c2";
        }

        // Printing details
        if ( !$checkstyleMode )
            echo "\tdetails: ", trim( $entry['d'] ), "\n";

        $previousFile = $file;
    }
    if ( $checkstyleMode )
        if ( $previousFile !== false )
            echo "  </file>\n";
    else
        echo "\n";
}

if ( $checkstyleMode )
    echo "</checkstyle>\n";
