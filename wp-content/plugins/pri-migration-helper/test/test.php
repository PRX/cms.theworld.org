<?php

/**
 * Testing JSON Diff
 *
 * @return void
 */
function test_json_diff() {

	$json1 = wp_json_file_decode( PMH_TEST_DIR . '/json1.json' );
	$json2 = wp_json_file_decode( PMH_TEST_DIR . '/json2.json' );

	$diff = pmh_get_json_diff( $json1, $json2 );

	/* Debug
	 */
	echo "<pre>";
	var_dump( $diff );
	echo "</pre>";
	exit;
}
// add_action( 'admin_init', 'test_json_diff' );

/**
 * Get JSON Diff
 *
 * @param stdClass $json1 Json decoded.
 * @param stdClass $json2 Json decoded.
 * @return Swaggest\JsonDiff\JsonPatch
 */
function pmh_get_json_diff( $json1, $json2 ) {

	require_once PMH_DIR . 'vendor/autoload.php';
	require_once PMH_DIR . 'vendor/swaggest/json-diff/src/JsonDiff.php';

	$diff = new Swaggest\JsonDiff\JsonDiff( $json1, $json2 );

	return $diff->getPatch();
}


/*

pmh_get_json_diff( $json1, $json2 )

object(Swaggest\JsonDiff\JsonPatch)#2510 (2) {
  ["flags":"Swaggest\JsonDiff\JsonPatch":private]=>
  int(0)
  ["operations":"Swaggest\JsonDiff\JsonPatch":private]=>
  array(6) {
    [0]=>
    object(Swaggest\JsonDiff\JsonPatch\Test)#2512 (3) {
      ["value"]=>
      int(5)
      ["op"]=>
      string(4) "test"
      ["path"]=>
      string(7) "/key1/0"
    }
    [1]=>
    object(Swaggest\JsonDiff\JsonPatch\Replace)#2513 (3) {
      ["value"]=>
      int(4)
      ["op"]=>
      string(7) "replace"
      ["path"]=>
      string(7) "/key1/0"
    }
    [2]=>
    object(Swaggest\JsonDiff\JsonPatch\Test)#2517 (3) {
      ["value"]=>
      string(1) "a"
      ["op"]=>
      string(4) "test"
      ["path"]=>
      string(10) "/key3/sub1"
    }
    [3]=>
    object(Swaggest\JsonDiff\JsonPatch\Replace)#2518 (3) {
      ["value"]=>
      string(2) "aa"
      ["op"]=>
      string(7) "replace"
      ["path"]=>
      string(10) "/key3/sub1"
    }
    [4]=>
    object(Swaggest\JsonDiff\JsonPatch\Test)#2523 (3) {
      ["value"]=>
      int(1)
      ["op"]=>
      string(4) "test"
      ["path"]=>
      string(16) "/key4/0/subs/0/s"
    }
    [5]=>
    object(Swaggest\JsonDiff\JsonPatch\Replace)#2524 (3) {
      ["value"]=>
      int(12)
      ["op"]=>
      string(7) "replace"
      ["path"]=>
      string(16) "/key4/0/subs/0/s"
    }
  }
}
*/
