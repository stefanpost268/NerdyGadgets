# Database Migration Guide

## Introduction

This document provides a guide on how to perform database migrations for our application. The current migration system is not automatisch and must be done manualy.

## Prerequisites

Before you start the migration process, ensure you have the following:

- Access to the database system
- Necessary permissions to perform operations on the database
- Backup of all important data

## Migration Files

Migration files are located in the `Migrations` directory. Each file is a SQL script that makes some changes to the database schema.

The naming convention for migration files is as follows:
- `YYYY_MM_DD_HHMM_description_of_migration.sql`

Where:
- `YYYY_MM_DD_HHMM` is the timestamp of when the migration was created. This is used to determine the order in which migrations should be run. Migrations are run from oldest to newest based on this timestamp.
- `description_of_migration` is a brief description of what the migration does.

For example, the file `2023_11_28_2001_create_transaction_bind_table.sql` is a migration that was created on November 28, 2023 at 20:01 and it creates a table called `TransactionBind`.

## Running Migrations

To run migrations, you would typically use a database migration tool that supports your database system. This tool would read the migration files in the `Migrations` directory and apply them in the order determined by their timestamps.

You can also do it manually:
- `mysql -u root nerdygadgets < './Database/Migrations/2023_11_27_1050_create_user_table.sql'`