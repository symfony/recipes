# AGENTS.md

This is a Symfony application. `composer.json` gives the versions of Symfony and
PHP. `symfony.lock` lists the recipes that ran. Check both before you assume
that a package is installed.

## Installing packages

Add a feature with `composer require <package>` (`symfony/messenger`,
`orm-pack`...): its Flex recipe registers the bundle and writes its default
configuration. Don't edit `config/bundles.php` or write that configuration by
hand.

Before writing infrastructure code or adding a third-party library, check
whether a Symfony component does the job: Lock, Cache, HttpClient, Mailer,
Messenger, Scheduler, RateLimiter, Workflow... Usually there is one, and
`composer require` installs it.

## Configuration

- Since Symfony 8.2, each component is configured at its own root key:
  `messenger:`, `cache:`, `mailer:`, `validation:`... not under `framework:` as
  in older versions.
- `config/reference.php` describes every option of the installed bundles, and
  `config/schema.json` is their JSON schema, which editors use and `lint:yaml`
  checks when `opis/json-schema` is installed. Both are regenerated when the
  container is rebuilt in debug mode. Read them instead of guessing an option.
- Configure services and routes with attributes, YAML or PHP. Symfony 8
  removed the XML format.
- Prefer attributes to configuration files: `#[Route]`, `#[IsGranted]`,
  `#[AsCommand]`, `#[AsEventListener]`, `#[AsMessageHandler]`, `#[Autowire]`,
  `#[Target]`... Services are autowired and autoconfigured, so a service
  definition in YAML is the last resort.
- Map request data with `#[MapRequestPayload]` and `#[MapQueryString]` on the
  arguments of an action. These attributes deserialize and validate the data,
  unlike `json_decode()`.
- `.env` is committed and contains default values only. Secrets go in
  `.env.local`, which is ignored by git, or in the vault
  (`bin/console secrets:set`).

## Running the app

- If `compose.yaml` runs services (database, mailer...), run commands through
  the Symfony CLI: `symfony console ...`, `symfony php bin/phpunit`. The CLI
  exposes the env vars of the running containers, and `bin/console` alone
  doesn't see them. `symfony serve -d` starts a web server.
- There's no need to clear the cache in dev: the kernel rebuilds what changed.
- When something fails, read `var/log/dev.log` and the profiler (`/_profiler`
  lists the last requests) before changing code.
- Makers (`bin/console make:*`) ask for missing arguments, and a
  non-interactive shell then waits forever. Pass all the arguments, with
  `--no-interaction`.
- With Doctrine, change the schema with migrations (`make:migration`, then
  `doctrine:migrations:migrate`), not with `doctrine:schema:update`.

## Tests

`symfony/test-pack` provides PHPUnit: run `php bin/phpunit`. Extend
`WebTestCase` to test HTTP requests and `KernelTestCase` to test services. These
helpers replace a lot of setup code:

- `$client->loginUser($user)` instead of submitting a login form;
- `MessengerAssertionsTrait`, with `consumeQueuedMessages()`, for the messages
  sent to in-memory transports;
- `#[UseRecord]` to record HTTP exchanges once and replay them, instead of
  calling real APIs;
- `ClockSensitiveTrait::mockTime()` to control the time.

## Writing

These rules apply to comments, commit messages, docs and anything written for
the team:

- Write plain, factual English, because many readers aren't native speakers.
  Write one idea per sentence and use verbs in their everyday sense. Repeat the
  noun instead of using a pronoun that makes the reader look back.
- State facts, not how good they are. Avoid words like "robust", "seamless",
  "powerful" or "simply", exclamation marks and emoji.
- Start with what changes for the reader, then explain why. Don't repeat what
  the code or the diff already shows.
- Use commas, colons and parentheses, not em-dashes.
- A commit message says what changed in one line, plus a sentence or two when
  the reason isn't obvious.

## Code

- Write a comment only for what the code can't say, such as a reason, a
  reference to a spec or a non-obvious constraint. Don't narrate the code.
- Don't set an option to its default value. `config/reference.php` shows the
  defaults.
- Fix a bug by writing a test that reproduces it first.
- Keep the diff to what the task needs: no unrelated renames, reformatting or
  refactoring.

## When a package gets in the way

- Before you work around what looks like a bug or a missing feature of Symfony
  or another package, check that the bug is real. Reproduce it in a few lines,
  compare the installed version with the latest release, and search the issues
  and pull requests of the project.
- If the bug is real, keep the workaround small. Link the workaround to the
  upstream issue or pull request in a comment, so that it can be removed after
  the fix is released.
- Offer to report the bug. Draft the issue with the reproduction, or a pull
  request with a test when the fix is clear. Ask before posting anything, since
  it is published under the name of the person you work for.
- Report security issues privately, as the security policy of the project says,
  never in a public issue. For Symfony, see
  https://symfony.com/doc/current/contributing/code/security.html.

## Look it up

Symfony's APIs change between versions, and what you remember may be outdated.
The application can tell what exists:

- `bin/console debug:autowiring <keyword>`: the services you can type-hint;
- `bin/console debug:autoconfiguration <keyword>`: the attributes and interfaces
  that make a class a command, a listener, a voter, a message handler...;
- `bin/console debug:config <root key>`: the configuration as resolved;
- `bin/console debug:router`, `debug:container --env-vars`, `debug:messenger`,
  `debug:event-dispatcher`...;
- `bin/console lint:container`, `lint:yaml config/`, `lint:twig templates/`: to
  catch errors before running;
- the source and the docblocks in `vendor/`;
- https://symfony.com/doc/current/, switched to the version of `composer.json`
  when it differs.
