#!/bin/bash

# Set options to exit immediately if any command fails
set -o errexit
set -o pipefail

# Check if the vendor directory exists
if [ ! -d "vendor" ]; then
    # Check if composer command exists
    if ! command -v composer &> /dev/null; then
        echo "Composer is not installed. Please install Composer to continue."
        exit 1
    fi

    # Run composer install
    echo "Installing PHP packages using composer..."
    composer install
fi

# Check if the user is setting up on Sail
echo "Are you setting up on Larvel Sail? (y/N)"
read -r laravel_sail

if [ "$laravel_sail" == "true" ]; then
    env_file=".env.sail"
    artisan_args="--env=sail"
else
    env_file=".env"
    artisan_args=""
fi

# Check if .env file doesn't exist
if [ ! -f "$env_file" ]; then
    # Copy .env.example to .env
    cp .env.example $env_file

    # Generate Laravel application key
    php artisan $artisan_args key:generate
fi

# Open .env file in text editor
if [ -n "$EDITOR" ]; then
    $EDITOR .env
elif command -v nano &> /dev/null; then
    nano .env
elif command -v vim &> /dev/null; then
    vim .env
elif command -v vi &> /dev/null; then
    vi .env
else
    echo "No text editor found. Please manually edit the .env file."
fi

# Inform the user to proceed with setup
echo "Please proceed with the necessary setup steps for your environment."

if [ "$laravel_sail" == "true" ]; then
    echo "Run the following to run Same Old Website with Laravel Sail:"
    echo
    echo "./sail-up.sh --build -d"
    echo "./sail artisan setup:project"
else
    echo "Run the following to setup Same Old Website on the host machine:"
    echo
    echo "php artisan setup:project"
fi
