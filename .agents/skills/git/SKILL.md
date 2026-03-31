---
name: Git Management (No-Commit)
description: Guidelines for managing the project repository without executing commits.
---

# Git Management Skill

This skill provides instructions for managing the `litterfreeleeds` repository. 

> [!CAUTION]
> **STRICT POLICY**: You are NOT allowed to run `git commit` or `git push`. All version control operations that modify the remote history or create new commits MUST be handled by the user.

## Core Responsibilities

1. **Repository Awareness**: Use `git status`, `git diff`, and `git branch` to understand the current state of the project.
2. **Commit Preparation**: Help the user prepare for a commit by summarizing changes and suggesting clear, concise commit messages.
3. **Change Verification**: Before asking the user to commit, verify that all intended changes are staged (`git status`) and no accidental debug code is included.

## Common Operations

### Checking Status
Always check the current status before and after making changes:
```bash
git status
```

### Reviewing Changes
Review the specific lines changed to ensure accuracy:
```bash
git diff
```

### Suggesting Commit Messages
When your task is complete, suggest a commit command to the user:
"I have completed the changes. You can commit them using: `git commit -m 'Your descriptive message here'`"

## Forbidden Commands
- `git commit`
- `git push`
- `git remote add/remove`
- `git reset --hard` (unless explicitly asked by the user to revert)
- `git fetch` or `git pull` (the user should handle syncing)
