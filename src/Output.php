<?php

declare(strict_types=1);

namespace Fi1a\Http;

use DateTime;
use Symfony\Component\Console\Exception\LogicException;

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
    public function send(RequestInterface $request, ResponseInterface $response): void
    {
        $this->sendHeaders($request, $response);
        $this->sendContent($response);
    }

    /**
     * Отправляет заголовки
     */
    protected function sendHeaders(RequestInterface $request, ResponseInterface $response): void
    {
        if ($this->isHeaderSent()) {
            throw new LogicException('Заголовки уже отправлены');
        }

        $this->header(
            sprintf(
                'HTTP/%s %s %s',
                $response->getHttpVersion(),
                $response->getStatus(),
                (string) $response->getReasonPhrase()
            ),
            true,
            $response->getStatus()
        );
        $headers = $response->getHeaders();
        if (!$headers->hasHeader('Date')) {
            $response->setDate(new DateTime());
        }
        foreach ($headers as $header) {
            assert($header instanceof HeaderInterface);
            $value = $header->getValue();
            if (!$value) {
                continue;
            }
            $this->header(
                $header->getName() . ': ' . $value,
                false,
                $response->getStatus()
            );
        }

        $cookies = $request->getCookies();
        foreach ($cookies as $cookie) {
            assert($cookie instanceof HttpCookieInterface);
            if (!$cookie->getNeedSet()) {
                continue;
            }
            $this->setCookie->set($cookie);
        }
    }

    /**
     * Отправляет содержимое
     */
    protected function sendContent(ResponseInterface $response): void
    {
        echo $response->getContent();
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
