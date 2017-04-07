Symfony Flex Recipes
====================

`Symfony Flex`_ is the new way to manage dependencies in Symfony applications.
One of its main features is the automatic installation, configuration and
removal of dependencies. This automation is possible thanks to the **Symfony Flex
Recipes**.

Creating Flex Recipes
---------------------

Symfony Flex recipes consist of a ``manifest.json`` config file and, optionally,
any number of files and directories. Recipes must be stored on their own
repositories, outside of your Composer package repository.

All the contents of the ``manifest.json`` file are optional and they are divided
into options and configurators.

.. TODO: explain that recipe contents must be versioned

Options
-------

``aliases`` option
~~~~~~~~~~~~~~~~~~

The main configuration option is called ``aliases`` and its value is an array
that defines one or more alternative names that can be used to install the
dependency. For example, if a dependency is published as the ``acme-inc/acme-
log-monolog-handler``, it can define one or more aliases to make it easier to
install:

.. code-block:: json

    {
        aliases: ["acme-log", "acmelog"]
    }

Developers can now install this dependency as ``composer require acme-log``. For
security reasons, and to improve Symfony's developer experience, a long list of
aliases are reserved by Symfony Flex (``orm``, ``mailer``, ``loger``, ``admin``,
etc.) If you use those aliases in your recipe, they will be ignored.

``version_aliases`` option
~~~~~~~~~~~~~~~~~~~~~~~~~~

This option lists all the additional versions of the dependency that work with
this very same recipe (versions can use any of the syntaxes supported by
Composer). This avoids duplicating recipes when a new version of the package is
released:

.. code-block:: json

    {
        "version_aliases": ["3.3", "3.4", "4.0-dev"]
    }

Configurators
-------------

Recipes define the different tasks executed when installing a dependency, such
as running commands, copying files or adding new environment variables. Recipes
only contain the tasks needed to install and configure the dependency because
Symfony Flex is smart enough to reverse those tasks when uninstalling and
unconfiguring the dependencies.

Symfony Flex provides eight types of tasks, which are called **configurators**:
``copy-from-recipe``, ``copy-from-package``, ``bundles``, ``env``, ``makefile``,
``composer-scripts``, ``gitignore``, and ``post-install-output``.

``bundles`` configurator
~~~~~~~~~~~~~~~~~~~~~~~~

Enables one or more bundles in the Symfony application by appending them to the
``bundles.php`` file. Its value is an associative array with the bundle
namespace as the keys and the array of environments where it must be enabled as
the value. Use ``all`` to enable the bundle in any existing environment:

.. code-block:: json

    {
        "bundles": {
            "Symfony\\Bundle\\DebugBundle\\DebugBundle": ["dev", "test"],
            "Symfony\\Bundle\\MonologBundle\\MonologBundle": ["all"]
        }
    }

The previous recipe is transformed by Symfony Flex into the following PHP code::

    // etc/bundles.php
    return [
        'Symfony\Bundle\DebugBundle\DebugBundle' => ['dev' => true, 'test' => true],
        'Symfony\Bundle\MonologBundle\MonologBundle' => ['all' => true],
    ];

``copy-from-package`` configurator
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Copies files or directories from the Composer package contents to the Symfony
application. It's defined as an associative array where the key is the original
file/directory and the value is the target file/directory.

This example copies the ``bin/check.php`` script of the package into the binary
directory of the application:

.. code-block:: json

    {
        "copy-from-package": {
            "bin/check.php": "%BIN_DIR%/check.php"
        }
    }

The ``%BIN_DIR%`` string is a special value that it's turned into the absolute
path of the binaries directory of the Symfony application. These are the special
variables available: ``BIN_DIR``, ``CONF_DIR``, ``ETC_DIR``, ``SRC_DIR`` and
``WEB_DIR``. You can also access to any variable defined in the ``extra`` section
of your ``composer.json`` file:

.. code-block:: json

    // composer.json
    {
        "...": "...",

        "extra": {
            "my-special-dir": "..."
        }
    }

Now you can use ``%MY_SPECIAL_DIR%`` in your Symfony Flex recipes.

``copy-from-recipe`` configurator
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

It's identical to ``copy-from-package`` but contents are copied from the recipe
itself instead of from the Composer package contents. It's useful to copy the
initial configuration of the dependency and even a simple initial structure of
files and directories:

.. code-block:: json

    "copy-from-recipe": {
        "etc/": "%ETC_DIR%/",
        "src/": "%SRC_DIR%/"
    }

``env`` configurator
~~~~~~~~~~~~~~~~~~~~

Adds the given list of environment variables to the ``.env`` and ``.env.dist``
files stored in the root of the Symfony project:

.. code-block:: json

    {
        "env": {
            "DB_HOST": "127.0.0.1",
            "DB_NAME": "symfony",
            "DB_USER": "root",
            "DB_PASSWORD": ""
        }
    }

Symfony Flex turns that recipe into the following content appended to the ``.env``
and ``.env.dist`` files:

.. code-block:: ini

    ###> your-recipe-name-here ###
    DB_HOST=127.0.0.1
    DB_NAME=symfony
    DB_USER=root
    DB_PASSWORD=
    ###< your-recipe-name-here ###

