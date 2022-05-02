<?php declare(strict_types=1);
  require_once(__DIR__ . '/../../platform.php');
  require_once(__DIR__ . '/../../utils.php');

function createSync($context, $command) {
  if (isset($context["user"])) {
    $conn  = getDbConn();
    $query = "INSERT INTO sync (internal_type, internal_id, remote_id, remote_system) VALUES (:internal_type, :internal_id, :remote_id, :remote_system);";
    $conn->executeStatement($query, [
      "internal_type" => $command[0],
      "internal_id" => intval($command[1]),
      "remote_id" => intval($command[2]),
      "remote_system" => $command[3]
    ]);
    return [ strval($conn->lastInsertId()) ];
  } else {
    return ["User not found or wrong password"];
  }
}