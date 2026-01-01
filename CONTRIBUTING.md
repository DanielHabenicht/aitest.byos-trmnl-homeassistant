# Contributing to TRMNL BYOS Home Assistant Add-on

Thank you for your interest in contributing! This document provides guidelines for contributing to this project.

## Development Setup

### Prerequisites

- Docker
- Home Assistant development environment (optional)
- Git

### Local Development

1. Clone the repository:
   ```bash
   git clone https://github.com/DanielHabenicht/aitest.byos-trmnl-homeassistant.git
   cd aitest.byos-trmnl-homeassistant
   ```

2. Build the Docker image:
   ```bash
   docker build -t trmnl-byos-addon .
   ```

3. Test locally (requires Home Assistant):
   - Add the repository to your local Home Assistant instance
   - Install and test the add-on

## Making Changes

### Code Style

- Follow PSR-12 coding standards for PHP code
- Use meaningful variable and function names
- Add comments for complex logic
- Keep functions small and focused

### File Structure

```
.
├── config.yaml                 # Add-on configuration
├── Dockerfile                  # Container definition
├── run.sh                      # Startup script
├── build.yaml                  # Build configuration
├── rootfs/                     # Files copied to container root
│   ├── etc/                    # Configuration files
│   ├── usr/local/bin/          # Scripts
│   └── var/www/html-custom/    # Custom Laravel files
└── docs/                       # Documentation
```

### Testing Changes

1. Build the add-on locally
2. Test in a Home Assistant development environment
3. Verify all features work as expected
4. Check logs for errors

## Submitting Changes

### Pull Request Process

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Commit with clear messages (`git commit -m 'Add amazing feature'`)
5. Push to your fork (`git push origin feature/amazing-feature`)
6. Open a Pull Request

### PR Guidelines

- Provide a clear description of the changes
- Reference any related issues
- Include screenshots for UI changes
- Ensure all tests pass
- Update documentation if needed

## Reporting Issues

### Bug Reports

When reporting bugs, please include:

- Home Assistant version
- Add-on version
- Steps to reproduce
- Expected behavior
- Actual behavior
- Relevant logs

### Feature Requests

When requesting features:

- Clearly describe the feature
- Explain the use case
- Provide examples if possible

## Code of Conduct

- Be respectful and inclusive
- Welcome newcomers
- Focus on constructive feedback
- Help others learn and grow

## Questions?

If you have questions, feel free to:

- Open an issue for discussion
- Check existing issues and documentation
- Reach out to maintainers

Thank you for contributing! 🎉
