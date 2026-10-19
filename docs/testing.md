# Testing

This package provides a consistent set of [Composer](https://getcomposer.org/) scripts for local validation.

Install the checkout dependencies with `composer install --prefer-dist` after following the
[installation guide](installation.md). Foxy uses its native PHP manager for the published browser assets; the PHP checks
require no Node.js build.

Tool references:

- [Composer Require Checker](https://github.com/maglnet/ComposerRequireChecker) for dependency definition checks.
- [Easy Coding Standard (ECS)](https://github.com/easy-coding-standard/easy-coding-standard) for coding standards.
- [Infection](https://infection.github.io/) for mutation testing.
- [PHPStan](https://phpstan.org/) for static analysis.
- [PHPUnit](https://phpunit.de/) for unit tests.

## Repository quality checks

Make the CLI tools used by the shared quality workflow available on `PATH`: actionlint, codespell, EditorConfig Checker,
markdownlint-cli2, Prettier and yamllint. The command reads the repository's linter, formatter and ignore files. It
skips ignored local dependency, runtime and private artifact trees that are absent from a clean CI checkout.

```bash
composer quality
```

## Workflow security checks

Make zizmor and Gitleaks available on `PATH`. Audit the current workflow files with the same pedantic persona used by
the shared security workflow:

```bash
composer zizmor
```

Run that audit and scan Git history for secrets with the repository's Gitleaks configuration. Findings make the command
fail; inspect them rather than disabling the checks.

```bash
composer security
```

Zizmor needs GitHub API access for its online audits. If the tool runs offline, report that limit separately. To request
an offline audit explicitly:

```bash
composer zizmor -- --offline
```

Report hosted CI execution and its PHP/OS matrix separately from these local commands. Keep generated reports in ignored
`internal/`.

## Coding standards (ECS)

Check standards without changing files:

```bash
composer ecs-check
```

`composer ecs` applies fixes. Review its changes before keeping them.

Run Easy Coding Standard (ECS) and apply fixes.

```bash
composer ecs
```

## Dependency definition check

Verify that runtime dependencies are correctly declared in `composer.json`.

```bash
composer check-dependencies
```

## Mutation testing (Infection)

Run mutation testing.

```bash
composer mutation
```

Run native Infection with PHPStan enabled.

```bash
composer mutation-static
```

## Static analysis (PHPStan)

Run static analysis.

```bash
composer static
```

## Unit tests (PHPUnit)

Run the full test suite.

```bash
composer tests
```

## Passing extra arguments

Composer scripts support forwarding additional arguments using `--`.

Run PHPUnit with code coverage report generation.

```bash
composer tests -- --coverage-html internal/coverage
```

Run PHPStan with a different memory limit.

```bash
composer static -- --memory-limit=512M
```

## Browser and form flows

Use the [usage examples](examples.md) in an application and check keyboard interaction, validation, remote results,
errors and repeated widget initialization on both supported Yii lines. PHP assertions do not establish browser or screen
reader behavior.

The existing `test` and `phpstan` script names remain aliases of `tests` and `static`.

[Back to documentation](../README.md#documentation)
