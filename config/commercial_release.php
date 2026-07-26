<?php

return [
    'required_files' => [
        '.env.example', 'composer.json', 'composer.lock', 'package.json', 'package-lock.json',
        'THIRD-PARTY-LICENSES.md', 'documentation/commercial-readiness-audit.md',
        'documentation/release-security-checklist.md', 'documentation/third-party-asset-inventory.md',
        'documentation/repository-hygiene-policy.md', 'documentation/release-exclusion-policy.md',
        'docs/deployment-guide.md', 'LICENSE', 'CHANGELOG.md',
    ],
    'excluded_directories' => [
        '.git', 'vendor', 'node_modules', 'storage/framework', 'storage/logs', 'bootstrap/cache',
    ],
    'excluded_content_files' => ['composer.lock', 'package-lock.json'],
    'blocking_extensions' => [
        'log', 'sql', 'sqlite', 'sqlite3', 'db', 'dump', 'bak', 'backup', 'zip', 'tar', 'gz',
        'tgz', 'pem', 'key', 'p12', 'pfx',
    ],
    'forbidden_branding' => [
        ['term' => 'BrightCon', 'severity' => 'warning'],
        ['term' => 'Bright Construction', 'severity' => 'warning'],
        ['term' => 'brightconeng.com', 'severity' => 'warning'],
    ],
    'sensitive_content_rules' => [
        'credential assignment' => '/\b(?:password|secret|api[_-]?key|access[_-]?token|private[_-]?key)\s*[:=]\s*[\'\"][^\'\"]{4,}/i',
        'authorization token' => '/\b(?:authorization|bearer)\s*[:= ]\s*[\'\"]?[^\s\'\"]{8,}/i',
        'default credential' => '/\b(?:password|secret)\s*[:=]\s*[\'\"]?(?:admin|password|changeme|secret|12345678)\b/i',
    ],
    'production_domain_rules' => ['/https?:\/\/(?!example\.(?:com|org|net))[^\s\'\"]+/i'],
    'absolute_path_rules' => ['/(?:\/home\/|\/var\/www\/|[A-Z]:\\\\Users\\\\)[^\s\'\"]+/i'],
    'allowed_demo_domains' => ['example.com', 'example.org', 'example.net'],
    'unverified_assets' => [
        'public/favicon.ico',
        'public/storage/uploads',
        'storage/app/public/uploads',
    ],
    'packaging' => [
        'vendor' => 'UNRESOLVED: source package excludes vendor; dependency-included package requires approval and license review.',
        'public_build' => 'INCLUDE: shared-hosting deployment supports uploading locally compiled Vite assets.',
    ],
];
