<?php

namespace KignOrg\StateMachine\ConfigParser;

use KignOrg\StateMachine\Exceptions\InvalidConfigException;

class ConfigParser extends BaseConfigParser
{
    const DEFAULT_STATE_CONFIG_ITEM_KEY = 'defaultState';
    const STATES_CONFIG_ITEM_KEY = 'states';

    protected array $config;
    private array $stateParser = [];
    private array $stateMap = [];
    private array $referencedStates = [];

    /**
     * ConfigParser constructor.
     * @param array $config
     * @throws InvalidConfigException
     */
    private function __construct(array $config)
    {
        $this->config = $config;
        $this->parse();
    }


    /**
     * @param array $config
     * @return ConfigParser
     * @throws InvalidConfigException
     */
    public static function withArray(array $config): ConfigParser
    {
        return new ConfigParser($config);
    }

    /**
     * @param string $json
     * @return ConfigParser
     * @throws InvalidConfigException
     */
    public static function withJson(string $json): ConfigParser
    {
        $array = json_decode($json, true);
        if (!$array) {
            throw new InvalidConfigException("Json decode failed");
        }
        return new ConfigParser($array);
    }


    /**
     * @throws InvalidConfigException
     */
    private function parse()
    {
        $this->makeStateMap();
        $this->parseStates();
        $this->checkDefaultState();
    }


    /**
     * @throws InvalidConfigException
     */
    private function makeStateMap()
    {
        $stateNames = array_column($this->getStates(), StateParser::STATE_NAME_KEY);
        $this->stateMap = array_flip($stateNames);
        $this->exceptOnAmbiguousState();
    }

    /**
     * @throws InvalidConfigException
     */
    private function parseStates()
    {
        foreach ($this->getStates() as $state) {
            $stateParser = new StateParser($this, $state);
            $this->referencedStates = array_unique(array_merge($this->referencedStates, $stateParser->getStateReferences()));
            $this->stateParser[] = $stateParser;
        }
    }

    /**
     * @throws InvalidConfigException
     */
    private function exceptOnAmbiguousState()
    {
        if (count($this->stateMap) !== count($this->getStates())) {
            $stateNames = array_column($this->getStates(), 'name');
            $stateNamesUnique = array_unique($stateNames);
            $diff = array_diff_key($stateNames, $stateNamesUnique);
            throw new InvalidConfigException("Ambiguous state name: " . json_encode($diff));
        }
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getStates(): array
    {
        return $this->getConfigItem(self::STATES_CONFIG_ITEM_KEY, 'array');
    }


    /**
     * @throws InvalidConfigException
     */
    private function checkDefaultState()
    {
        $defaultState = $this->getConfigItem(self::DEFAULT_STATE_CONFIG_ITEM_KEY, 'string');
        $this->exceptOnInvalidState($defaultState);
    }


    /**
     * @param string $stateName
     * @throws InvalidConfigException
     */
    public function exceptOnInvalidState(string $stateName)
    {
        if (!isset($this->stateMap[$stateName])) {
            throw new InvalidConfigException("Invalid state '$stateName'");
        }
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function getDefaultState(): string
    {
        return $this->getConfigItem(self::DEFAULT_STATE_CONFIG_ITEM_KEY, 'string');
    }

    public function getStateParsers(): array
    {
        return $this->stateParser;
    }

    /**
     * @return array
     */
    public function getStateMap(): array
    {
        return $this->stateMap;
    }

    /**
     * @return array
     */
    public function getReferencedStates(): array
    {
        return $this->referencedStates;
    }


}
