<?php


namespace KignOrg\StateMachine\ConfigParser;


use KignOrg\StateMachine\Exceptions\InvalidConfigException;

class TransitionParser extends BaseConfigParser
{
    const INPUT_KEY = 'input';
    const NEXT_STATE_NAME_KEY = 'nextState';
    const OUTPUT_KEY = 'output';

    protected array $config;
    private ConfigParser $configParser;
    private array $stateReferences;

    /**
     * TransitionParser constructor.
     * @param ConfigParser $configParser
     * @param array $transition
     * @throws InvalidConfigException
     */
    public function __construct(ConfigParser $configParser, array $transition)
    {
        $this->configParser = $configParser;
        $this->config = $transition;
        $this->parse();
    }


    /**
     * @throws InvalidConfigException
     */
    public function parse()
    {
        $this->stateReferences = [];
        $this->parseTransition();
    }


    /**
     * @throws InvalidConfigException
     */
    private function parseTransition()
    {
        $this->checkInput();
        $this->checkNextState();
        $this->addNextStateToReferenceMap();
        $this->checkOutput();
    }

    /**
     * @throws InvalidConfigException
     */
    private function checkInput()
    {
        $this->getConfigItem(self::INPUT_KEY, 'string');
    }


    /**
     * @throws InvalidConfigException
     */
    private function checkNextState()
    {
        $nextStateName = $this->getNextState();
        $this->configParser->exceptOnInvalidState($nextStateName);
    }


    /**
     * @throws InvalidConfigException
     */
    private function addNextStateToReferenceMap()
    {
        $nextState = $this->getNextState();
        $this->stateReferences[$nextState] = 1;
    }

    /**
     * @throws InvalidConfigException
     */
    private function checkOutput()
    {
        $this->getConfigItem(self::OUTPUT_KEY, 'array', true);
    }


    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function getInput(): string
    {
        return $this->getConfigItem(self::INPUT_KEY, 'string');
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function getNextState(): string
    {
        return $this->getConfigItem(self::NEXT_STATE_NAME_KEY, 'string');
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getOutput(): array
    {
        return $this->getConfigItem(self::OUTPUT_KEY, 'array', true);
    }

    public function getReferencedStateNames(): array
    {
        return array_keys($this->stateReferences);
    }
}
