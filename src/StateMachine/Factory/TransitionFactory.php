<?php


namespace KignOrg\StateMachine\Factory;


use KignOrg\StateMachine\ConfigParser\TransitionParser;
use KignOrg\StateMachine\Exceptions\InvalidConfigException;
use KignOrg\StateMachine\Output;
use KignOrg\StateMachine\Transition;

class TransitionFactory
{
    /**
     * @param TransitionParser $transitionParser
     * @return Transition
     * @throws InvalidConfigException
     */
    public static function withTransitionParser(TransitionParser $transitionParser): Transition
    {
        $input = $transitionParser->getInput();
        $nextState = $transitionParser->getNextState();
        $output = new Output($transitionParser->getOutput());
        return new Transition($input, $nextState, $output);
    }
}