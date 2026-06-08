#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
BACKUP_DIR="${ROOT}/backups"
STAMP="$(date +%Y%m%d-%H%M%S)"
ARCHIVE="${BACKUP_DIR}/orange-chicken-${STAMP}.tar.gz"

mkdir -p "${BACKUP_DIR}"

tar -czf "${ARCHIVE}" \
  -C "${ROOT}/volumes" \
  gallery data

echo "Backup saved: ${ARCHIVE}"
