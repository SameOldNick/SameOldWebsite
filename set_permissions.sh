#!/bin/bash

# Check if the script is run as root
if [ "$EUID" -ne 0 ]; then
  echo "Please run this script as root (e.g., sudo $0 <user> <group>)"
  exit 1
fi

# Check if user and group are passed as arguments
if [ $# -ne 2 ]; then
  echo "Usage: $0 <user> <group>"
  exit 1
fi

USER=$1
GROUP=$2

# Set ownership of all files to the specified user and group
chown -R $USER:$GROUP .

# Set the 'bootstrap/cache' directory as writable
chmod -R 775 bootstrap/cache

# Set the 'storage' directory as writable
chmod -R 775 storage

echo "Permissions have been set for user: $USER and group: $GROUP"
