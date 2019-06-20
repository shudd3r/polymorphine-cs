# Code standards check & fix scripts for Polymorphine libraries

Combination of [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
and [CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) with custom
settings added as dev dependency to Polymorphine packages.

### CI integration
[`cs-fixer.php.dist`](cs-fixer.php.dist) file added to root package directory
will setup PHP-CS-Fixer factory to use package name and set its working directory.

Dry run (without fixes) with Composer script configured in `composer.json`:

    "scripts": {
        "style-check": "polymorphine-cs"
    }

Perform CI checks using `.travis.yml` config. To check both `src/` and `tests/`
directories (and their subdirectories) call script twice:

    script:
      - composer style-check src
      - composer style-check tests

You can specify concrete files after selecting directory. Listed files located
outside directory will be ignored. This way you can tell Travis to check only
recently changed files:

    before_script:
      - CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB $TRAVIS_COMMIT_RANGE)
    script:
      - composer style-check src ${CHANGED_FILES}
      - composer style-check tests/phpunit ${CHANGED_FILES}

Style checking will be skipped if none of `CHANGED_FILES` is located within
`src` or `tests/phpunit` directory. Command below will check only `UsernameValidation.php` file:

    composer style-check src/Validation README.md src/Validation/UsernameValidation.php src/app/Config.php 

### IDE Setup
##### PHP-CS-Fixer
**PHPStorm**'s external tools are good way to run checker/fixer:
- `Program:` add path to `vendor/bin/php-cs-fixer` or `php-cs-fixer.bat` for windows
- `Parameters:`
    - for checking command:

            fix -v --config=cs-fixer.php.dist --dry-run --using-cache=no --path-mode=intersection "$FileDir$\$FileName$"

    - for fixing command:
     
            fix -v --config=cs-fixer.php.dist --using-cache=no --path-mode=intersection "$FileDir$\$FileName$"
   
- `Working directory:` set to `$ProjectFileDir$`
- Add keyboard shortcuts to run commands in `Settings > Keymap > Extarnal Tool`

##### Code Sniffer
Script also performs checks for variable names and line length,
but uses `Code Sniffer` because `PHP-CS-Fixer` will not check
something it cannot fix.

- Add path to local `phpcs` script in `Settings > Languages & Frameworks > PHP > Code Sniffer`
- Set custom ruleset in `Settings > Editor > Inspections > PHP Code Sniffer validation`
and set path to [`phpcs.xml.dist`](phpcs.xml.dist) file provided with this package.
