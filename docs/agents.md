# Agent skills

The editable source is [`.agents/skills/yii2-select2/SKILL.md`](../.agents/skills/yii2-select2/SKILL.md). It guides
Select2 configuration, ActiveForm validation, remote data, and lifecycle cleanup using the installed package's
contracts.

## Install the Select2 skill

### Automatic Composer installation

Use the [one-time application setup](https://yii2-framework.github.io/docs/project/ai-agents/#composer-installation)
with `auto-sync` set to `true` in `skills.json`, approving `yii2-extensions/select2` explicitly. The package declares
`.agents/skills` as its bundle source. For an installed release containing the skill:

```bash
composer install
composer skills:show
```

With `auto-sync` enabled, `composer install` and `composer update` synchronize the skill automatically. If it is
`false`, run `composer skills:update` after dependency installation or updates.

For a release without the bundle, register a source checkout once instead:

```bash
composer skills:add /absolute/path/to/select2 --skill=yii2-select2
```

### Optional manual installation

Copy or link the complete `yii2-select2` skill directory from a package checkout or from
`vendor/yii2-extensions/select2/.agents/skills/` into the application client's project skill path:

| Client      | Project skill path                                             | Explicit activation                        |
| ----------- | -------------------------------------------------------------- | ------------------------------------------ |
| Codex       | `.agents/skills/yii2-select2/SKILL.md`                         | `$yii2-select2` or the skill picker        |
| Claude Code | `.claude/skills/yii2-select2/SKILL.md`                         | `/yii2-select2`                            |
| OpenCode    | `.opencode/skills/yii2-select2/SKILL.md`, or `.agents/skills/` | Load `yii2-select2` through its skill tool |

Keep one effective installation per client. A Composer installation in `vendor/` alone does not expose the skill to an
agent opened at the application root. Refresh a copied directory when upgrading the package; edit the package source
instead of maintaining a separate set of instructions. The source checkout already uses Codex's project skill path.

See [Claude Code skills](https://code.claude.com/docs/en/skills) and [OpenCode skills](https://opencode.ai/docs/skills/)
for client discovery rules.

## Yii framework skill

For Yii 22 conventions, use the `yii2-framework` workflow from the
[common skill package](https://github.com/yii2-extensions/skill). The
[one-time Composer application setup](https://yii2-framework.github.io/docs/project/ai-agents/#composer-installation)
installs it together with the other framework skills. The same `auto-sync` setting and manual synchronization step
apply. Keep one framework donor in the application configuration.

For optional manual installation, copy the complete `skills/yii2-framework/` directory from a common-package checkout,
including its referenced resources, into the client project path above. Verify discovery in the client. For Yii 2.0
applications, use matching framework contracts; do not apply Yii 22-only setup automatically.

## Choose matching documentation

Resolve `yii2-extensions/select2`, `yiisoft/yii2`, and any optional theme or validation adapter separately. Use each
package's actual installation path and Composer-selected reference; for live checkouts, the current files and `HEAD`
govern, including uncommitted changes. A lockfile may describe a different state from the installed files.

When a matching deployment of the [shared documentation site](https://yii2-framework.github.io/docs/) provides
`llms.txt`, read that index and fetch individual Markdown pages by appending `index.md` to their URLs. A development
version label alone does not establish a matching source revision. Use the installed package's `docs/`, PHPDoc, source,
and changelog when hosted guides are missing, unavailable, or describe a different revision. Both the Select2 skill and
these guides are included in the package archive.

## Verify installation and use

Open a fresh client session at the application root, inspect its skill list, then explicitly invoke `yii2-select2` for a
small form task. File installation, client discovery, instruction loading, and a working integration are separate
checks. Automatic selection depends on the client and model.

A copied skill resolves its resources from the installed package rather than relative to the copied directory. Verify
generated forms, server validation, browser events, and cleanup in the consuming application.

[Back to documentation](../README.md#documentation)
