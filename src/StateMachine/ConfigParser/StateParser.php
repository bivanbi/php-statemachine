<?php


namespace KignOrg\StateMachine\ConfigParser;


use KignOrg\StateMachine\Exceptions\InvalidConfigException;

class StateParser extends BaseConfigParser
{
    const STATE_NAME_KEY = 'name';
    const TRANSITIONS_KEY = 'transitions';

    protected array $config;
    /** @var ConfigParser */
    private ConfigParser $configParser;
    private array $transitionParser = [];
    private array $inputMap = [];
    private array $stateReference = [];

    /**
     * @param ConfigParser $configParser
     * @param array $state
     * @throws InvalidConfigException
     */
    public function __construct(ConfigParser $configParser, array $state)
    {
        $this->configParser = $configParser;
        $this->config = $state;
        $this->parse();
    }


    /**
     * @throws InvalidConfigException
     */
    private function parse()
    {
        $this->stateReference = [];
        $this->parseState();
    }

    /**
     * @throws InvalidConfigException
     */
    private function parseState()
    {
        $this->checkName();
        $this->makeInputMap();
        $this->parseTransition();
    }


    /**
     * @throws InvalidConfigException
     */
    public function checkName()
    {
        $stateName = $this->getConfigItem(self::STATE_NAME_KEY, 'string');
        $this->configParser->exceptOnInvalidState($stateName);
    }

    /**
     * @throws InvalidConfigException
     */
    private function parseTransition()
    {
        $transitions = $this->getConfigItem(self::TRANSITIONS_KEY, 'array');

        foreach ($transitions as $transition) {
            $transitionParser = new TransitionParser($this->configParser, $transition);
            $this->stateReference = array_merge($this->stateReference, $transitionParser->getReferencedStateNames());
            $this->transitionParser[] = $transitionParser;
        }
    }

    /**
     * @throws InvalidConfigException
     */
    private function makeInputMap()
    {
        $inputs = array_column($this->getTransitions(), TransitionParser::INPUT_KEY);
        $this->inputMap = array_flip($inputs);
        $this->exceptOnAmbiguousInput();
    }


    /**
     * @throws InvalidConfigException
     */
    private function exceptOnAmbiguousInput()
    {
        if (count($this->inputMap) !== count($this->getTransitions())) {
            $inputs = array_column($this->getTransitions(), 'input');
            $inputsUnique = array_unique($inputs);
            $diff = array_diff_key($inputs, $inputsUnique);

            throw new InvalidConfigException("state '" . $this->getName() . "' has ambiguous input: " . json_encode($diff));
        }
    }

    public function getName()
    {
        return $this->config[self::STATE_NAME_KEY];
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     */
    public function getTransitions(): mixed
    {
        return $this->getConfigItem(self::TRANSITIONS_KEY, 'array');
    }

    public function getStateReferences(): array
    {
        return $this->stateReference;
    }

    public function getTransitionParsers(): array
    {
        return $this->transitionParser;
    }

    /**
     * @return array
     */
    public function getInputMap(): array
    {
        return $this->inputMap;
    }


}
