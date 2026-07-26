# Server requirements

Blocking: PHP 8.2+, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, PDO MySQL, Tokenizer, and XML. MySQL or MariaDB must already exist. cURL, GD, and Zip are currently recommendations for remote operations, image processing, and archive workflows rather than blockers. Run `php artisan cms:requirements`; exit code 0 means blockers pass and 1 means at least one failed. The report excludes paths, configuration, credentials, and detailed server internals.
