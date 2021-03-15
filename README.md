# Тест на трудоустройство в компанию **TrueConf** с открытой публикацией кода

Использовались при выполнении:

1. XAMPP PHP 7.4.5
2. Slim 4 Framework (4.*)
3. Insomnia Core 2021.1.1 (Тестирование и отладка API)
4. Visual Studio Code 1.45.1 (редактор\IDE)

*Комментарии в коде на английском языке

----

## Текст теста

Вам нужно написать маленькое приложение на Slim Framework предоставляющие REST API по работе с сущностью User.

REST API должно удовлетворять следующие возможности:

- Добавление User
- Получение списка User
- Получение User по Id
- Редактирование User по Id
- Удаление User по Id

REST API должно работать с форматом данных JSON.

Сущность User должно состоять минимум из следующих полей:

- Идентификатор пользователя
- Отображаемое имя

Вы можете использовать дополнительные поля, если считаете нужным.

В качестве хранилища данных нужно использовать файл в формате JSON.

----

## Пример выполнения

Приложение написано с учетом особенностей **Slim 4 Framework** с применением ООП практик с учетом возможного внедрения других API сервисов и также внедрения других методов в рамках этих API на ООП основе. Возможно легкое подключение новых API сервисов, а также методов уже в рамках существующих API.

Имплементация в виде ООП выбрана для более гибкой обработки запроса и реализации логики API. В дополнительных методах класса возможна реализация дополнительных комплексных алгоритмов в возможных новых методах API, использование абстрактного класса для связки позволяет стандартизировать корневой метод для API метода.

Приложение полностью портабельно. Возможен перенос без папки **vendor**, с последующей установкой зависимостей. Стандартная ситуация при использовании **Composer**. Первичный **commit** выполнен с папкой **vendor** в случае использования в **development** среде без **Composer**-а.

**Request** (запрос) к API поступает с **header** `Content-Type: application/json` и содержит два **core** поля: `method` и `data`. О проверке всех возможных вариантов возникающих при запросе Вы можете прочитать ниже.

**Response** (ответ) от API реализован в формате `application/json` с применением соответствующих **HTTP Status Codes** и error data в теле ответа в случае ошибки. Примеры Вы можете найти в тексте ниже.

В тесте не указана необходимость аутентификации, но была имплементирована **Basic HTTP** аутентификация с обязательным использованием **HTTPS** и порта **443** ( что проверяется в **middleware**).

В таблице ниже указаны несколько пар **login/password** для **Basic HTTP** аутентификации. Сами пароли хранятся также как и основной файл данных задания в **JSON** файле в **bcrypt hash** формате (подробнее о файле и имплементации чтения и записи **JSON** файлов ниже по тексту).

Пример **hash**-а:

`$2y$10$9AjDOfxZ.EV0OS3dVY3QhuXq1n2OMf6AZsPVSsNQjkRD9o15e1ULa`

Login | Password
------------- | -------------
SomeTestLogin | d7g5d9sg5s
AnotherTestLogin | d6w70fdj680vk
OneMoreTestLogin | 7fpjd74jvf9

API еndpoint адрес на **production**:

<https://project.com/api/users.api>

В **development** среде использовался подкаталог и подпапка **public**:

<https://project.com/trueConfEmploymentTest/public/api/users.api>

Предпроверка запроса на соответствие протоколу **https** и порту **443** реализована при помощи **middleware** класса который подключается к обработке запроса стандартными route настройками и не входит в наследование имплементирующих API классов.

Имплементированные методы и их английские названия.

Русский | English
------------- | -------------
Добавление User | AddUser
Получение списка User | GetUsersList
Получение User по Id | GetUserById
Редактирование User по Id | EditUserById
Удаление User по Id | DeleteUserById

