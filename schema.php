<?php

# Prejournal internal database schema, version 1

function getTables() {
    return [
"drop if exists `users`;",
"create table users (
  id serial primary key,
  username varchar unique,
  passwordhash varchar
);",

"drop if exists `components`;",
"create table components (
  id serial primary key,
  name varchar unique
);",

"drop if exists `movements`;",
"create table movements (
  id serial primary key,
  type_ varchar, /* 'invoice', 'payment' */
  fromComponent int,
  toComponent int,
  timestamp_ timestamp,
  amount float
);",

"drop if exists `statements`;",
"create table statements (
  id serial primary key,
  movementId int,
  userId int,
  sourceDocumentFormat varchar, /* could be an invoice, bank statement csv file, API call etc */
  sourceDocumentFilename varchar, /* TODO: work out how to store files when on Heroku */
  timestamp_ timestamp
)",

"drop if exists `componentGrants`;",
"create table componentGrants (
  id serial primary key,
  fromUser int,
  toUser int,
  componentId int
)"
  ];
}


if (isset($_SERVER["GEN_SQL"])) {
  echo "-- Created from schema.php, DO NOT EDIT DIRECTLY!\n";
  echo "-- To regenerate: GEN_SQL=1 php schema.php > schemal.xml\n\n";
  $tables = getTables();
  for ($i = 0; $i < count($tables); $i++) {
      echo $tables[$i] . "\n\n";
  }
}