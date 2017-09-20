Symfony Recipes
===============

Symfony recipes allow the automation of Composer packages configuration via the
`Symfony Flex`_ Composer plugin.

This repository contains "official" recipes for Composer packages endorsed by
the Symfony Core Team. For contributed recipes, see the `contrib repository`_.

Creating Recipes
----------------

Symfony recipes consist of a ``manifest.json`` config file and, optionally, any
number of files and directories. Recipes must be stored on their own
repositories, outside of your Composer package repository. They must follow the
``vendor/package/version/`` directory structure, where ``version`` is the
minimum version supported by the recipe.

The following example shows the real directory structure of some Symfony recipes::

    symfony/
        console/
            3.3/
                bin/
                manifest.json
        framework-bundle/
            3.3/
                config/
                public/
                src/
                manifest.json
        requirements-checker/
            1.0/
                manifest.json

All the ``manifest.json`` file contents are optional and they are divided into
options and configurators.

.. note::

    Don't create a recipe for Symfony bundles if the only configuration in the
    manifest is the registration of the bundle for all environments, as this is
    done automatically.

.. note::

    When creating a recipe, don't create bundle config files under
    ``config/packages/`` when no options are set.

Options
-------

``aliases`` option
~~~~~~~~~~~~~~~~~~

This option defines one or more alternative names that can be used to install
the dependency. Its value is an array of strings. For example, if a dependency
is published as ``acme-inc/acme-log-monolog-handler``, it can define one or
more aliases to make it easier to install:

.. code-block:: json

    {
        "aliases": ["acme-log", "acmelog"]
    }

Developers can now install this dependency with ``composer require acme-log``.

Configurators
-------------

Recipes define the different tasks executed when installing a dependency, such
as running commands, copying files or adding new environment variables. Recipes
only contain the tasks needed to install and configure the dependency because
Symfony is smart enough to reverse those tasks when uninstalling and
unconfiguring the dependencies.

There are eight types of tasks, which are called **configurators**:
``copy-from-recipe``, ``copy-from-package``, ``bundles``, ``env``,
``makefile``, ``composer-scripts``, ``gitignore``, and ``post-install-output``.

``bundles`` Configurator
~~~~~~~~~~~~~~~~~~~~~~~~

Enables one or more bundles in the Symfony application by appending them to the
``bundles.php`` file. Its value is an associative array where the key is the
bundle class name and the value is an array of environments where it must be
enabled. The supported environments are ``dev``, ``prod``, ``test`` and ``all``
(which enables the bundle in all environments):

.. code-block:: json

    {
        "bundles": {
            "Symfony\\Bundle\\DebugBundle\\DebugBundle": ["dev", "test"],
            "Symfony\\Bundle\\MonologBundle\\MonologBundle": ["all"]
        }
    }

The previous recipe is transformed into the following PHP code:

.. code-block:: php

    // config/bundles.php
    return [
        'Symfony\Bundle\DebugBundle\DebugBundle' => ['dev' => true, 'test' => true],
        'Symfony\Bundle\MonologBundle\MonologBundle' => ['all' => true],
    ];

``container`` Configurator
~~~~~~~~~~~~~~~~~~~~~~~~~~

Adds new container parameters in the ``services.yaml`` file by adding your
parameters in the ``container`` option.

This example creates a new ``locale`` container parameter with a default value
in your container:

.. code-block:: json

    {
        "container": {
            "locale": "en"
        }
    }

``copy-from-package`` Configurator
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
variables available: ``%BIN_DIR%``, ``%CONF_DIR%``, ``%CONFIG_DIR%``, ``%SRC_DIR%``
``%VAR_DIR%`` and ``%PUBLIC_DIR%``. You can also access to any variable defined in
the ``extra`` section of your ``composer.json`` file:

.. code-block:: json

    // composer.json
    {
        "...": "...",

        "extra": {
            "my-special-dir": "..."
        }
    }

Now you can use ``%MY_SPECIAL_DIR%`` in your recipes.

``copy-from-recipe`` Configurator
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

It's identical to ``copy-from-package`` but contents are copied from the recipe
itself instead of from the Composer package contents. It's useful to copy the
initial configuration of the dependency and even a simple initial structure of
files and directories:

.. code-block:: json

    "copy-from-recipe": {
        "config/": "%CONFIG_DIR%/",
        "src/": "%SRC_DIR%/"
    }

``env`` Configurator
~~~~~~~~~~~~~~~~~~~~

Adds the given list of environment variables to the ``.env`` and ``.env.dist``
files stored in the root of the Symfony project:

.. code-block:: json

    {
        "env": {
            "APP_ENV": "dev",
            "APP_DEBUG": "1"
        }
    }

This recipe is converted into the following content appended to the ``.env``
and ``.env.dist`` files:

.. code-block:: bash

    ###> your-recipe-name-here ###
    APP_ENV=dev
    APP_DEBUG=1
    ###< your-recipe-name-here ###

