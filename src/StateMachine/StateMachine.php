<?php


namespace KignOrg\StateMachine;


use KignOrg\StateMachine\Exceptions\AmbiguousStateException;
use KignOrg\StateMachine\Exceptions\IllegalInputException;
use KignOrg\StateMachine\Exceptions\IllegalStateException;
use KignOrg\StateMachine\Exceptions\StateMachineNotInitializedException;

class StateMachine
{
    private State $defaultState;
    private array $possibleState;
    private State|null $currentState;
    private array $stateNameToStateMap;

    /**
     * @param string $defaultStateName
     * @param State ...$possibleState
     * @throws AmbiguousStateException|IllegalStateException
     */
    public function __construct(string $defaultStateName, State ...$possibleState)
    {
        $this->possibleState = $possibleState;
        $this->makeStateMap();
        $this->defaultState = $this->getStateByName($defaultStateName);
    }

    /**
     * @throws AmbiguousStateException
     */
    private function makeStateMap(): void
    {
        $states = array_column($this->possibleState, State::STATE_NAME_COLUMN);
        $this->stateNameToStateMap = array_combine($states, $this->possibleState);
        $this->exceptOnAmbiguousState();
    }

    /**
     * @throws AmbiguousStateException
     */
    private function exceptOnAmbiguousState(): void
    {
        if (count($this->stateNameToStateMap) !== count($this->possibleState)) {
            throw new AmbiguousStateException("Ambiguous state name");
        }
    }

    /**
     * @param string|null $currentStateName
     * @return StateMachine
     * @throws IllegalStateException
     */
    public function initialize(string $currentStateName = null): StateMachine
    {
        $this->currentState = $this->getStateByName($currentStateName);
        return $this;
    }

    /**
     * @param string|null $stateName
     * @return State
     * @throws IllegalStateException
     */
    private function getStateByName(string $stateName = null): State
    {
        if (null === $stateName) {
            return $this->defaultState;
        }
        $this->exceptOnInvalidStateName($stateName);
        return $this->stateNameToStateMap[$stateName];
    }

    /**
     * @param string $state
     * @throws IllegalStateException
     */
    private function exceptOnInvalidStateName(string $state): void
    {
        if (!isset($this->stateNameToStateMap[$state])) {
            throw new IllegalStateException("Invalid state '$state'");
        }
    }

    /**
     * @param string $input
     * @param array $payload will be passed to output
     * @return Output
     * @throws IllegalInputException
     * @throws IllegalStateException
     * @throws StateMachineNotInitializedException
     */
    public function triggerTransition(string $input, array $payload = []): Output
    {
        $this->exceptIfNotInitialized();
        $payload1 = $payload;
        $this->currentState->setInput($input, $payload1);
        $output = $this->currentState->getOutput();
        $nextStateName = $this->currentState->getNextStateName();
        $this->setCurrentStateByName($nextStateName);
        return $output;
    }

    /**
     * @param string $stateName
     * @throws IllegalStateException
     */
    private function setCurrentStateByName(string $stateName): void
    {
        $this->setCurrentState($this->getStateByName($stateName));
    }


    private function setCurrentState(State $state): void
    {
        $this->currentState = $state;
    }

    /**
     * @return string
     * @throws StateMachineNotInitializedException
     */
    public function getCurrentStateName(): string
    {
        return $this->getCurrentState()->getName();
    }

    /**
     * @return State
     * @throws StateMachineNotInitializedException
     */
    public function getCurrentState(): State
    {
        $this->exceptIfNotInitialized();
        return $this->currentState;
    }

    /**
     * @throws StateMachineNotInitializedException
     */
    private function exceptIfNotInitialized(): void
    {
        if (!$this->isInitialized()) {
            throw new StateMachineNotInitializedException("must be initialized first");
        }
    }

    private function isInitialized(): bool
    {
        return $this->currentState instanceof State;
    }
}
