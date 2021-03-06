<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;

/**
 * Operation for the distinct command.
 *
 * @api
 * @see \MongoDB\Collection::distinct()
 * @see http://docs.mongodb.org/manual/reference/command/distinct/
 */
class Distinct implements Executable
{
    private static $wireVersionForCollation = 5;
    private static $wireVersionForReadConcern = 4;

    private $databaseName;
    private $collectionName;
    private $fieldName;
    private $filter;
    private $options;

    /**
     * Constructs a distinct command.
     *
     * Supported options:
     *
     *  * collation (document): Collation specification.
     *
     *    This is not supported for server versions < 3.4 and will result in an
     *    exception at execution time if used.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *    This is not supported for server versions < 3.2 and will result in an
     *    exception at execution time if used.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param string       $fieldName      Field for which to return distinct values
     * @param array|object $filter         Query by which to filter documents
     * @param array        $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $collectionName, $fieldName, $filter = [], array $options = [])
    {
        if ( ! is_array($filter) && ! is_object($filter)) {
            throw InvalidArgumentException::invalidType('$filter', $filter, 'array or object');
        }

        if (isset($options['collation']) && ! is_array($options['collation']) && ! is_object($options['collation'])) {
            throw InvalidArgumentException::invalidType('"collation" option', $options['collation'], 'array or object');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], 'MongoDB\Driver\ReadConcern');
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], 'MongoDB\Driver\ReadPreference');
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->fieldName = (string) $fieldName;
        $this->filter = $filter;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return mixed[]
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if collation or read concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        if (isset($this->options['collation']) && ! \MongoDB\server_supports_feature($server, self::$wireVersionForCollation)) {
            throw UnsupportedException::collationNotSupported();
        }

        if (isset($this->options['readConcern']) && ! \MongoDB\server_supports_feature($server, self::$wireVersionForReadConcern)) {
            throw UnsupportedException::readConcernNotSupported();
        }

        $readPreference = isset($this->options['readPreference']) ? $this->options['readPreference'] : null;

        $cursor = $server->executeCommand($this->databaseName, $this->createCommand(), $readPreference);
        $result = current($cursor->toArray());

        if ( ! isset($result->values) || ! is_array($result->values)) {
            throw new UnexpectedValueException('distinct command did not return a "values" array');
        }

        return $result->values;
    }

    /**
     * Create the distinct command.
     *
     * @return Command
     */
    private function createCommand()
    {
        $cmd = [
            'distinct' => $this->collectionName,
            'key' => $this->fieldName,
        ];

        if ( ! empty($this->filter)) {
            $cmd['query'] = (object) $this->filter;
        }

        if (isset($this->options['collation'])) {
            $cmd['collation'] = (object) $this->options['collation'];
        }

        if (isset($this->options['maxTimeMS'])) {
            $cmd['maxTimeMS'] = $this->options['maxTimeMS'];
        }

        if (isset($this->options['readConcern'])) {
            $cmd['readConcern'] = \MongoDB\read_concern_as_document($this->options['readConcern']);
        }

        return new Command($cmd);
    }
}
