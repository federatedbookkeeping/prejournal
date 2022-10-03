<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');
require_once(__DIR__ . '/../api/herokuQuickbooks.php');

// php src/cli-single.php forward-latest-invoice heroku quickbooks

function forwardLatestInvoice($context, $command) {
    if (isset($context["user"])) {
        $remote_one = $command[1];
        $remote_second = $command[2];
        //var_dump($remote_one);

        $remote_system = $remote_one . ":". $remote_second;
        $type = 'invoice';

        $quickBill = createQuickBooksBill();
    
        if (!is_array($quickBill) && str_starts_with($quickBill, 'Token expired')) {
           return ["Token expired you need to refresh token"];
        }


        $timestamp = strtotime($quickBill["Bill"]["TxnDate"]) / 3600;
       
        $invoice_hours = (strtotime($quickBill["Bill"]["DueDate"]) - strtotime($quickBill["Bill"]["TxnDate"])) / 3600;

        $movementId = intval(createMovement($context, [
            "create-movement",
            $context["user"]["id"],
            $type,
            strval(getComponentId($quickBill["Bill"]["Line"][0]["AccountBasedExpenseLineDetail"]["AccountRef"]["name"])),
            strval(getComponentId($quickBill["Bill"]["domain"])),
            $timestamp,
            $invoice_hours
        ])[0]);

        /*$statementId = intval(createSync($context, [
            "movement",
            $movementId,
            $remote_system
        ])[0]);*/
        
        return ["Movement id ".$movementId];
    } else {
        return ["User not found or wrong password"];
    }
}