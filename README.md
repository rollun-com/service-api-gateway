# service-api-gateway
---

## Api Gateway
Сервис который проксирует через себя все запросы к api,
 и перенаправляет их в конкретные сервисы.
[Более детально о патерне микросервисной архитектуры - api-gateway](http://microservices.io/patterns/apigateway.html)

Все внутренние сервисы, общаются между собой исключительно через api-gateway. 
Используя указанный ниже протокол общения.

## Принцип работы

Маска запроса `{services}.domain.com/{send_path}`
> Поддерживаемый типы запроса - все согласно стандарта http
* services - имя сервиса в который будет передан запрос
* send_path - путь по которому будет отправлен запрос в сервис

### Пример  

Допустим мы отправим запрос на api-gateway.
```
GET
http://amazonStore.api-gateway/api/webhook/withdraw?asin="AAAAAAAA"&sku="AA-AAA-AAA"
```
Он в свою очередь, примет это запрос, разберет его и переотправит на сервис с именем `amazonStore`.
Допустим, у нас есть конфиг соответсвия.
```
amazonStore: 192.168.122.23
amazonStore: 192.168.123.23
```
Тогда имени `amazonStore` будет соответсвовать ip `192.168.122.23`.
Переотправленый запрос будет выглядеть  
```
GET
http://192.168.122.23/api/webhook/withdraw?asin="AAAAAAAA"&sku="AA-AAA-AAA"
```
После того как запрос отработает, **api-gateway** вернет ответ.


### Service Register

Для того что бы зарегестрировать сервис, вы должы

* Создать класс реализующий `rollun\Services\ApiGateway\Services\ServicesInterface`.

* Добавить сервис в плагинМенеджер `rollun\Services\ApiGateway\ServicesPluginManager`

    * Зарегестрировать сервис и фабрику для его создания

    * Добавить алиас на ваш сервис

Простейший пример Services класса

```php
<?php
class GoogleServices implements ServicesInterface
{

    /**
     * Generate string with service host
     * @return string
     */
    public function __toString()
    {
        return "google.com";
    }
}
```

Регистрируем его в `ServicesPluginManager`

```php
<?php
class ServicesPluginManager extends AbstractPluginManager
{
    protected $aliases = [
        "google" => GoogleServices::class,
    ];

    protected $factories = [
        GoogleServices::class => InvokableFactory::class
    ];
    //...
}
```

Тперь мы можем обращатся к сервису google - **google.gw.mototoyou.com**

## Проблемы с проксированием запросов типа multipart/form-data
По умолчаниб запросы c *Content-Type=multipart/form-data* не доступны в php как *raw body*
> [php:// - php.net](http://php.net/manual/ru/wrappers.php.php)

Для решения этой проблемы используется настройка apache, которам переопределяет тип заголовка 
*Content-Type* с *multipart/form-data* на *application/x-httpd-php*, а оригинальный тип кладет 
в заголовок *X-Real-Content-Type*. 
Так что для коректной работы *api-gateway* Вам необходимо добавить в .htaccess сделующие строки с правилами.
    
```apacheconfig
SetEnvIf Content-Type ^(multipart/form-data)(.*) MULTIPART_CTYPE=$1$2
RequestHeader set Content-Type application/x-httpd-php env=MULTIPART_CTYPE
RequestHeader set X-Real-Content-Type %{MULTIPART_CTYPE}e env=MULTIPART_CTYPE
```

> api-gateway при проксирование запроса, заменит заголовки на исходые, тем самым сервис к которому 
будет отправлен запрос, не заметим подмены.
