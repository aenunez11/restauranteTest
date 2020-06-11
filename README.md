# Symfony4 App #
------------------

## Setup Project ##

**0. Create Schema:** Mandatory if you clone new project, or you are developing with a new database:

```bash
 php bin/console doctrine:schema:create
```

**1. Check database integrity:** Mandatory if you pull code or if you are changing mappings or database: 

```bash
 php bin/console doctrine:schema:validate
```

**2. Compile SCSS & JS with Yarn:** The first execution and everytime that you want to change anything in the **assets** folder (styles or JS) you must recompile all the code using: 

```bash
 yarn run dev
```

**3. If any entity is modified** Run: 

```bash
php bin/console make:entity --regenerate Domain
``` 

## Configuration Notes ##

- Environment specific variables (db connections, etc.) are placed in ```/.env```

- Doctrine mappings must be placed in ```config/doctrine/```

- WebPack assets configuration is placed in ```webpack.config.js``` 
