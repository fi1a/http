# PHP абстракция для HTTP-запроса (request), ответа (response), сессии (session) и cookies.

[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
![Coverage Status][badge-coverage]
[![Total Downloads][badge-downloads]][downloads]

В PHP HTTP-запрос (request) представлен глобальными переменными ($_GET, $_POST, $_FILES, $_COOKIE, $_SESSION, ...),
а ответ (response) генерируется функциями (echo, header(), setcookie(), ...)

Данный пакет заменяет эти стандартные глобальные переменные и функции PHP объектно-ориентированным слоем,
инкапсулируя HTTP-запрос (request) и ответ (response) в объекты `Fi1a\Http\RequestInterface` и `Fi1a\Http\ResponseInterface`,
которые предлагают удобный API для работы с ними.

## Установка

Установить этот пакет можно как зависимость, используя Composer.

``` bash
composer require fi1a/http
```

## Хелперы

В пакете доступны следующие хелперы:

- http(): HttpInterface - хелпер для HttpInterface;
- request(?RequestInterface $request = null): RequestInterface - хелпер для текушего запроса;
- response(?ResponseInterface $response = null): ResponseInterface - хелпер для текушего ответа;
- session(?SessionStorageInterface $session = null): SessionStorageInterface - хелпер для доступа к сессии;
- buffer(?BufferOutputInterface $buffer = null): BufferOutputInterface - хелпер для буферизированного вывода;
- redirect($location = null, ?int $status = null, $headers = []): RedirectResponse - возвращает ответ для реализации перенаправления;
- json($data = null, ?int $status = null, $headers = []): JsonResponseInterface - возвращает JSON-ответ.

## Dependency injection

Контейнер dependency injection доступен из пакета [fi1a/dependency-injection](https://github.com/fi1a/dependency-injection)

Для интерфейсов, в контейнере dependency injection, доступны следующие определения:

- Fi1a\Http\HttpInterface;
- Fi1a\Http\RequestInterface;
- Fi1a\Http\ResponseInterface;
- Fi1a\Http\Session\SessionStorageInterface;
- Fi1a\Http\BufferOutputInterface;
- Fi1a\Http\RedirectResponseInterface;
- Fi1a\Http\JsonResponseInterface.

```php
di()->get(Fi1a\Http\RequestInterface::class)->all();
```

## HTTP-запрос

HTTP-запрос — это объект реализующий интерфейс `Fi1a\Http\RequestInterface`. HTTP-запрос является не изменяемым, у него нет сеттеров.
Для доступа к текущему запросу можно использовать хелпер `request()`.

```php
request()->query()->get('foo'); // bar
```

Доступные методы `Fi1a\Http\RequestInterface`:

| Метод                                    | Описание                                   |
|------------------------------------------|--------------------------------------------|
| post(): PathAccessInterface              | Возвращает POST                            |
| query(): PathAccessInterface             | Возвращает GET значения                    |
| all(): PathAccessInterface               | Все значения из GET, POST, FILES, BODY     |
| only(array $keys): PathAccessInterface   | Только переданные ключи из GET и POST      |
| files(): UploadFileCollectionInterface   | Возвращает файлы                           |
| setRawBody($body)                        | Устанавливает содержание                   |
| rawBody()                                | Возвращает содержание                      |
| setBody($body)                           | Устанавливает преобразованное содержание   |
| body()                                   | Возвращает преобразованное содержание      |
| cookies(): HttpCookieCollectionInterface | Возвращает cookies                         |
| headers(): HeaderCollectionInterface     | Вернуть заголовки                          |
| server(): ServerCollectionInterface      | Возвращает значение SERVER                 |
| options(): PathAccessInterface           | Возвращает опции                           |
| clientIp(): string                       | Возвращает IP адрес клиента                |
| scriptName(): string                     | Возвращает запрошенный файл скрипта        |
| path(): string                           | Возвращает путь                            |
| basePath(): string                       | Путь без файла                             |
| normalizedBasePath(): string             | Путь без файла с / на конце                |
| queryString(): string                    | Возвращает строку запроса                  |
| host(): string                           | Хост                                       |
| httpHost(): string                       | Хост и порт, если он не стандартный        |
| schemeAndHttpHost(): string              | Схема, хост и порт                         |
| isSecure(): bool                         | Использован https                          |
| scheme(): string                         | Возвращает схему запроса                   |
| port(): int                              | Возвращает порт                            |
| user(): string                           | Возвращает пользователя                    |
| password(): ?string                      | Возвращает пароль                          |
| userInfo(): string                       | Возвращает пользователя и пароль           |
| pathAndQuery(): string                   | Возвращает путь и строку запроса           |
| uri(): string                            | Возвращает урл с хостом и строку запроса   |
| method(): string                         | Возвращает метод                           |
| isMethod(string $method): bool           | Определяет метод                           |
| contentType(): string                    | Возвращает тип содержания                  |
| isNoCache(): bool                        | Без кеша                                   |
| isXmlHttpRequest(): bool                 | Возвращает true если запрос XMLHttpRequest |
| eTags(): array                           | Возвращает ETags                           |
| script(): string                         | Возвращает путь до выполняемого скрипта    |

Методы post(), query(), all(), only(array $keys), options() возвращают результат в виде объекта, реализующего
`Fi1a\Collection\DataType\PathAccessInterface` из пакета [fi1a/collection](https://github.com/fi1a/collection)

### JSON-запрос

Если был передан заголовок `Content-Type: application/json` при запросе клиентом, то промежуточное ПО автоматически декодирует тело запроса
из JSON-формата.
Для того чтобы, получить доступ к декодированному значению вызовите метод `body()`:

```php
request()->rawBody(); // {"foo":"bar"}
request()->body(); // ['foo' => 'bar']
```

### uri()

Возвращает URL-адрес запроса

```php
request()->uri(); // https://domain.ru/path/?foo=bar
```

### query()

Возвращает GET параметры запроса:

```php
request()->query()->get('foo'); // bar
```

### post()

Возвращает POST параметры запроса:

```php
request()->post()->get('foo'); // bar
```

### all()

Возвращает GET, POST, FILES, BODY параметры запроса:

```php
request()->all()->get('foo'); // bar
```

### only()

Только переданные ключи из параметров GET и POST:

```php
request()->only(['foo'])->get('foo'); // bar
```

### files()

Возвращает коллекцию `Fi1a\Http\UploadFileCollectionInterface` загруженных файлов `Fi1a\Http\UploadFileInterface`:

```php
$collection = request()->files();
$uploadFile = $collection->get('some:file1');
$uploadFile->getName(); // file.txt
```

### cookies()

Возвращает коллекцию `Fi1a\Http\HttpCookieCollectionInterface` cookies `Fi1a\Http\HttpCookieInterface`:

```php
$collection = request()->cookies();
$cookie = $collection->getByName('cookieName');
$cookie->getValue(); // cookieValue
```

### method()

Возвращает HTTP метод, с помощью которого был сделан запрос.

```php
request()->method(); // POST
```

### headers()

Возвращает все HTTP заголовки:

```php
$headers = request()->headers();
$header = $headers->getLastHeader('X-Header');
$header->getValue(); // HeaderValue
```

## HTTP-ответ

HTTP-ответ представляет собой объект `Fi1a\Http\ResponseInterface`.
Он содержит всю информацию, которая должна быть отправлена клиенту для текущего запроса.
Конструктор принимает до трех аргументов: статус ответа, массив заголовков HTTP и объект запроса `Fi1a\Http\RequestInterface`.
Для доступа к текущему ответу можно использовать хелпер `response()`.

```php
use \Fi1a\Http\ResponseInterface;

response()->setStatus(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
```

Установить новый текущий ответ:

```php
use Fi1a\Http\Response;
use Fi1a\Http\ResponseInterface;

$response = new Response(ResponseInterface::HTTP_NOT_FOUND);
response($response);
```

Доступные методы `Fi1a\Http\ResponseInterface`:

| Метод                                                | Описание                                              |
|------------------------------------------------------|-------------------------------------------------------|
| setStatus(int $status, ?string $reasonPhrase = null) | Устанавливает код и текст ответа                      |
| getStatus(): int                                     | Возвращает код ответа                                 |
| getReasonPhrase(): ?string                           | Возвращает текст ответа                               |
| withHeaders(HeaderCollectionInterface $headers)      | Устанавливает заголовки                               |
| getHeaders(): HeaderCollectionInterface              | Возвращает заголовки                                  |
| withHeader(string $name, string $value)              | Добавляет заголовок с определенным именем и значением |
| withoutHeader(string $name)                          | Удалить заголовки с определенным именем               |
| hasHeader(string $name): bool                        | Проверяет наличие заголовка                           |
| cookies(): HttpCookieCollectionInterface             | Возвращает cookies                                    |
| setCookies(HttpCookieCollectionInterface $cookies)   | Устанавливает cookies                                 |
| setHttpVersion(string $version)                      | Устанавливает версию HTTP протокола                   |
| getHttpVersion(): string                             | Возвращает HTTP версию протокола                      |
| isEmpty(): bool                                      | Если true, то ответ пустой                            |
| isInformational(): bool                              | Если true, то ответ информационный                    |
| isSuccessful(): bool                                 | Если true, то ответ успешный                          |
| isClientError(): bool                                | Если true, то клиентская ошибка                       |
| isServerError(): bool                                | Если true, то серверная ошибка                        |
| isOk(): bool                                         | Если true, то ответ 200 OK                            |
| isForbidden(): bool                                  | Если true, то 403 Forbidden                           |
| isNotFound(): bool                                   | Если true, то 404 Not found                           |
| isRedirection(?string $location = null): bool        | Если true, то перенаправление                         |
| setCharset(string $charset)                          | Устанавливает кодировку                               |
| getCharset(): string                                 | Возвращает кодировку                                  |
| setDate(DateTime $date)                              | Устанавливает дату                                    |
| getDate(): DateTime                                  | Возвращает дату                                       |
| getLastModified(): ?DateTime                         | Возвращает время последнего изменения                 |
| setLastModified(?DateTime $date = null)              | Устанавливает время последнего изменения              |


### setStatus(int $status, ?string $reasonPhrase = null)

Устанавливает код ответа состояния.
Рекомендуется использовать предопределенные константы `Fi1a\Http\ResponseInterface::HTTP_OK`, ... вместо реальных чисел.

```php
use Fi1a\Http\ResponseInterface;

response()->setStatus(ResponseInterface::HTTP_OK, 'OK');
```

### withHeader(string $name, string $value)

Добавляет заголовок с определенным именем и значением к ответу:

```php
response()->withHeader('X-Header', 'Value');
```

### withoutHeader(string $name)

Удалить заголовки с определенным именем:

```php
response()->withoutHeader('X-Header');
```

### cookies

Возвращает cookies.
Для того чтобы установить новую cookie, нужно добавить ее в коллекцию как в примере:

```php
use Fi1a\Http\HttpCookie;

$cookie = new HttpCookie();
$cookie->setDomain('domain.ru');
$cookie->setName('CookieName');
$cookie->setPath('/');
$cookie->setValue('Value');

response()->cookies()->add($cookie);

buffer()->sendHeaders(response());
```

Cookie будет установлена при вызове метода `sendHeaders` класса `Fi1a\Http\OutputInterface`.

_В  фреймворке Elpha, нет необходимости вызывать метод `sendHeaders` класса `Fi1a\Http\BufferOutputInterface`,
фреймворк это сделает за вас._

## Отправка ответа

Отправка ответа клиенту осуществляется вызовом метода `send()` класса, реализующего интерфейс `Fi1a\Http\BufferOutputInterface`:

```php
buffer()->send(response());
```

_В  фреймворке Elpha, нет необходимости вызывать метод `send` класса `Fi1a\Http\BufferOutputInterface`,
фреймворк это сделает за вас._

## Перенаправление

Перенаправление реализуется ответом с интерфейсом `Fi1a\Http\RedirectResponseInterface`.
Можно воспользоваться хелперами:

```php
use Fi1a\Http\ResponseInterface;

response(redirect()->to('/redirect/path', ResponseInterface::HTTP_MOVED_PERMANENTLY))
```

С помощью хелпера `redirect` мы создает ответ с перенаправлением по адресу '/redirect/path'
и статусом ResponseInterface::HTTP_MOVED_PERMANENTLY и устанавливаем его используя `response`.

## JSON-ответ

JSON-ответ реализуется интерфейсом `Fi1a\Http\JsonResponseInterface`.
Можно воспользоваться хелперами:

```php
response(json()->data(['foo' => 'bar']));
```

С помощью хелпера `json` мы создает JSON-ответ и устанавливаем его используя `response`.
После установки JSON-ответа, промежуточное ПО устанавливает необходимые заголовки и выводит результат.

## Сессия

Если у вас есть сессия, вы можете получить к ней доступ через хелпер `session()`.
Сессия имеет интерфейс `Fi1a\Http\SessionStorageInterface`.
Перед тем как получать или устанавливать значения в сессию, ее нужно открыть с помощью метода `open()`.

_В  фреймворке Elpha, нет необходимости открывать сессию, фреймворк это сделает за вас._

```php
$session = session();
if (!$session->isOpen()) {
    $session->open();
}

$session->getValues()->set('foo:bar', 'baz');
$session->getValues()->get('foo:bar'); // baz

$session->close();
```

## Flush

Сохраняет значение в сессии. После получения значения, стирает его.

```php
use Fi1a\Http\Flush;

if (!session()->isOpen()) {
    session()->open();
}

$flush = new Flush();

$flush->set('foo', 'bar');

$flush->get('foo'); // bar
$flush->get('foo'); // null
```

## Uri

Класс реализующий интрефейс `Fi1a\Http\UriInterface` упрощает работу с URI и с его отдельными компонентами:

```
https://user:password@domain.ru:8080/url/path/?foo=bar#fragment
|----| |---| |------| |-------| |---||-------| |-----| |------|
  |      |      |        |        |     |         |        |
scheme  user password   host     port  path     query   fragment
```

Генерация Uri:

```php
use Fi1a\Http\Uri;

$uri = new Uri();

$uri->withScheme('https')
    ->withHost('domain.ru')
    ->withPath('/path/')
    ->withQueryParams([
        'foo' => 'bar',
    ]);

$uri->getUri(); // "https://domain.ru/path/?foo=bar"
```

Вы также можете задать URL-адрес строкой, а затем использовать его компоненты:

```php
use Fi1a\Http\Uri;

$uri = new Uri('https://domain.ru/path/?foo=bar');

$uri->getHost(); // "domain.ru"
$uri->getPath(); // "/path/"
```

Доступные методы `Fi1a\Http\UriInterface`:

| Метод                                                | Описание                                      |
|------------------------------------------------------|-----------------------------------------------|
| getScheme(): string                                  | Схема                                         |
| withScheme(string $scheme)                           | Задать схему                                  |
| isSecure(): bool                                     | Использован https                             |
| getUserInfo(): string                                | Компонент информации о пользователе URI       |
| getUser(): string                                    | Возвращает имя пользователя                   |
| getPassword(): ?string                               | Возвращает пароль                             |
| withUserInfo(string $user, ?string $password = null) | Задать информацию о пользователе              |
| getHost(): string                                    | Хост                                          |
| withHost(string $host)                               | Задать хост                                   |
| getPort(): ?int                                      | Порт                                          |
| withPort(?int $port)                                 | Задать порт                                   |
| getPath(): string                                    | Часть пути URI                                |
| withPath(string $path)                               | Установить часть пути URI                     |
| getBasePath(): string                                | Урл без файла                                 |
| getNormalizedBasePath(): string                      | Урл без файла с / на конце                    |
| getQuery(): string                                   | Строка запроса в URI                          |
| withQuery(string $query)                             | Задать строку запроса URI                     |
| getQueryParams(): PathAccessInterface                | Массив запроса в URI                          |
| withQueryParams($queryParams)                        | Задать массив запроса в URI                   |
| getFragment(): string                                | Фрагмент URI                                  |
| withFragment(string $fragment)                       | Задать фрагмент URI                           |
| getUrl(): string                                     | Возвращает URL                                |
| getUri(): string                                     | Возвращает URI                                |
| getPathAndQuery(): string                            | Возвращает путь и строку запроса              |
| getAuthority(): string                               | Компонент полномочий URI                      |
| getMaskedUri(): string                               | Возвращает URI с маской на данных авторизации |
| replace(string $uri = '', array $variables = [])     | Заменить адрес переданным значением           |

[badge-release]: https://img.shields.io/packagist/v/fi1a/http?label=release
[badge-license]: https://img.shields.io/github/license/fi1a/http?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/fi1a/http?style=flat-square
[badge-coverage]: https://img.shields.io/badge/coverage-100%25-green
[badge-downloads]: https://img.shields.io/packagist/dt/fi1a/http.svg?style=flat-square&colorB=mediumvioletred

[packagist]: https://packagist.org/packages/fi1a/http
[license]: https://github.com/fi1a/http/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/fi1a/http