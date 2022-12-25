<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Буфферизированный вывод
 */
class BufferOutput extends Output implements BufferOutputInterface
{
    /**
     * @var string[]
     */
    private $buffer = [];

    /**
     * @inheritDoc
     */
    public function send(RequestInterface $request, ResponseInterface $response): void
    {
        parent::send($request, $response);
        $this->sendContent($response);
    }

    private function sendContent(ResponseInterface $response): void
    {
        if ($response->isInformational() || $response->isEmpty() || $response->isRedirection()) {
            $this->buffer = [];
            ob_clean();
            ob_end_flush();

            return;
        }
        $out = '';
        $content = ob_get_contents();
        ob_clean();
        foreach ($this->buffer as $buffer) {
            $out .= $buffer;
        }
        $out .= $content;
        echo $out;
        ob_end_flush();
    }

    /**
     * @inheritDoc
     */
    public function start(): bool
    {
        array_push($this->buffer, ob_get_contents());
        ob_clean();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(): string
    {
        return ob_get_contents();
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->buffer = [];
        ob_clean();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function end(): string
    {
        $content = $this->get();
        ob_clean();
        if (count($this->buffer)) {
            echo array_pop($this->buffer);
        }

        return $content;
    }
}
