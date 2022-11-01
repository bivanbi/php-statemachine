<?php


namespace KignOrg\StateMachine;


class Transition
{
    private string $input;
    private string $nextStateName;
    private Output $output;

    public function __construct(string $input, string $nextStateName, Output $output)
    {
        $this->input = $input;
        $this->nextStateName = $nextStateName;
        $this->output = $output;
    }

    public function getNextStateName(): string
    {
        return $this->nextStateName;
    }

    public function getOutput(array $payload): Output
    {

        $this->output->setPayload($payload);
        return $this->output;
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
