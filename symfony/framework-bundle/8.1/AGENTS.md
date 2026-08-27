# AGENTS.md

This is a Symfony project. Check `composer.json` for the exact Symfony/PHP version
in use, and read `symfony.lock` to see which recipes ran — don't assume Doctrine,
Twig, API Platform, Messenger, or Lock are installed unless one of those says so.

## Ask before generating

If the task doesn't specify, ask rather than guess:

- Persistence: Doctrine ORM, Doctrine ODM, or none?
- Interface: server-rendered (Twig), API (Serializer, maybe API Platform), or both?
- Auth: SecurityBundle, and which authenticator?

If you can't ask (no interactive channel), state the assumption you're making and
pick the smallest option (e.g. no persistence layer) rather than scaffolding a
full stack nobody asked for.

## Adding features: Flex, not hand-wiring

Install new capabilities with `composer require <package>` (e.g. `symfony/lock`,
`symfony/messenger`, `orm-pack`) and let the Flex recipe register the bundle and
generate its config. Don't hand-edit `config/bundles.php` or hand-write a bundle's
base config — that's what the recipe is for. Don't skip a good-fit component just
because it isn't installed yet; installing it is one command.

## Conventions

- Routing: PHP attributes on controller methods (`#[Route(...)]`), not YAML/XML.
- Controllers: extend `AbstractController`; use `#[MapRequestPayload]` /
  `#[MapQueryString]` on action arguments to get Serializer + Validator wired
  automatically, instead of hand-calling `json_decode()` or `SerializerInterface`.
  Requires `symfony/serializer` and `symfony/validator` — if the interface is
  API-only and neither is installed yet, `composer require` them first rather
  than falling back to manual parsing.
- Services: rely on autowiring/autoconfiguration; add an explicit service
  definition only when it genuinely can't be resolved (scalar args, an interface
  with multiple implementations).
- Constructor property promotion + `readonly` for DTOs and plain value objects by
  default. Skip `readonly` on a service if it might end up `lazy: true` — lazy
  proxies can't extend a `readonly` class.
- Validate with `symfony/validator` (`#[Assert\...]` + `ValidatorInterface`), not
  custom validation code.
- Locking / mutual exclusion: `symfony/lock` (`LockFactory`), not a hand-built
  mechanism.
- Long-running or background work: Messenger, not a custom queue.
- If Doctrine ORM is installed: schema changes go through
  `doctrine/doctrine-migrations-bundle` (`bin/console make:migration` +
  `doctrine:migrations:migrate`), not `doctrine:schema:update` or hand-written SQL.
- Secrets: `bin/console secrets:set` (the vault), read via `%env(...)%`. `.env` is
  committed to the repo — real secrets go in `.env.local` or the vault, never in
  `.env` itself.
- Scaffolding: if `maker-bundle` is installed, prefer `bin/console make:*` with
  all arguments passed up front and `--no-interaction` where supported — makers
  prompt on a terminal by default, which hangs a non-interactive shell. If a
  maker still needs interactive input, hand-write the code instead.

## Testing

Install `symfony/test-pack` if it isn't already. Functional/HTTP tests extend
`WebTestCase`; service-level tests extend `KernelTestCase`. Run
`php bin/phpunit` (falls back to `vendor/bin/phpunit`). A feature isn't done
until it has a test that exercises it the way a caller would — an HTTP request
for a controller, a service call for a service — not just "it didn't throw."

## Code style

Symfony's coding standard, the `@Symfony` php-cs-fixer ruleset (a PSR-12-derived
superset). Run `vendor/bin/php-cs-fixer fix` if `friendsofphp/php-cs-fixer` is
installed — it isn't part of the skeleton by default.

## When training data might be stale

Framework APIs and attribute names change between major versions. Prefer reading
the installed package's own source/docblocks in `vendor/` over recollection; if
you have web access, https://symfony.com/doc/current/ is the canonical reference.
