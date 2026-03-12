#!/usr/bin/env bash
set -e

###############################################################################
# ENV VAR SANITIZATION
#
# Dokploy's PostgreSQL storage has corrupted environment variables on multiple
# occasions by appending trailing whitespace and '+' characters (artifacts from
# PostgreSQL text column formatting). This broke Laravel startup at least twice.
#
# This sanitization runs on EVERY container boot as a permanent safeguard.
#
# Corruption pattern: "real_value   +" (spaces then plus, or just spaces)
# Safe pattern:       "base64value+" (plus directly after non-space = legitimate)
#
# Strategy: strip trailing whitespace. Then repeatedly strip trailing "+" that
# was separated from the real value by whitespace (i.e., "<space>+" at end).
###############################################################################
echo "[start.sh] Sanitizing environment variables..."
sanitized=0
for varname in $(compgen -e); do
    original="${!varname}"
    cleaned="$original"
    prev=""
    # Iterate until stable (handles "value  +  +  " nesting)
    while [ "$cleaned" != "$prev" ]; do
        prev="$cleaned"
        # Remove pattern: optional whitespace, then '+', then optional whitespace at end
        # But ONLY if there's at least one whitespace char in the match (so bare "value+" is safe)
        cleaned=$(printf '%s' "$cleaned" | sed 's/[[:space:]][[:space:]]*+\?[[:space:]]*$//')
    done
    if [ "$original" != "$cleaned" ]; then
        export "$varname"="$cleaned"
        echo "[start.sh]   Cleaned: $varname"
        sanitized=$((sanitized + 1))
    fi
done
echo "[start.sh] Sanitization complete. $sanitized variable(s) cleaned."

###############################################################################
# FIND PHP WITH EXTENSIONS (nixpacks builds put it in /nix/store)
###############################################################################
PHPEXT=$(ls /nix/store/*/bin/php 2>/dev/null | grep with-extensions | head -1)
if [ -z "$PHPEXT" ]; then
    echo "[start.sh] FATAL: Could not find php-with-extensions in /nix/store"
    exit 1
fi
echo "[start.sh] Using PHP: $PHPEXT"

###############################################################################
# PHP INI OVERRIDES
###############################################################################
mkdir -p /tmp/phpini
cat > /tmp/phpini/uploads.ini << 'INI'
upload_max_filesize=50M
post_max_size=60M
max_file_uploads=20
max_execution_time=300
max_input_time=300
memory_limit=256M
INI

###############################################################################
# LARAVEL STARTUP SEQUENCE
###############################################################################
echo "[start.sh] Discovering packages..."
$PHPEXT artisan package:discover --ansi

echo "[start.sh] Creating storage link..."
$PHPEXT artisan storage:link --force

mkdir -p storage/logs

echo "[start.sh] Running migrations..."
$PHPEXT artisan migrate --force

echo "[start.sh] Starting server on 0.0.0.0:${PORT:-8080}..."
exec $PHPEXT artisan serve --host=0.0.0.0 --port=${PORT:-8080}
