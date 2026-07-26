# Directory permissions

PHP must write `storage`, `storage/app`, `storage/framework`, `storage/framework/cache`, `storage/framework/sessions`, `storage/framework/views`, `storage/logs`, and `bootstrap/cache`. Safe missing runtime subdirectories may be created, but the installer never changes ownership or runs chmod. Prefer owner/group-based 0755/0775 as appropriate to the host, not 0777. Root write permission is needed only while creating/updating `.env`; restrict it afterward. Public storage linking is optional and host-specific.
