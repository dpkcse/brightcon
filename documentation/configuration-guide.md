# Configuration Guide

Copy `.env.example` to `.env` only in the deployed installation. Configure the application URL, MySQL/MariaDB connection, file cache/session, and mail settings with hosting-specific values. Keep `APP_ENV=production`, `APP_DEBUG=false`, and `CMS_SEED_MODE=clean` unless demo content is explicitly intended. Never publish `.env`, `APP_KEY`, database credentials, or offline signing material. The product verification public key may be deployed through `CMS_LICENSE_OFFLINE_PUBLIC_KEY`; never deploy a signing key.
