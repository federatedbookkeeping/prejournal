<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/run-command.php');


final class ImportTimesheetCsvTest extends TestCase
{
    public function testParseTimeCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123');
        $fixture = __DIR__ . "/fixtures/time-CSV.csv";
        $result = runCommand(getContext(), ["import-hours", "time-CSV", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alex.malikov94@gmail.com'
            ],
            [
                'id' => 2,
                'name' => 'any'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-18 09:39:19',
                'amount' => 5.0            ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 1,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                            ]
        ], getAllStatements());
    }

    public function testParseTimeBroCsv(): void
    {
        setTestDb();
        $aliceId = intval(register([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123');
        $fixture = __DIR__ . "/fixtures/timeBro-CSV.csv";
        $result = runCommand(getContext(), ["import-hours", "timeBro-CSV", $fixture,  "2022-03-31 12:00:00" ]);
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alex.malikov94@gmail.com'
            ],
            [
                'id' => 2,
                'name' => 'test'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-18 09:39:19',
                'amount' => 5.0            ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 1,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                            ]
        ], getAllStatements());
    }

    public function testParseTimeDoctorCsv(): void
    {
        setTestDb();
        $aliceId = intval(register([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123');
        $fixture = __DIR__ . "/fixtures/timeDoctor-CSV.csv";
        $result = runCommand(getContext(), ["import-hours", "timeDoctor-CSV", $fixture,  "2022-03-31 12:00:00" ]);
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alex.malikov94@gmail.com'
            ],
            [
                'id' => 2,
                'name' => ' test'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-18 00:00:00',
                'amount' => '0'            ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 1,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                            ]
        ], getAllStatements());
    }
    public function testParseTimetipJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123');
        $fixture = __DIR__ . "/fixtures/timetip-JSON.json";
        $result = runCommand(getContext(), ["import-hours", "timetip-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alex.malikov94@gmail.com'
            ],
            [
                'id' => 2,
                'name' => 'coffee break'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-27 19:00:00',
                'amount' => '104580'           ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 1,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                            ]
        ], getAllStatements());
    }

    public function testParseTimeTrackerXml(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123');
        $fixture = __DIR__ . "/fixtures/timetracker-XML.xml";
        $result = runCommand(getContext(), ["import-hours", "timetracker-XML", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alex'
            ],
            [
                'id' => 2,
                'name' => 'test'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-30 00:00:00',
                'amount' => '15'           ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 1,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                            ]
        ], getAllStatements());
    }
  
public function testParseTimeDoctorCsv(): void
{
    setTestDb();
    $aliceId = intval(register([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
    setUser('alice', 'alice123');
    $fixture = __DIR__ . "/fixtures/timeDoctor-CSV.csv";
    $result = runCommand(getContext(), ["import-hours", "timeDoctor-CSV", $fixture,  "2022-03-31 12:00:00" ]);
    $this->assertEquals([
        [
            'id' => 1,
            'name' => 'alex.malikov94@gmail.com'
        ],
        [
            'id' => 2,
            'name' => ' test'
        ]
    ], getAllComponents());
    $this->assertEquals([
        [
            'id' => 1,
            'type_' => 'worked',
            'fromcomponent' => 1,
            'tocomponent' => 2,
            'timestamp_' => '2022-04-06 00:00:00',
            'amount' => '0'            ]
    ], getAllMovements());
    $this->assertEquals([
        [
            'id' => 1,
            'movementid' => 1,
            'userid' => 1,
            'sourcedocumentformat' => null,
            'sourcedocumentfilename' => null,
            'timestamp_' => '2022-03-31 12:00:00',
                        ]
    ], getAllStatements());
}

public function testParseShaveMyTimeCsv(): void
{
    setTestDb();
    $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
    setUser('alice', 'alice123');
    $fixture = __DIR__ . "/fixtures/saveMyTime-CSV.csv";
    $result = runCommand(getContext(), ["import-hours", "saveMyTime-CSV", $fixture,  "2022-03-31 12:00:00" ]);

    $this->assertEquals([
        [
            'id' => 1,
            'name' => 'alice'
        ],
        [
            'id' => 2,
            'name' => 'default-project'
        ]
    ], getAllComponents());
    $this->assertEquals([
        [
            'id' => 1,
            'type_' => 'worked',
            'fromcomponent' => 1,
            'tocomponent' => 2,
            'timestamp_' => '2022-03-25 14:09:38',
            'amount' => '560'        ]
    ], getAllMovements());
    $this->assertEquals([
        [
            'id' => 1,
            'movementid' => 1,
            'userid' => 1,
            'sourcedocumentformat' => null,
            'sourcedocumentfilename' => null,
            'timestamp_' => '2022-03-31 12:00:00',
                        ]
    ], getAllStatements());
}
}

// in curl commands:
// curl -d'["alice","alice123"]' http://localhost:8080/v1/register
// curl -d'["bob","bob123"]' http://localhost:8080/v1/register
// curl -d'["from component", "to component", "1.23", "2021-12-31T23:00:00.000Z", "invoice", "ponder-source-agreement-192"]' http://alice:alice123@localhost:8080/v1/enter
// curl -d'["bob", "from component"]' http://alice:alice123@localhost:8080/v1/grant
// curl http://bob:bob123@localhost:8080/v1/list-new
