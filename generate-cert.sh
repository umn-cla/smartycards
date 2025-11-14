#!/bin/bash

# Generate local SSL certificates for HTTPS development
#
# This script creates self-signed SSL certificates for localhost development
# using mkcert. The certificates will be placed in the .cert directory.
#
# Prerequisites:
# - mkcert must be installed:
#   - macOS: brew install mkcert
#   - Linux (Fedora): sudo dnf install mkcert
#   - Linux (Debian/Ubuntu): sudo apt install mkcert
# - Run 'mkcert -install' once to install the local CA
#
# The generated certificates will be valid for:
# - localhost
# - 127.0.0.1
# - ::1 (IPv6 localhost)
# - Your machine's local IP address (for testing on other devices)

# Clean up any existing certificates
rm -rf .cert

# Create certificate directory
mkdir -p .cert

# Detect OS and get local IP address
if [ "$(uname)" = "Darwin" ]; then
    # macOS: Get IP address from en0 interface
    LOCAL_IP=$(ipconfig getifaddr en0)
else
    # Linux: Get first IP address from hostname -I
    LOCAL_IP=$(hostname -I | awk '{print $1}')
fi

# Generate certificates with mkcert
# Include default hosts plus any additional hosts passed as arguments
mkcert -key-file ./.cert/key.pem -cert-file ./.cert/cert.pem \
    'localhost' \
    127.0.0.1 \
    ::1 \
    "$LOCAL_IP" \
    "$@"

echo "✓ SSL certificates generated in .cert/"
echo "  Local IP: $LOCAL_IP"
if [ $# -gt 0 ]; then
    echo "  Additional hosts: $*"
fi
