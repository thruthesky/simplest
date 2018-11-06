<?php

include_once 'test-config.php';



$re = rpc(['run' => 'test.simplest.version']);
$re['version'] ? testOk($re['version']) : testBad('version failed.');


$re = db()->table('tests')->record(['name' => 'jaeho'])->insert();
$re ? testOk("insert ok. insert id: $re") : testBad('insert failed');


sleep(1);

$idx = db()->table('tests')->insert(['name' => 'jiyeon']);

$re = db()->table('tests')->record(['name' => 'eunsu', 'address' => 'DongHae City'])->where("idx=$idx")->update();
$re ? testOk("update ok. idx: $idx") : testBad('update failed');


$re = db()->table('tests')->where(" idx < 10 AND name='jaeho' ")->delete();
$re ? testOk("delete ok. re: $re") : testBad('delete failed');


$rows = db()->rows("SELECT * FROM " . _table('tests') . " WHERE name='jaeho'");
count($rows) > 0 ? testOk("rows() ok. count: " . count($rows)) : testBad('rows() failed');


$row = db()->row("SELECT * FROM " . _table('tests') . " WHERE name='jaeho' LIMIT 1");
count($row) ? testOk("row() ok. idx: $row[idx]") : testBad('row() failed');


$re = db()->result("SEL * FROM");
$re === false ? testOk("Expect error because Query has error") : testBad("Expect false since query has error. re:" . $re );

$re = db()->result("SELECT * FROM " . _table('tests') . " WHERE name='jaeho'");
$re ? testOk("Got idx: $idx") : testBad("Failed on result()");
