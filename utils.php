<?php

function printOpeningBalance($params) {
  echo($params["date"] . "  Opening balance\n");
  echo("  (" . $params["account1"] . ")  " . $params["balance"] . "\n\n");
}


// date - string
// comment - string
// account1 - string
// account2 - string
// amount - float
// balanceAfter? - float
function printTransaction($params) {
  echo($params["date"] . "  " . $params["comment"] . "\n");
  echo("  " . $params["account1"] . "  " . $params["amount"] . (isset($params["balanceAfter"]) ? $params["balanceAfter"] : "") . "\n");
  echo("  " . $params["account2"] . "\n\n");
}

function readJournal($filename, $openingBalance = true) {
  $lines = explode("\n", file_get_contents($filename));
  $line1Parts = explode("  ", trim($lines[1]));
  $i = 0;
  $ret = [];
  if ($openingBalance) {
      $ret = [
        "openingBalanceDate" => explode(" ", $lines[0])[0],
        "accountName" => substr($line1Parts[0], 1, strlen($line1Parts[0]) -2),
        "openingBalance" => floatval($line1Parts[1]),
        "entries" => [],
      ];
      $i=3;
  }
  for (; $i < count($lines) - 2;) {
    $headLine = trim($lines[$i]);
    $lineOneParts = explode("  ", trim($lines[$i + 1]));
    $lineTwo = trim($lines[$i + 2]);
    $splitComment = strpos($headLine, " ");
    array_push($ret["entries"], [
      "date" => substr($headLine, 0, $splitComment),
      "comment" => trim(substr($headLine, $splitComment)),
      "account1" => $lineOneParts[0],
      "account2" => $lineTwo,
      "amount" => floatval($lineOneParts[1]),
      "balanceAfter" => floatval(substr($lineOneParts[2], 1)),
    ]);
    $i += 3;
    while ($i < count($lines) && strlen($lines[$i]) == 0) {
      $i++;
    }
  }
  return $ret;
}

function readSuppliers($filename) {
  return json_decode(file_get_contents($filename));
}
