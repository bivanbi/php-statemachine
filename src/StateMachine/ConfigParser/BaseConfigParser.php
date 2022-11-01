<?php


namespace KignOrg\StateMachine\ConfigParser;


use KignOrg\StateMachine\Exceptions\InvalidConfigException;

class BaseConfigParser
{
    protected array $config;

    /**
     * @param string $key
     * @param string $expectedType
     * @param bool $permitEmpty
     * @return mixed
     * @throws InvalidConfigException
     */
    public function getConfigItem(string $key, string $expectedType, bool $permitEmpty = false): mixed
    {
        if (!isset($this->config[$key])) {
            throw new InvalidConfigException("$key must be set");
        }

        if ($expectedType !== gettype($this->config[$key])) {
            throw new InvalidConfigException("$key must be $expectedType");
        }

        if (!$permitEmpty && !$this->config[$key]) {
            throw new InvalidConfigException("$key must not be empty");
        }
        return $this->config[$key];
    }
}
