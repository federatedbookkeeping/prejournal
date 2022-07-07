<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../../platform.php');
  require_once(__DIR__ . '/../../utils.php');

function createStatement($context, $command)
{
    if (isset($context["user"])) {
        $conn  = getDbConn();
        $query = "INSERT INTO statements (userId, movementId, timestamp_, description) VALUES (:userId, :movementId, :timestamp_, :description);";
        $ret = $conn->executeStatement($query, [
      "userId" => $context["user"]["id"],
      "movementId" => intval($command[1]),
      "timestamp_" => timestampToDateTime(intval($command[2])),
      "description" => $command[3] ?? null
    ]);
        return [ strval($conn->lastInsertId()) ];
    } else {
        return ["User not found or wrong password"];
    }
}
