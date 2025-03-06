<?php

declare(strict_types=1);


require 'vendor/autoload.php';

$parsedown = new Parsedown();

$markdown = <<<EOT
Ось перелік команд Git для виконання завдань, які ти описав:

### 1. **Проаналізувати різницю в гілках dev/stage/prod**
```bash
# Подивитися різницю між develop і stage
git diff develop stage

# Подивитися різницю між stage і prod
git diff stage prod

# Подивитися різницю між develop і prod
git diff develop prod

# Подивитися останні коміти, які є в одній гілці, але відсутні в іншій
git log develop..stage --oneline
git log stage..prod --oneline
git log develop..prod --oneline
```

### 2. **Створити список ділянок коду, що мають бути різними в залежності від оточення**
```bash
# Знайти всі файли, що відрізняються між гілками
git diff --name-only develop stage
git diff --name-only stage prod
git diff --name-only develop prod

# Перевірити змінні середовища та конфігураційні файли
git diff develop stage -- config/
git diff stage prod -- config/
git diff develop prod -- config/
```
> **Далі потрібно проаналізувати, які з цих файлів варто винести в `.env` або зробити через `if(env(...))` у коді.**

### 3. **Обгорнути змінний код умовами**
- Якщо використовується Laravel, то варто винести різний код у `.env` та використовувати `env()`:
```php
if (env('APP_ENV') === 'production') {
    // Код для продакшну
} else {
    // Код для стейджу або девелопу
}
```
- Якщо це Docker, то варто винести змінні в `docker-compose.override.yml` для різних середовищ.

### 4. **Консультація з девопсами щодо перестворення гілок stage/develop від prod**
```bash
# Створення нових stage і develop на основі prod
git checkout prod
git checkout -b new-develop
git checkout -b new-stage

# (Обговорити, як зберегти потрібні зміни у stage/develop перед їх перестворенням)
```

### 5. **Вирішити, коли найкраще видаляти гілки**
- Найкращий момент для видалення гілок — **після завершення основного циклу розробки**, коли всі зміни з `develop` та `stage` вже **змерджено у `prod`**.

```bash
# Перевірити, чи всі зміни потрапили в продакшн
git log develop..prod --oneline
git log stage..prod --oneline

# Якщо всі зміни є в продакшні, можна видаляти гілки:
git branch -D develop  # Локально
git push origin --delete develop  # Віддалено
git branch -D stage
git push origin --delete stage
```

---

## **Результати, які ми отримаємо**
✅ **Ліквідуємо cherry-picks** — більше не буде потреби вручну перетягувати зміни між гілками.  
✅ **Ліквідуємо постійні конфлікти** — код буде однаковий у всіх середовищах, крім специфічних налаштувань.  
✅ **Ліквідуємо помилки через відмінності оточень** — конфігурація буде винесена у змінні середовища.  

Якщо ще щось потрібно уточнити чи доповнити — запитуй! 🚀


EOT;

$html = $parsedown->text($markdown);

echo $html . PHP_EOL;
