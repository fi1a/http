<?php

declare(strict_types=1);

namespace Fi1a\Http;

use LogicException;

/**
 * Отправляет заголовки
 */
class Output implements OutputInterface
{
    /**
     * @var SetCookieInterface
     */
    private $setCookie;

    public function __construct(SetCookieInterface $setCookie)
    {
        $this->setCookie = $setCookie;
    }

    /**
     * @inheritDoc
     */
    public function send(ResponseInterface $response): void
    {
        $this->sendHeaders($response);
        $this->sendContent($response);
    }

    /**
     * @inheritDoc
     */
    public function sendHeaders(ResponseInterface $response): void
    {
        if ($this->isHeaderSent()) {
            throw new LogicException('Заголовки уже отправлены');
        }

        $this->header(
            sprintf(
                'HTTP/%s %s %s',
                $response->httpVersion(),
                $response->status(),
                (string) $response->reasonPhrase()
            ),
            true,
            $response->status()
        );
        $headers = $response->headers();
        foreach ($headers as $header) {
            assert($header instanceof HeaderInterface);
            $value = $header->getValue();
            if (!$value) {
                continue;
            }
            $this->header(
                $header->getName() . ': ' . $value,
                false,
                $response->status()
            );
        }

        $cookies = $response->cookies();
        foreach ($cookies as $cookie) {
            assert($cookie instanceof HttpCookieInterface);
            if (!$cookie->getNeedSet()) {
                continue;
            }
            $this->setCookie->set($cookie);
        }
    }

    /**
     * @inheritDoc
     */
    public function sendContent(ResponseInterface $response): void
    {
        if ($response instanceof ContentResponseInterface) {
            echo $response->getContent();
        }
    }

    /**
     * Заголовки уже отправлены или нет
     */
    protected function isHeaderSent(): bool
    {
        return headers_sent();
    }

    /**
     * Устанавливает заголовок
     *
     * @codeCoverageIgnore
     */
    protected function header(string $header, bool $replace, int $responseCode): void
    {
        header($header, $replace, $responseCode);
    }
}