The ``###> your-recipe-name-here ###`` section separators are needed by
Symfony Flex to detect the contents added by this dependency in case you
uninstall it later. Don't remove or modify these separators.

``makefile`` configurator
~~~~~~~~~~~~~~~~~~~~~~~~~

Adds new tasks to the ``Makefile`` file stored in the root of the Symfony project.
The value is a simple array where each element is a new line (Symfony Flex adds
a ``PHP_EOL`` character after each line):

.. code-block:: json

    {
        "makefile": [
            "cache-clear:",
            "\t@test -f bin/console && bin/console cache:clear --no-warmup || rm -rf var/cache/*",
            ".PHONY: cache-clear",
        ]
    }

Similar to the ``env`` configurator, the contents are copied into the ``Makefile``
file and wrapped with section separators (``###> your-recipe-name-here ###``)
that must not be removed or modified.

``composer-scripts`` configurator
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Registers scripts in the ``auto-scripts`` section of the ``composer.json`` file
to execute them automatically when running ``composer install`` and ``composer update``.
The value is an associative array where the key is the script to execute (including
all its arguments and options) and the value is the type of script (``php-script``
for PHP scripts, ``script`` for Makefile tasks and ``symfony-cmd`` for Symfony
commands):

.. code-block:: json

    {
        "composer-scripts": {
            "vendor/bin/security-checker security:check": "php-script",
            "make cache-warmup": "script",
            "assets:install --symlink --relative %WEB_DIR%": "symfony-cmd"
        }
    }

``gitignore`` configurator
~~~~~~~~~~~~~~~~~~~~~~~~~~

Adds patterns to the ``.gitignore`` file of the Symfony project. Define those
patterns as a simple array of strings (Symfony Flex adds a ``PHP_EOL`` character
after each line):

.. code-block:: json

    {
        "gitignore": [
            ".env",
            "/var/",
            "/vendor/",
            "/web/bundles/"
        ]
    }

Similar to other configurators, the contents are copied into the ``.gitignore``
file and wrapped with section separators (``###> your-recipe-name-here ###``)
that must not be removed or modified.

``post-install-output`` configurator
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Displays contents in the command console after the package has been installed.
Avoid outputting meaningless information and use it only when you need to show
help messages or the next step actions.

The contents are defined as a simple array of strings (Symfony Flex adds a
``PHP_EOL`` character after each line). `Symfony Console styles and colors`_
are supported too:

.. code-block:: json

    {
        "post-install-output": [
            "<fg=blue> What's next? </>",
            "",
            "  * <fg=blue>Run</> your application:",
            "    1. Execute the <comment>make serve</comment> command;",
            "    2. Browse to the <comment>http://localhost:8000/</comment> URL.",
            "",
            "  * <fg=blue>Read</> the documentation at <comment>https://symfony.com/doc</comment>"
        ]
    }

Full Example
------------

Combining all the above configurators you can define powerful recipes, like the
one used by ``symfony/framework-bundle``:

.. code-block:: json

    {
        "bundles": {
            "Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle": ["all"]
        },
        "copy-from-recipe": {
            "etc/": "%ETC_DIR%/",
            "src/": "%SRC_DIR%/",
            "web/": "%WEB_DIR%/"
        },
        "composer-scripts": {
            "make cache-warmup": "script",
            "assets:install --symlink --relative %WEB_DIR%": "symfony-cmd"
        },
        "env": {
            "APP_ENV": "dev",
            "APP_DEBUG": "1",
            "APP_SECRET": "Ju$tChang3it!"
        },
        "makefile": [
            "cache-clear:",
            "\t@test -f bin/console && bin/console cache:clear --no-warmup || rm -rf var/cache/*",
            ".PHONY: cache-clear",
            "",
            "cache-warmup: cache-clear",
            "\t@test -f bin/console && bin/console cache:warmup || echo \"cannot warmup the cache (needs symfony/console)\"",
            ".PHONY: cache-warmup",
            "",
            "serve:",
            "\t@echo \"\\033[32;49mServer listening on http://127.0.0.1:8000\\033[39m\"",
            "\t@echo \"Quit the server with CTRL-C.\"",
            "\t@echo \"Run \\033[32mcomposer require symfony/web-server-bundle\\033[39m for a better web server\"",
            "\tphp -S 127.0.0.1:8000 -t web",
            ".PHONY: serve"
        ],
        "gitignore": [
            ".env",
            "/var/",
            "/vendor/",
            "/web/bundles/"
        ],
        "post-install-output": [
            "<bg=blue;fg=white>              </>",
            "<bg=blue;fg=white> What's next? </>",
            "<bg=blue;fg=white>              </>",
            "",
            "  * <fg=blue>Run</> your application:",
            "    1. Execute the <comment>make serve</comment> command;",
            "    2. Browse to the <comment>http://localhost:8000/</comment> URL.",
            "",
            "  * <fg=blue>Read</> the documentation at <comment>https://symfony.com/doc</comment>"
        ]
    }

.. _`Symfony Flex`: https://github.com/fabpot/flex
.. _`Symfony Console styles and colors`: https://symfony.com/doc/current/console/coloring.html
