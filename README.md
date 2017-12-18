## Порядок установки

### Клонирование репозитория

    git clone git@gitlab.abr-daemon.ru:abr_git/pinguem-bot.git public_html

### Установка Yii2

    composer create-project --prefer-dist --stability=dev yiisoft/yii2-app-basic

### Установка дополнительных библиотек

    composer require longman/telegram-bot
    composer require stolt/json-lines
    composer require --prefer-dist yiisoft/yii2-sphinx

### Настройка MySQL

login, passsword, db: pinguem-bot  
Если у вас другие параметры, изменяем их в config/db.php

### Мигрируем

    ./yii migrate/up

yes -> Enter

### Заливаем дамп базы

    mysql -u pinguem-bot -ppinguem-bot pinguem.bot < pinguem.bot.sql

### Устанавливаем telegram-cli (гуглите)

### Настраиваем сфинкс, устанавливаем cron на скрипты (check-sphinx.sh as root, tg-dump.sh as www-data)
