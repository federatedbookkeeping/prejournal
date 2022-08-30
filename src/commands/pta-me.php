<?php

declare(strict_types=1);
require_once(__DIR__ . '/../database.php');
function ptaMe($context)
{
    if (isset($context["user"])) {
        $conn = getDbConn();
        $consumptionRes = $conn->executeQuery('SELECT ' .
            'm.timestamp_, m.amount, m.unit, s.description ' .
            'FROM movements m INNER JOIN implications i ON i.movementId = m.id INNER JOIN statements s ON i.statementId = s.id ' .
            'WHERE i.relation=\'delivery\' AND m.tocomponent = 1');
        $movements = $consumptionRes->fetchAllAssociative();
        $ret = [];
        for ($i = 0; $i < count($movements); $i++) {
            array_push($ret, $movements[$i]["timestamp_"] . "  " . $movements[$i]["description"]);
            array_push($ret, "    expenses  " . $movements[$i]["amount"]);
            array_push($ret, "    bank");
            array_push($ret, "");
        }
        return $ret;
    } else {
        return [ "User not found or wrong password" ];
    }
}
