<?php
/**
 * Admin helper
 *
 * @package WordPress
 */

/**
 * Testing JSON Diff
 *
 * @return void
 */
function test_json_diff( $json_url_1, $json_url_2 ) {

	$json1 = wp_remote_get( $json_url_1 );
	$obj1  = json_decode( wp_remote_retrieve_body( $json1 ) );

	$json2 = wp_remote_get( $json_url_2 );
	$obj2  = json_decode( wp_remote_retrieve_body( $json2 ) );

	$diff = pmh_get_json_diff(  $obj1, $obj2 );

	return $diff;
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

	return $diff;
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


