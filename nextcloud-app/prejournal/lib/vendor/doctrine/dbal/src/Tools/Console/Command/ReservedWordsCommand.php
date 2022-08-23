<?php

namespace Doctrine\DBAM\Tools\Console\Command;

use Doctrine\DBAM\Connection;
use Doctrine\DBAM\Exception;
use Doctrine\DBAM\Platforms\Keywords\DB2Keywords;
use Doctrine\DBAM\Platforms\Keywords\KeywordList;
use Doctrine\DBAM\Platforms\Keywords\MariaDb102Keywords;
use Doctrine\DBAM\Platforms\Keywords\MySQL57Keywords;
use Doctrine\DBAM\Platforms\Keywords\MySQL80Keywords;
use Doctrine\DBAM\Platforms\Keywords\MySQLKeywords;
use Doctrine\DBAM\Platforms\Keywords\OracleKeywords;
use Doctrine\DBAM\Platforms\Keywords\PostgreSQL100Keywords;
use Doctrine\DBAM\Platforms\Keywords\PostgreSQL94Keywords;
use Doctrine\DBAM\Platforms\Keywords\ReservedKeywordsValidator;
use Doctrine\DBAM\Platforms\Keywords\SQLiteKeywords;
use Doctrine\DBAM\Platforms\Keywords\SQLServer2012Keywords;
use Doctrine\DBAM\Tools\Console\ConnectionProvider;
use Doctrine\Deprecations\Deprecation;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function array_keys;
use function assert;
use function count;
use function implode;
use function is_array;
use function is_string;

class ReservedWordsCommand extends Command
{
    /** @var array<string,KeywordList> */
    private $keywordLists;

    /** @var ConnectionProvider */
    private $connectionProvider;

    public function __construct(ConnectionProvider $connectionProvider)
    {
        parent::__construct();
        $this->connectionProvider = $connectionProvider;

        $this->keywordLists = [
            'db2'        => new DB2Keywords(),
            'mariadb102' => new MariaDb102Keywords(),
            'mysql'      => new MySQLKeywords(),
            'mysql57'    => new MySQL57Keywords(),
            'mysql80'    => new MySQL80Keywords(),
            'oracle'     => new OracleKeywords(),
            'pgsql'      => new PostgreSQL94Keywords(),
            'pgsql100'   => new PostgreSQL100Keywords(),
            'sqlite'     => new SQLiteKeywords(),
            'sqlserver'  => new SQLServer2012Keywords(),
        ];
    }

    /**
     * Add or replace a keyword list.
     */
    public function setKeywordList(string $name, KeywordList $keywordList): void
    {
        $this->keywordLists[$name] = $keywordList;
    }

    /**
     * If you want to add or replace a keywords list use this command.
     *
     * @param string                    $name
     * @param class-string<KeywordList> $class
     *
     * @return void
     */
    public function setKeywordListClass($name, $class)
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https://github.com/doctrine/dbal/issues/4510',
            'ReservedWordsCommand::setKeywordListClass() is deprecated,'
                . ' use ReservedWordsCommand::setKeywordList() instead.'
        );

        $this->keywordLists[$name] = new $class();
    }

    /** @return void */
    protected function configure()
    {
        $this
        ->setName('dbal:reserved-words')
        ->setDescription('Checks if the current database contains identifiers that are reserved.')
        ->setDefinition([
            new InputOption('connection', null, InputOption::VALUE_REQUIRED, 'The named database connection'),
            new InputOption(
                'list',
                'l',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Keyword-List name.'
            ),
        ])
        ->setHelp(<<<EOT
Checks if the current database contains tables and columns
with names that are identifiers in this dialect or in other SQL dialects.

By default all supported platform keywords are checked:

    <info>%command.full_name%</info>

If you want to check against specific dialects you can
pass them to the command:

    <info>%command.full_name% -l mysql -l pgsql</info>

The following keyword lists are currently shipped with Doctrine:

    * db2
    * mariadb102
    * mysql
    * mysql57
    * mysql80
    * oracle
    * pgsql
    * pgsql100
    * sqlite
    * sqlserver
EOT
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conn = $this->getConnection($input);

        $keywordLists = $input->getOption('list');

        if (is_string($keywordLists)) {
            $keywordLists = [$keywordLists];
        } elseif (! is_array($keywordLists)) {
            $keywordLists = [];
        }

        if (count($keywordLists) === 0) {
            $keywordLists = array_keys($this->keywordLists);
        }

        $keywords = [];
        foreach ($keywordLists as $keywordList) {
            if (! isset($this->keywordLists[$keywordList])) {
                throw new InvalidArgumentException(
                    "There exists no keyword list with name '" . $keywordList . "'. " .
                    'Known lists: ' . implode(', ', array_keys($this->keywordLists))
                );
            }

            $keywords[] = $this->keywordLists[$keywordList];
        }

        $output->write(
            'Checking keyword violations for <comment>' . implode(', ', $keywordLists) . '</comment>...',
            true
        );

        $schema  = $conn->getSchemaManager()->createSchema();
        $visitor = new ReservedKeywordsValidator($keywords);
        $schema->visit($visitor);

        $violations = $visitor->getViolations();
        if (count($violations) !== 0) {
            $output->write(
                'There are <error>' . count($violations) . '</error> reserved keyword violations'
                . ' in your database schema:',
                true
            );

            foreach ($violations as $violation) {
                $output->write('  - ' . $violation, true);
            }

            return 1;
        }

        $output->write('No reserved keywords violations have been found!', true);

        return 0;
    }

    private function getConnection(InputInterface $input): Connection
    {
        $connectionName = $input->getOption('connection');
        assert(is_string($connectionName) || $connectionName === null);

        if ($connectionName !== null) {
            return $this->connectionProvider->getConnection($connectionName);
        }

        return $this->connectionProvider->getDefaultConnection();
    }
}