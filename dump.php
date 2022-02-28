<?php

use PhpParser\Node;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

require __DIR__ . '/vendor/autoload.php';

if ( $argc !== 3 ) {
	echo "\nUsage: php dump.php source_codebase file_prefix";
	echo "\n       php dump.php ./plugin-one/src commit_1";
	echo "\n";
	exit( 1 );
}

class Dumping_Visitor extends NodeVisitorAbstract {
	private $functions_file;
	private $classes_file;
	private $class_methods_file;
	private $current_class;

	public function __construct( $functions_file, $classes_file, $class_methods_file ) {
		$this->functions_file     = $functions_file;
		$this->classes_file       = $classes_file;
		$this->class_methods_file = $class_methods_file;
	}

	public function enterNode( Node $node ) {
		if ( $node instanceof Function_ ) {
			$function_name = $node->namespacedName->toCodeString();
			$data          = "$function_name\n";
			fwrite( $this->functions_file, $data, strlen( $data ) );

			return;
		}

		if ( $node instanceof Node\Stmt\Class_ && $node->namespacedName !== null ) {
			$class_name = $node->namespacedName->toCodeString();
			$this->current_class = $class_name;
			$data          = "$class_name\n";
			fwrite( $this->classes_file, $data, strlen( $data ) );

			return;
		}

		if ( $node instanceof Node\Stmt\ClassMethod ) {
			$method_name = $this->current_class . '::' . $node->name->toString();
			$data          = "$method_name\n";
			fwrite( $this->class_methods_file, $data, strlen( $data ) );

			return;
		}
	}
}

function dump_list( $source_dir, $file_prefix ) {
	$functions_file_path     = $file_prefix . 'functions.txt';
	$classes_file_path       = $file_prefix . 'classes.txt';
	$class_methods_file_path = $file_prefix . 'class_methods.txt';

	if ( file_exists( $functions_file_path ) ) {
		unlink( $functions_file_path );
	}
	if ( file_exists( $classes_file_path ) ) {
		unlink( $classes_file_path );
	}
	if ( file_exists( $class_methods_file_path ) ) {
		unlink( $class_methods_file_path );
	}

	touch( $functions_file_path );
	touch( $classes_file_path );
	touch( $class_methods_file_path );

	$functions_file = fopen( $functions_file_path, 'wb' );

	if ( ! ( is_resource( $functions_file ) ) ) {
		throw new RuntimeException( 'Failed to open functions file for writing.' );
	}

	$classes_file = fopen( $classes_file_path, 'wb' );

	if ( ! ( is_resource( $classes_file ) ) ) {
		throw new RuntimeException( 'Failed to open classes file for writing.' );
	}

	$class_methods_file = fopen( $class_methods_file_path, 'wb' );

	if ( ! ( is_resource( $class_methods_file ) ) ) {
		throw new RuntimeException( 'Failed to open class_methods file for writing.' );
	}

	$matching_php_files = new RegexIterator(
		new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source_dir ) ),
		'/^.+\.php$/i',
		RegexIterator::GET_MATCH
	);

	$traverser    = new NodeTraverser();
	$nameResolver = new NameResolver;
	$traverser->addVisitor( $nameResolver );
	$traverser->addVisitor( new Dumping_Visitor( $functions_file, $classes_file, $class_methods_file ) );

	foreach ( $matching_php_files as $php_files ) {
		foreach ( $php_files as $php_file ) {
			$parser = ( new ParserFactory )->create( ParserFactory::PREFER_PHP7 );
			$ast    = $parser->parse( file_get_contents( $php_file ) );
			$traverser->traverse( $ast );
		}
	}

	fclose( $functions_file );
	fclose( $classes_file );
	fclose( $class_methods_file );
}

list( $source_codebase, $file_prefix ) = array_slice( $argv, 1 );

dump_list( $source_codebase, $file_prefix );
