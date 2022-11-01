<?php


namespace KignOrg\StateMachine\Factory;


use KignOrg\StateMachine\ConfigParser\StateParser;
use KignOrg\StateMachine\Exceptions\AmbiguousInputException;
use KignOrg\StateMachine\Exceptions\InvalidConfigException;
use KignOrg\StateMachine\State;

class StateFactory
{
    /**
     * @param StateParser $stateParser
     * @return State
     * @throws AmbiguousInputException
     * @throws InvalidConfigException
     */
    public static function withStateParser(StateParser $stateParser): State
    {
        $name = $stateParser->getName();
        $transitions = [];
        foreach($stateParser->getTransitionParsers() as $transitionParser) {
            $transitions[] = TransitionFactory::withTransitionParser($transitionParser);
        }
        return new State($name, ...$transitions);
    }
}