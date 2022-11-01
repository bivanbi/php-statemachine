<?php


namespace KignOrg\StateMachine\Factory;

use KignOrg\StateMachine\ConfigParser\ConfigParser;
use KignOrg\StateMachine\Exceptions\AmbiguousInputException;
use KignOrg\StateMachine\Exceptions\AmbiguousStateException;
use KignOrg\StateMachine\Exceptions\IllegalStateException;
use KignOrg\StateMachine\Exceptions\InvalidConfigException;
use KignOrg\StateMachine\StateMachine;

class StateMachineFactory
{

    /**
     * @param string $json
     * @return StateMachine
     * @throws AmbiguousInputException
     * @throws AmbiguousStateException
     * @throws IllegalStateException
     * @throws InvalidConfigException
     */
    public static function withJson(string $json): StateMachine
    {
        $configParser = ConfigParser::withJson($json);
        return self::withConfigParser($configParser);
    }


    /**
     * @param array $config
     * @return StateMachine
     * @throws AmbiguousInputException
     * @throws AmbiguousStateException
     * @throws IllegalStateException
     * @throws InvalidConfigException
     */
    public static function withArray(array $config): StateMachine
    {
        $configParser = ConfigParser::withArray($config);
        return self::withConfigParser($configParser);
    }


    /**
     * @param ConfigParser $configParser
     * @return StateMachine
     * @throws InvalidConfigException
     * @throws AmbiguousInputException
     * @throws AmbiguousStateException
     * @throws IllegalStateException
     */
    public static function withConfigParser(ConfigParser $configParser): StateMachine
    {
        $defaultState = $configParser->getDefaultState();
        $states = [];
        foreach ($configParser->getStateParsers() as $stateParser) {
            $states[] = StateFactory::withStateParser($stateParser);
        }
        return new StateMachine($defaultState, ...$states);
    }
}