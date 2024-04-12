# Contributions

## Recommended Environment

- PHP v8.2.x
- NodeJS v20.12.x
- Yarn v4.1.x
- Composer v2.7.x

## Recommendations

### 1. Commit Descriptive Messages

Provide clear and concise commit messages that describe the purpose of the changes made in the commit.

### 2. Stage Relevant Changes

Only stage and commit changes that are relevant to the current task or issue being addressed. Avoid committing unrelated changes.

### 3. Test Locally

Test your changes locally before committing to ensure they function as intended and do not introduce errors or bugs.

### 4. Pull Before Push

Always pull the latest changes from the remote repository before pushing your changes to avoid conflicts and ensure your local repository is up-to-date.

### 5. Resolve Conflicts Promptly

If conflicts arise during the pull or merge process, resolve them promptly and communicate with team members if necessary.

### 6. Follow Coding Standards

Maintain coding standards and style guidelines established for the project or organization. Ensure consistency in formatting and code structure.

### 7. Review Changes

Review your changes before committing to catch any mistakes or overlooked issues. Consider doing a code review.

### 8. Include Relevant Documentation

Update relevant documentation, such as README files or inline comments, to reflect any changes made and provide context for future developers.

### 9. Avoid Pushing Sensitive Information

Be cautious not to push sensitive information, such as passwords, API keys, or confidential data, to the repository. Utilize environment variables or configuration files for such information.

### 10. Monitor Continuous Integration (CI) Status

Ensure that any automated tests or CI pipelines associated with the repository pass successfully before pushing changes. Fix failing tests or build failures promptly.

## Frequently Asked Questions

### Why aren't NodeJS packages detected in VS Code after installing them with Yarn 2?

Yarn 2 has adopted Plug n Play (PnP) as its package installation strategy. Consequently, packages are no longer stored in the `node_modules` directory. IDEs like VS Code need explicit instructions on where to locate these packages. To inform VS Code about the package locations, execute the following command:

```
yarn dlx @yarnpkg/sdks vscode
```

This command can also be utilized for other IDEs:

```
yarn dlx @yarnpkg/sdks -h
```

[Source](https://stackoverflow.com/questions/65328123/how-to-configure-vscode-to-run-yarn-2-with-pnp-powered-typescript)
