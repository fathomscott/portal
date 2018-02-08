Fathom Realty Subscription System
========================

# Installation

Please be aware that you should create your own `app/config/parameters.yml` when this file does not exist.  Please copy `app/config/parameters.yml.dist` and modify it.

To install the application and its dependencies, download composer and then run `composer.phar install`

# Install bower dependencies

    bower install

# Reload database and fixtures

```bash
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load
```

# Test Credit Cards
To test credit card processor integration, please follow the instructions located here:

https://www.helcim.com/support/article/54-converge-formerly-virtualmerchant-test-credit-card-numbers-and-generating-errors/

# Commands:
To bill all renewable subscriptions:
(on production server this command should run daily)

    php bin/console bill:subscription

To bill fixed MLS dues and create pending transactions for variable MLS dues:
(on production server this command should be run every month on the 15th)

    php bin/console bill:mls:dues

To re-bill previously generated and unpaid fixed MLS dues pending transactions:
(on production server this command should be run every month on the 16th, 17th, 18th, 19th, 20th)

    php bin/console rebill:mls:dues

To load agents from %agent_files_data_dir%:

    php bin/console migration:agent-load

To send notification emails to agents about their expired documents:

    php bin/console agent:expiring-documents
