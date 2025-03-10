-- Fix PostgreSQL permissions for Laravel migrations
-- First, connect to the database as a superuser (postgres)

-- Ensure the user exists
CREATE USER laravel_api WITH PASSWORD 'laravel_api' IF NOT EXISTS;

-- Ensure the database exists
CREATE DATABASE laravel_api WITH OWNER laravel_api IF NOT EXISTS;

-- Connect to the laravel_api database
\c laravel_api

-- Grant all privileges on the database
ALTER DATABASE laravel_api OWNER TO laravel_api;

-- Grant schema permissions
ALTER SCHEMA public OWNER TO laravel_api;

-- Grant all privileges on all tables
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO laravel_api;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO laravel_api;
GRANT ALL PRIVILEGES ON ALL FUNCTIONS IN SCHEMA public TO laravel_api;

-- Grant create and usage permissions on the schema
GRANT CREATE ON SCHEMA public TO laravel_api;
GRANT USAGE ON SCHEMA public TO laravel_api;

-- Grant permissions for future tables
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO laravel_api;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO laravel_api;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON FUNCTIONS TO laravel_api; 