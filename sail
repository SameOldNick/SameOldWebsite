#!/bin/bash

# Check if sail script exists
if [ ! -f "vendor/bin/sail" ]; then
    echo "Error: vendor/bin/sail not found."
    exit 1
fi

export APP_ENV="sail"

# Run vendor/bin/sail with passed arguments
vendor/bin/sail "$@"
