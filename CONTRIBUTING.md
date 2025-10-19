# Contributing to SQ Backend

First off, thank you for considering contributing to SQ Backend! It's people like you that make this project better for everyone.

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples** (code snippets, API requests, etc.)
- **Describe the behavior you observed** and what you expected
- **Include screenshots** if applicable
- **Note your environment** (OS, PHP version, Laravel version)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

- **Use a clear and descriptive title**
- **Provide a detailed description** of the suggested enhancement
- **Explain why this enhancement would be useful**
- **List some examples** of how it would be used

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Make your changes** following our coding standards
3. **Test your changes** thoroughly
4. **Update documentation** if needed
5. **Write a clear commit message**
6. **Submit a pull request**

## Development Setup

1. Clone your fork:
   ```bash
   git clone https://github.com/your-username/sq-backend.git
   cd sq-backend
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Setup database:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. Run tests:
   ```bash
   php run_all_tests.php
   ```

## Coding Standards

### PHP Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style
- Use meaningful variable and function names
- Add docblocks to all classes and public methods
- Keep functions small and focused

### Laravel Best Practices

- Use Eloquent ORM, avoid raw queries
- Use Form Request validation
- Use API Resources for responses
- Follow RESTful conventions
- Use database transactions for critical operations

### Example Code Style

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Create a new user.
     *
     * @param  StoreUserRequest  $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::create($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => new UserResource($user)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
```

## Git Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters
- Reference issues and pull requests after the first line

### Commit Message Format

```
<type>: <subject>

<body>

<footer>
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, missing semi colons, etc)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

**Example:**
```
feat: add role-based authorization for teacher management

- Implement RBAC middleware for guru endpoints
- Only admin roles can create/update/delete teachers
- Add comprehensive test coverage

Closes #123
```

## Testing

All contributions should include tests:

- Write tests for new features
- Update tests for changed features
- Ensure all tests pass before submitting PR
- Aim for high test coverage

```bash
# Run all tests
php run_all_tests.php

# Run specific test
php test_authentication.php
```

## Documentation

- Update README.md if needed
- Add docblocks to new methods
- Update API documentation
- Create/update relevant .md files in docs/

## Review Process

1. **Automated checks** will run on your PR
2. **Code review** by maintainers
3. **Testing** of your changes
4. **Approval** and merge

## Questions?

Feel free to:
- Open an issue for discussion
- Contact maintainers
- Join our community discussions

## Recognition

Contributors will be recognized in:
- README.md contributors section
- Release notes
- Project documentation

Thank you for contributing! 🎉
