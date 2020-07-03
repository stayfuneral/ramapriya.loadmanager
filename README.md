# LoadManager

Модуль для автозагрузки классов в Битрикс

### Установка

1. Клонируйте репозиторий в папку `/local/modules`
2. Установите модуль в админке в разделе `Маркетплейс > Установленные решения`


### Использование

Создайте папку, в которую будете складывать новые классы, например, `/local/php_interface/lib`

Чтобы не засорять init.php, можно создать специальный файл в созданной папке, в котором будет вызываться автозагрузчик, и уже этот файл подключать в init.php

```php

// /local/php_interface/lib/include.php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Ramapriya\LoadManager\Autoload;

// подключение модуля
Loader::includeModule('ramapriya.loadmanager');

$defaultNamespace = 'Ramapriya';
$excludeFiles = ['include.php']; // Файлы, которые не нужно добавлять в автозагрузчик

$libDir = Application::getDocumentRoot() . '/local/php_interface/lib';
$autoloadClasses = Autoload::setAutoloadClassesArray($libDir, $defaultNamespace, $excludeFiles);

Autoload::loadClasses($autoloadClasses);
```

Далее остаётся только подключить данный файл в init.php:

```php
// init.php

$includeFile = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/lib/include.php';

if(file_exists($includeFile)) {
    require_once $includeFile;
}
```

[Подробный разбор модуля на Хабре](https://habr.com/ru/post/509474/)

