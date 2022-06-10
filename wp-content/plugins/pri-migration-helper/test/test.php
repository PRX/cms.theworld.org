<?php

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
