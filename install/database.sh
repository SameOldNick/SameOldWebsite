#!/bin/bash

# Set options to exit immediately if any command fails
set -euo pipefail

# Function to display help message
display_help() {
    cat << EOF
Usage: $0 [-y] [-i] [-h]
Options:
  -y, --yes       Proceed without asking for confirmation
  -i, --initial   Run the initial seeder
  -h, --help      Display help message
EOF
    exit 1
}

# Parse command-line options
skip_prompt="false"
initial_seeder="false"

while [[ $# -gt 0 ]]; do
    case $1 in
        -y|--yes) skip_prompt="true" ;;
        -i|--initial) initial_seeder="true" ;;
        -h|--help) display_help ;;
        *) echo "Error: Unknown option: $1" >&2; display_help ;;
    esac
    shift
done

# Function to confirm database setup
confirm_database_setup() {
    if [ "$skip_prompt" != "true" ]; then
        read -p "This will erase everything in the database. Are you sure you want to continue? (y/n): " erase_confirmed
        [[ $erase_confirmed == "y" ]] || { echo "Cancelled database setup."; exit 1; }
    fi
}

# Source the .env file to load environment variables
[ -f .env ] && source .env

# Set the command prefix
cmd_prefix="${CMD_PREFIX:-}"

# Set the artisan command
artisan="${ARTISAN:-php artisan}"

# Confirm database setup
confirm_database_setup

# Run migrations
echo "Running database migrations..."
$cmd_prefix $artisan migrate:fresh

# Seed the database
echo "Seeding the database..."
$cmd_prefix $artisan db:seed SetupSeeder

# Run initial seeder if specified
if [ "$initial_seeder" == "true" ]; then
    echo "Seeding initial data into database..."
    $cmd_prefix $artisan db:seed InitialSeeder
fi

echo "Database setup complete."
