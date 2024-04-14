#!/bin/bash

# Set options to exit immediately if any command fails
set -o errexit
set -o pipefail

# Function to display help message
display_help() {
    cat << EOF
Usage: $0 [-i] [-t TARGET] [-h]
Options:
  -i, --initial     Run the initial seeder
  -t, --target      Specify the target for the install (host or sail)
  -h, --help        Display help message
EOF
    exit 1
}

# Default values
run_initial_seeder="false"
target="host"  # Default target is host

# Parse command-line options
while [[ $# -gt 0 ]]; do
    case $1 in
        -i|--initial) run_initial_seeder="true" ;;
        -t|--target) target="$2"; shift ;;
        -h|--help) display_help ;;
        *) echo "Error: Unknown option: $1" >&2; display_help ;;
    esac
    shift
done

# Set the appropriate command prefix based on the target
case "$target" in
    sail)
        export CMD_PREFIX="./vendor/bin/sail"
        ;;
    host)
        export CMD_PREFIX=""
        ;;
    *)
        echo "Invalid target. Exiting..."
        exit 1
        ;;
esac

# Confirm the .env file is configured unless -y or --yes option is provided
if [ "$skip_prompt" != "true" ]; then
    read -p "Have you updated the .env configuration variables? (y/n): " env_confirmed
    if [[ $env_confirmed != "y" ]]; then
        echo "Please update the .env configuration variables and run the script again."
        exit 1
    fi
fi

# Source the .env file to load environment variables
if [ -f .env ]; then
    source .env
fi

if [ "$sail" == "true" ]; then
    export CMD_PREFIX="./vendor/bin/sail"
fi

if [ -z "$DATABASE_ARGS" ]; then
    database_args=""  # Default to "" if not set
else
    database_args="$DATABASE_ARGS"
fi

if [ "$skip_prompt" == "true" ]; then
    database_args+=" -y"
fi

if [ "$run_initial_seeder" == "true" ]; then
    database_args+=" -i"
fi

if [ -z "$FRONTEND_ARGS" ]; then
    frontend_args="yarn"  # Default to "yarn" if not set
else
    frontend_args="$FRONTEND_ARGS"
fi

echo "Setting up database..."

./install/database.sh $database_args

echo "Building front-end..."

./install/frontend.sh $frontend_args

# Create symbolic link to storage directory
echo "Creating symbolic link to storage directory..."
$CMD_PREFIX php artisan storage:link --force

echo "Setup complete."
