<?php


namespace KignOrg\StateMachine;


use KignOrg\StateMachine\Exceptions\AmbiguousInputException;
use KignOrg\StateMachine\Exceptions\IllegalInputException;

class State
{
    const STATE_NAME_COLUMN = 'name';

    private string $name;
    private array $transition;
    private array $inputMap;

    private string $input;
    private array $payload;

    /**
     * @param string $name
     * @param Transition ...$transition
     * @throws AmbiguousInputException
     */
    public function __construct(string $name, Transition ...$transition)
    {
        $this->name = $name;
        $this->transition = $transition;
        $this->makeInputMap();
    }

    /**
     * @throws AmbiguousInputException
     */
    private function makeInputMap(): void
    {
        $inputs = array_column($this->transition, 'input');
        $this->inputMap = array_combine($inputs, $this->transition);
        $this->exceptOnAmbiguousInput();
    }

    /**
     * @throws AmbiguousInputException
     */
    private function exceptOnAmbiguousInput(): void
    {
        if (count($this->inputMap) !== count($this->transition)) {
            throw new AmbiguousInputException("Ambiguous input");
        }
    }


    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $input
     * @param array $payload will be passed to output
     * @throws IllegalInputException
     */
    public function setInput(string $input, array $payload = []): void
    {
        $this->exceptOnIllegalInput($input);
        $this->input = $input;
        $this->payload = $payload;
    }

    /**
     * @return string
     * @throws IllegalInputException
     */
    public function getNextStateName(): string
    {
        return $this->getTransitionForInput()->getNextStateName();
    }

    /**
     * @return Output
     * @throws IllegalInputException
     */
    public function getOutput(): Output
    {
        return $this->getTransitionForInput()->getOutput($this->payload);
    }

    /**
     * @return Transition
     * @throws IllegalInputException
     */
    private function getTransitionForInput(): Transition
    {
        $this->exceptIfInputIsNotSet();
        return $this->inputMap[$this->input];
    }

    /**
     * @param string $input
     * @throws IllegalInputException
     */
    private function exceptOnIllegalInput(string $input): void
    {
        if (!isset($this->inputMap[$input])) {
            throw new IllegalInputException("Illegal input '$input' for state '".$this->name."'");
        }
    }

    /**
     * @throws IllegalInputException
     */
    private function exceptIfInputIsNotSet(): void
    {
        if (!$this->isInputSet()) {
            throw new IllegalInputException("must set input first");
        }
    }

    private function isInputSet(): bool
    {
        return isset($this->input);
    }

    public function __isset($name)
    {
        return property_exists($this, $name);
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }
}
