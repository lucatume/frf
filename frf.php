<?php
if ( $argc !== 3 ) {
	echo "\nUsage: php frf.php commit_1_file_prefix commit_2_file_prefix";
	echo "\n       php frf.php master_ some_branch_";
	echo "\n";
	exit( 1 );
}

list( $commit_1_file_prefix, $commit_2_file_prefix ) = array_slice( $argv, 1 );
$functions_1_file_path   = $commit_1_file_prefix . 'functions.txt';
$classes_1_file_path     = $commit_1_file_prefix . 'classes.txt';
$class_methods_1_file_path = $commit_1_file_prefix . 'class_methods.txt';

$commit_1_functions = file( $functions_1_file_path, FILE_IGNORE_NEW_LINES );
$commit_1_classes = file( $classes_1_file_path, FILE_IGNORE_NEW_LINES );
$commit_1_class_methods = file( $class_methods_1_file_path, FILE_IGNORE_NEW_LINES );

$functions_2_file_path   = $commit_2_file_prefix . 'functions.txt';
$classes_2_file_path     = $commit_2_file_prefix . 'classes.txt';
$class_methods_2_file_path = $commit_2_file_prefix . 'class_methods.txt';

$commit_2_functions = file( $functions_2_file_path, FILE_IGNORE_NEW_LINES );
$commit_2_classes = file( $classes_2_file_path, FILE_IGNORE_NEW_LINES );
$commit_2_class_methods = file( $class_methods_2_file_path, FILE_IGNORE_NEW_LINES );

$removed_functions     = array_diff( $commit_1_functions, $commit_2_functions );
$removed_classes       = array_diff( $commit_1_classes, $commit_2_classes );
$removed_class_methods = array_diff( $commit_1_class_methods, $commit_2_class_methods );

echo "Removed functions\n==================\n";
foreach($removed_functions as $removed_function){
	echo "$removed_function\n";
}
echo "\nRemoved classes\n==================\n";
foreach($removed_classes as $removed_class){
	echo "$removed_class\n";
}
echo "\nRemoved class methods\n==================\n";
foreach($removed_class_methods as $removed_class_method){
	echo "$removed_class_method\n";
}

echo "\n";
