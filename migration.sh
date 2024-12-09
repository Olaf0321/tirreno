#!/usr/bin/env bash
# Script to perform "up" migrations (if any) in order to bring database in accordance with the latest codebase.
# Migrations must be idempotent - executing script multiple times should result in the same (successful) outcome.

# `migration.sh` will be executed in one-off dyno, thus it is possible to install additional dependencies if needed:
# they will not affect running instances and will be purged once dyno is terminated.

# Available env variables:
# * `DATABASE_URL` - database endpoint in format `postgres://<username>:<password>@<host>:<port>/<database>`
# (In fact, all variables set for the application are available here as well)

php vendor/bin/migrate up
