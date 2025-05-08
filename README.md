# Currency API

REST API для получения курсов валют к рублю на основе данных Центробанка РФ.

## Технологии

- **Framework**: Yii2 (PHP)
- **База данных**: MySQL 5.7+
- **Источник данных**: [ЦБ РФ](http://www.cbr.ru/scripts/XML_daily.asp)

## Установка

### Требования
- PHP 7.4+
- MySQL 5.7+
- Composer

### Пошаговая установка

***1. Клонирование репозитория:***

```bash
git clone https://github.com/danielgreen229/currency-api.git
cd currency-api
```

***2. Установка зависимостей:***

```
composer install
```

***3. Настройка БД:***

Запустите MySQL сервер.

Зайдите в консоль MySQL и создайте БД currency:

```
-- Создание базы данных
CREATE DATABASE currency CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Настройте подключение в config/db.php:

```
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=currency',
    'username' => 'root',
    'password' => '', // Укажите ваш пароль
    'charset' => 'utf8mb4',
    
    // Дополнительные настройки для продакшна
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
```

***4. Применение миграций***

Выполните команду для создания таблиц:

```
bash
php yii migrate/up
Эта команда создаст следующие таблицы:

currency_rates - хранение курсов валют

migration - служебная таблица для учета выполненных миграций
```


***5. Консольные команды***

Обновление курсов валют

```bash
php yii update-currency-rates/run
/*
Описание:
Загружает актуальные курсы с сайта ЦБ РФ
Сохраняет в базу данных
Обновляет курсы для текущей даты
*/
```

***6. Настройка окружения***
Для разработки:
```
bash
php yii serve
```
Для продакшна:

Настройте веб-сервер (Apache/Nginx) на работу с web/index.php


***API Endpoints***


Получение курса валюты
URL: GET currency/api/rate/{code}[/{date}]

Параметры:

Параметр    Тип     Обязательный    Описание
code        string  Да              3-символьный код валюты
date        string  Нет             Дата в формате YYYY-MM-DD


Примеры:

# Текущий курс
curl http://localhost:8080/currency/api/rate/USD

# Курс на конкретную дату
curl http://localhost:8080/currency/api/rate/USD/2025-05-08

# Формат ответа:
```
{
    "code": "USD",
    "rate": "75.1234",
    "date": "2025-05-08"
}
```

Структура проекта
config/
  console.php    # Конфигурация консольного приложения
  db.php         # Настройки базы данных
  web.php        # Основная конфигурация
controllers/
  CurrencyController.php  # Обработчик API запросов
commands/
  UpdateCurrencyRatesCommand.php  # Команда обновления курсов
migrations/      # Миграции базы данных
models/          # Модели данных



## 📜 Лицензия

MIT © [Ваше Имя](https://github.com/danielgreen229)

---

⭐ Если проект вам понравился, не забудьте поставить звезду!






