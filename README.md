# Usage
**This plugin to receive alert signals from Trandingview**

![Alt text](https://github.com/dearvn/tradingview-alerts/raw/main/alerts.png?raw=true "alerts")


**Setup Alert Webhook from TradingView**

![Alt text](https://github.com/dearvn/tradingview-alerts/raw/main/alert.png?raw=true "alerts")


# Wordpress and React
A simple plugin to work in WordPress with WP-script, React, React Router, Tailwind CSS, PostCSS, Eslint, i18n, PHP OOP plugin architecture easily in a minute.

----

### Quick Start
```sh
# Clone the Git repository
git clone https://github.com/dearvn/tradingview-alerts.git

# Install node module packages
npm i

# Install PHP-composer dependencies [It's empty]
composer install

# Start development mode
npm start

# Start development with hot reload (Frontend components will be updated automatically if any changes are made)
npm run start:hot

# To run in production
npm run build
```

After running `start`, or `build` command, there will be a folder called `/build` will be generated at the root directory.

### Browse Plugin
**using https://github.com/dearvn/wp-deployment to deploy in local enviroment

http://wordpress.local:8080/wp/wp-admin/admin.php?page=tradingview_alerts#/

Where, `/wpex` is the project root folder inside `/htdocs`.

Or, it could be your custom processed URL.

### Version & Changelogs
**v0.0.1 - 07/09/2022**

1. Necessary traits to handle - sanitization, query.
1. Advanced setup for migration, seeder, REST API.
1. Alerts REST API developed.

### PHP Coding Standards - PHPCS

**Get all errors of the project:**
```sh
vendor/bin/phpcs .
```

**Fix all errors of the project:**
```sh
vendor/bin/phpcbf .
```

<details>
    <summary>Options for specific files:</summary>

**Get specific file errors of the project:**
```sh
vendor/bin/phpcs tradingview-alerts.php
```


**Fix specific file errors of the project:**
```sh
vendor/bin/phpcbf tradingview-alerts.php
```
</details>

### Setup webhook alert
***Url Webhook**
```https://[domain]/wp-json/tradingview-alerts/v1/alerts```

**CALL**
```sh

{
    "name":"CALL",
    "type": "Buy Long",
    "close": "{{close}}",
    "interval": "{{interval}}",
    "exchange": "{{exchange}}",
    "ticker": "{{ticker}}",
    "time": "{{time}}",
    "timenow": "{{timenow}}"
}

```

**Exit CALL**
```sh

{
    "name":"EXIT CALL",
    "type": "Exit Buy Long",
    "close": "{{close}}",
    "interval": "{{interval}}",
    "exchange": "{{exchange}}",
    "ticker": "{{ticker}}",
    "time": "{{time}}",
    "timenow": "{{timenow}}"
}
```

**PUT**
```sh

{
    "name":"PUT",
    "type": "Sell Short",
    "close": "{{close}}",
    "interval": "{{interval}}",
    "exchange": "{{exchange}}",
    "ticker": "{{ticker}}",
    "time": "{{time}}",
    "timenow": "{{timenow}}"
}

```

**Exit PUT**
```sh

{
    "name":"EXIT PUT",
    "type": "Exit Sell Short",
    "close": "{{close}}",
    "interval": "{{interval}}",
    "exchange": "{{exchange}}",
    "ticker": "{{ticker}}",
    "time": "{{time}}",
    "timenow": "{{timenow}}"
}
```


### Check coding wordpress standar
<details>
    <summary>Fixing errors for input data</summary>

https://github.com/WordPress/WordPress-Coding-Standards/wiki/Fixing-errors-for-input-data#nonces
</details>

<details>
    <summary>Yoda Conditions: To Yoda or Not to Yoda</summary>

https://knowthecode.io/yoda-conditions-yoda-not-yoda
</details>


