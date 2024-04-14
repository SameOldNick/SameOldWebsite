#!/bin/bash

# Set options to exit immediately if any command fails
set -o errexit
set -o pipefail

# Source the .env file to load environment variables
if [ -f .env ]; then
    source .env
fi

# Function to display help message
display_help() {
    echo "Usage: $0 [npm|yarn|pnpm]"
    exit 1
}

# Parse command-line options
if [ "$#" -ne 1 ]; then
    display_help
fi

# Assign the first argument to package_manager variable
package_manager="$1"

# Check if the CMD_PREFIX environment variable is set
if [ -z "$CMD_PREFIX" ]; then
    cmd_prefix=""  # Default to "" if not set
else
    cmd_prefix="$CMD_PREFIX"
fi

# Determine the correct command based on the package manager
case "$package_manager" in
    npm)
        npm="${cmd_prefix} npm"  # Default to "${cmd_prefix} npm" if not set
        echo "Installing NodeJS packages with npm..."
        $npm install
        echo "Building front-end assets for production..."
        $npm run build
        ;;
    yarn)
        yarn="${cmd_prefix} yarn"  # Default to "${cmd_prefix} yarn" if not set
        echo "Installing NodeJS packages with yarn..."
        $yarn install
        echo "Building front-end assets for production..."
        $yarn run build
        ;;
    pnpm)
        pnpm="${cmd_prefix} pnpm"  # Default to "${cmd_prefix} pnpm" if not set
        echo "Installing NodeJS packages with pnpm..."
        $pnpm install
        echo "Building front-end assets for production..."
        $pnpm run build
        ;;
    *)
        echo "Invalid choice. Exiting..."
        exit 1
        ;;
esac

echo "Compiled front-end assets."