The ``###> your-recipe-name-here ###`` section separators are needed by Symfony
to detect the contents added by this dependency in case you uninstall it later.
Don't remove or modify these separators.

.. tip::

    Use ``%generate(secret)%`` as the value of any environment variable to
    replace it with a cryptographically secure random value of 16 bytes.

``makefile`` Configurator
~~~~~~~~~~~~~~~~~~~~~~~~~

Adds new tasks to the ``Makefile`` file stored in the root of the Symfony
project. Unlike other configurators, there is no specific entry in the manifest
file. Define tasks by creating a ``Makefile`` file at the root of the recipe
directory (a ``PHP_EOL`` character is added after each line).

Similar to the ``env`` configurator, the contents are copied into the ``Makefile``
file and wrapped with section separators (``###> your-recipe-name-here ###``)
that must not be removed or modified.

``composer-scripts`` Configurator
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Registers scripts in the ``auto-scripts`` section of the ``composer.json`` file
to execute them automatically when running ``composer install`` and ``composer
update``. The value is an associative array where the key is the script to
execute (including all its arguments and options) and the value is the type of
script (``php-script`` for PHP scripts, ``script`` for any shell script and
``symfony-cmd`` for Symfony commands):

.. code-block:: json

    {
        "composer-scripts": {
            "vendor/bin/security-checker security:check": "php-script",
            "make cache-warmup": "script",
            "assets:install --symlink --relative %PUBLIC_DIR%": "symfony-cmd"
        }
    }

``gitignore`` Configurator
~~~~~~~~~~~~~~~~~~~~~~~~~~

Adds patterns to the ``.gitignore`` file of the Symfony project. Define those
patterns as a simple array of strings (a ``PHP_EOL`` character is added after
each line):

.. code-block:: json

    {
        "gitignore": [
            ".env",
            "/public/bundles/",
            "/var/",
            "/vendor/"
        ]
    }

Similar to other configurators, the contents are copied into the ``.gitignore``
file and wrapped with section separators (``###> your-recipe-name-here ###``)
that must not be removed or modified.

``post-install-output`` Configurator
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Displays contents in the command console after the package has been installed.
Avoid outputting meaningless information and use it only when you need to show
help messages or the next step actions.

The contents must be defined in a file named ``post-install.txt`` (a
``PHP_EOL`` character is added after each line). `Symfony Console styles and
colors`_ are supported too:

.. code-block:: text

    <bg=blue;fg=white>              </>
    <bg=blue;fg=white> What's next? </>
    <bg=blue;fg=white>              </>

      * <fg=blue>Run</> your application:
        1. Change to the project directory
        2. Execute the <comment>make serve</> command;
        3. Browse to the <comment>http://localhost:8000/</> URL.

      * <fg=blue>Read</> the documentation at <comment>https://symfony.com/doc</>

Validation
----------

When submitting a recipe, several checks are automatically executed to validate
the recipe:

* YAML files suffix must be ``.yaml``, not ``.yml``;
* YAML files must be valid;
* YAML files must use 4 space indentations;
* YAML files under config/packages must not define a "parameters" section;
* JSON files must be valid;
* JSON files must use 4 space indentations;
* Aliases are only supported in the main repository, not the contrib one;
* Aliases must not be already defined by another package;
* The manifest file only contains supported keys;
* The Makefile file does not wrap Symfony Console commands as tasks
* The package must exist on Packagist;
* The package must have at least one version on Packagist;
* The package must have an MIT or BSD license;
* The package must be of type "symfony-bundle" if a bundle is registered in the manifest;
* The package must have a registered bundle in the manifest if type is "symfony-bundle";
* The package does not only register a bundle for all environments;
* The package does not depend on ``symfony/symfony``;
* All text files should end with a newline;
* All configuration file names under ``config`` should use the underscore notation;
* No "semantically" empty configuration files are created under ``config/packages``;
* All files are stored under a directory referenced by the "copy-from-recipe" section of "manifest.json";
* The pull request does not contain merge commits;
* The Symfony website must be referenced using HTTPs.

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
            "config/": "%CONFIG_DIR%/",
            "public/": "%PUBLIC_DIR%/",
            "src/": "%SRC_DIR%/"
        },
        "composer-scripts": {
            "make cache-warmup": "script",
            "assets:install --symlink --relative %PUBLIC_DIR%": "symfony-cmd"
        },
        "env": {
            "APP_ENV": "dev",
            "APP_DEBUG": "1",
            "APP_SECRET": "%generate(secret)%"
        },
        "gitignore": [
            ".env",
            "/public/bundles/"
            "/var/",
            "/vendor/"
        ]
    }

.. _`Symfony Flex`: https://github.com/symfony/flex
.. _`contrib repository`: https://github.com/symfony/recipes-contrib
.. _`Symfony Console styles and colors`: https://symfony.com/doc/current/console/coloring.html
