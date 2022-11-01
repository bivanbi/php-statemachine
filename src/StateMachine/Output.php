<?php


namespace KignOrg\StateMachine;

class Output
{
    private array $attribs;
    private array $payload;

    /**
     * @param array $attribs
     */
    public function __construct(array $attribs = [])
    {
        $this->attribs = $attribs;
        $this->payload = [];
    }

    public function setAttrib(string $attribName, $value): Output
    {
        $this->attribs[$attribName] = $value;
        return $this;
    }

    /**
     * @param array $payload
     * @return Output
     */
    public function setPayload(array $payload = []): Output
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Get Output arguments specified in State Machine configuration
     *
     * @return array
     */
    public function getAttribs(): array
    {
        return $this->attribs;
    }

    public function getAttrib(string $attribName)
    {
        return $this->attribs[$attribName] ?? null;
    }

    /**
     * Get attributes received from triggered transition
     *
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function toArray(): array
    {
        return [ 'attribs' => $this->getAttribs(), 'payload' => $this->getPayload()];
    }
}
