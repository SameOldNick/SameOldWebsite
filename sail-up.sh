#!/bin/bash

# Check if sail script exists
if [ ! -f "vendor/bin/sail" ]; then
    echo "Error: vendor/bin/sail not found."
    exit 1
fi

export APP_ENV="sail"

if [ -f ./.env."$APP_ENV" ]; then
  env_file=".env.${APP_ENV}"
elif [ -f ./.env ]; then
  env_file=".env"
fi

# Run "vendor/bin/sail up" with passed arguments and the environment file.
# If another environment is being used and the --env-file isn't passed, the database won't be setup correctly.
vendor/bin/sail "--env-file=$env_file" up "$@"