После **middleware** запрос проходит следующую структуру классов в которой проходит валидация и проверка структуры запроса согласно логике данного API (подробно она будет описана ниже по тексту).
![alt](https://i.imgur.com/I7TAUpj.png)

Полная, развернутая структура папок выглядит следующим образом:
![alt](https://i.imgur.com/bXYfYHJ.png)

Первоначальная установка Slim 4 Framework состояла только из корневой папки и файлов:

**vendor\\** - зависимые компоненты (содержимое не редактировалось)

**composer.json** - файл конфигурации Composer

Добавлен autoloader для namespace папки App (корневой папки приложения)

```json
"autoload": {
 "psr-4": {
 "App\\": "App"
 }
}
```

**composer.lock** - информация о версиях компонентов Composer

----

### Краткое описание добавленных файлов

----

#### README.md

Данный файл справки в формате **markdown**

----

#### .htaccess

Файл настройки **Apache** сервера. Включен рероутинг на файл **Public\index.php** для всего пространства адресов **/public/** без указания директивы `<ifModule mod_rewrite.c>` для выдачи ошибки сервером при отсутствии модуля. Также установлен параметр удаления **header** информации **X-Powered-By** в ответе сервера.

----

#### Public\index.php

**Entrypoint** приложения. Оставлен демо роут. Роутинг для API эндпойтов вынесен группой в отдельное пространство адресов **/api**. указан адрес и входной класс. Для API выбран только **POST** метод для более гибкой настройки возможных внутренних методов API. Также установлено route имя для возможных быстрых ссылок.

Нахождение **entrypoint**-а приложения в подпапке обусловлено возможностью добавления новых публичных данных в приложение если это будет необходимо.

```php
$app->group('/api', function (RouteCollectorProxy $group) {
 
 $group
 ->post('/users.api', 'App\Controller\Api\Users\Entry')
 ->add('App\Middleware\Api\JsonHttps')
 ->setName('UsersApi');
 
});
```

----

#### App\Config.php

Настройки приложения в виде массива (**array**) и класс для его установки и получения значений через **dot** нотацию через метод. Получение значений имплементировано простой навигацией по ключам массива (**array**).

Пример получения параметра: `Config::get('database.json.path');`

Файл конфигурации так же содержит настройки для именного API. Такие как обязательные поля и имплементированные и тестовые методы.

----

#### App\Model\Api\JsonDb.php

Класс для взаимодействия с **JSON** файлами. Реализованы методы аутентификации для указанного файла, чтения и записи данных.

----

#### App\Middleware\Api\JsonHttps.php

**Middleware** класс для валидации запроса к API. Проверяет injected **request** объект на соответствие стандартам API. В случае несоответствия запроса заданным критериям выдается соответствующая ошибка в **response** теле. Ниже на скриншотах приведены примеры.

Запрос не по защищенному соединению **HTTPS** :
![alt](https://i.imgur.com/4TfXcoB.png)

Запрос не содержит единственный header **Content-Type: application/json**.
![alt](https://i.imgur.com/SK8WiuD.png)

Запрос не содержит данные для **Basic HTTP** аутентификации или данные не заполнены.
![alt](https://i.imgur.com/4xoPnWy.png)

В каждом из случаев отправляется соответствующий **HTTP Status Code** который отображен так же в теле сообщения в поле **code**. В имплементациях расширенных API данное поле в теле сообщения можно использовать для кастомных номерных статусов сообщений API.
![alt](https://i.imgur.com/wZcNBWA.png)

----

#### App\Database\Json\Users.json

Файл данных пользователей. Формат рассчитан на то что даже ключи будут нести полезное значение, поэтому не использовался неименной массив. Идентификатор пользователя (ID) является ключем ко всем остальным данным пользователя.

![alt](https://i.imgur.com/rWuA6Pk.png)

К указанному в тексте теста полю "Отображаемое имя" (`name`) добавлено поле "Компания" (`company`).

Список полей (текущие + будущие) по которым API проверяет соответствие формата запроса содержится в конфигурационном файле **App\Config.php** в настройках именнованного API и вызывается следующей командой: `Config::get('api.json.Users.fields');`.

![alt](https://i.imgur.com/OYAQaK8.png)

----

#### App\Database\Json\Auth.json

В файле содержаться логины и пароли пользователей для доступа к конкретной базе данных, в нашем случае **Users**. Используеться классом **App\Model\Api\JsonDb**. Пароли хранятся в зашифрованном **hash** алгоритмом **bcrypt** виде.

![alt](https://i.imgur.com/PDoFA8s.png)

----

#### App\Controller\Msg\Error.php

Трейт для вывода кастомной информации о возникшей ошибке. Реализован метод `json()` для вывода сообщения в `application/json` формате.

Пример использования:

```php
Error::Json(400, 'MsgSender', 'Message body');
```

----

#### App\Controller\Api\Users\Entry.php

Центральный файл имплементации API. После проверки **middleware** классом запрос в данном классе, перед запуском соответствующего метода, проверяется на следующие моменты:

Валидные аутентификационные данные (**login/password**). Возвращает ошибку при неудаче аутентификации.
![alt](https://i.imgur.com/nM5Y0CW.png)

Корректный формат оформления **json** тела запроса, с выдачей соответствующего номера ошибки при его неправильном оформлении.
![alt](https://i.imgur.com/JCqoCeU.png)

Проверка на наличие двух **core** полей запроса `method` и `data`.
![alt](https://i.imgur.com/NwvWwHn.png)
![alt](https://i.imgur.com/njFFTpQ.png)

Проверка запроса на разрешенный API метод из поля `method` список которых указывается в конфигурационном файле.
![alt](https://i.imgur.com/WdM0bqh.png)

А также получение данных и запуск соответствующего метода API через метод абстрактного класса.

----

#### App\Controller\Api\Users\Methods.php

Абстрактный класс который наследует класс **App\Controller\Api\Users\Entry** и содержит статический метод который позволяет запускать соответствующие методы API, которые наследуя данный класс содержат имплементацию абстрактного метода данного класса. В передаче данных задействована **dependency injection** практика в ввиду особенностей Slim 4 Framework.

----

#### App\Controller\Api\Users\Methods\AddUser.php

Метод API **AddUser** (Добавление User).

Проверяет входящие поля запроса на валидность имен полей из конфигурационных данных API в **core** поле `data`. Обязательные к присутствию в запросе поля отмечены в конфигурационном файле. В случае ошибки выдается соответствующая информация.

![alt](https://i.imgur.com/5Vw4lqI.png)

Отсутствует **required** поле `name`:
![alt](https://i.imgur.com/6hfx3cI.png)

Присутствует поле которого нет в списке разрешенных:
![alt](https://i.imgur.com/OLtvtij.png)

Пример удачного запроса со всеми возможными полями, хотя поле `company` может отсутствовать, так как является необязательным:

```json
{
 "method": "AddUser",
 "data": {
  "name": "Jennifer Vellington",
  "company": "Stellar Inc."
 }
}
```

При выполнении возвращается присвоенный **ID** пользователя и присвоенные поля с их значением:
![alt](https://i.imgur.com/Bhjx9GK.png)

Метод может быть изменен на возвращение только **ID** при большом количестве полей:
![alt](https://i.imgur.com/dHiSBof.png)

----

#### App\Controller\Api\Users\Methods\GetUsersList.php

Метод API **GetUsersList** (Получение списка User).

Возвращает список всех пользователей внесенных в **json** файл. В **core** поле `data` возможно указание директивы метода (фильтры по компаниям например), на данный момент по тексту задания реализована директива **All**, получение всех пользователей списка.

Ошибка указания директивы метода:
![alt](https://i.imgur.com/ivRE1P5.png)

Пример удачного запроса к методу:

```json
{
 "method": "GetUsersList",
 "data": "All"
}
```

Успешный запрос возвращает массив данных **json** в `core` поле `data`:
![alt](https://i.imgur.com/Hnn4HFs.png)

----

#### App\Controller\Api\Users\Methods\GetUserById.php

Метод API **GetUserById** (Получение User по Id).

Получает пользователя по присвоенному **ID** указанному в `core` поле `data` в виде типа данных **integer**. При отсутствии пользователя с указанным **ID** отправляет ответ с пустыми данными **ID** (так как это не ошибка запроса).

Ошибка указания значения **ID** пользователя в виде **integer**:
![alt](https://i.imgur.com/DuWqCRn.png)

Сообщение при ненайденном пользователе:
![alt](https://i.imgur.com/ykKZKpD.png)

Пример удачного запроса к методу:

```json
{
 "method": "GetUserById",
 "data": 13
}
```

Скриншот ответа (**ID** и поля данных пользователя):
![alt](https://i.imgur.com/hTlBNg4.png)

----

#### App\Controller\Api\Users\Methods\EditUserById.php

Метод API **EditUserById** (Редактирование User по Id).

Получает данные пользователя и проверяет по следующим параметрам перед записью новых данных:

Проверяет существование в полученных данных **ID** пользователя для редактирования его данных и тип указанного **ID** (должен быть **integer**).

Возможно редактирование любого поля которое внесено в список конфигурационного файла в разделе настроек соответствующего **API**, даже если поле не было добавлено при первоначальном внесении пользователя, оно будет добавлено при его редактировании.

Пример ответа при отсутствии **ID**:
![alt](https://i.imgur.com/HNsqAY9.png)

Пример ответа при указании **ID** как **integer**:
![alt](https://i.imgur.com/oo8yJF7.png)

Пример ответа при указании не внесенного в конфигурацию **API** поля пользователя:
![alt](https://i.imgur.com/vLU6ENE.png)

Пример удачного запроса, при котором возвращается информация о измененных полях в `old` и `new` разделе:

```json
{
 "method": "EditUserById",
 "data": {
  "id": 13,
  "name": "Mariam Shepard",
  "company": "Commander Inc."
 }
}
```

![alt](https://i.imgur.com/G0i5wxI.png)

----

#### App\Controller\Api\Users\Methods\DeleteUserById.php

Метод API **DeleteUserById** (Удаление User по Id).

Проверяет и получает поле со значением **ID** пользователя, проверяет его на соответствие типу **integer** и отправляет ответ с удаленной информацией (в случае ненайденного по **ID** пользователя отправляет пустой позитивный ответ, так как это не ошибка).

Пример запроса с указанием **ID** не как **integer** тип:
![alt](https://i.imgur.com/FXgG0hG.png)

Пример запроса с ненайденным **ID** пользователя:
![alt](https://i.imgur.com/BGRLqmD.png)

Пример удачного запроса к методу:

```json
{
 "method": "DeleteUserById",
 "data": 13
}
```

![alt](https://i.imgur.com/MsDoP4v.png)

----

## Завершение файла README.md

Спасибо Вам за предоставленную возможность пройти тестирование.

Буду рад получить от Вас приглашение на собеседование.

С уважением, Владимир Горбачев.