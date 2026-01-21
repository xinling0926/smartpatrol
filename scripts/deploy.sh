#!/bin/bash
#
# Auto-deployment script for SmartPatrol
# Triggered by GitHub webhook via deploy.flag file
#

FLAG_FILE="/var/www/smart_patrol/writable/deploy.flag"
LOG_FILE="/var/log/patrol/deploy.log"
REPO_DIR="/var/www/smart_patrol"

# Check if flag file exists
if [ ! -f "$FLAG_FILE" ]; then
    exit 0
fi

echo "$(date '+%Y-%m-%d %H:%M:%S') - Deploy triggered" >> $LOG_FILE

# Remove flag file
rm -f $FLAG_FILE

# Pull latest code
cd $REPO_DIR
git pull origin main >> $LOG_FILE 2>&1

# Set permissions
chown -R apache:apache $REPO_DIR/writable

echo "$(date '+%Y-%m-%d %H:%M:%S') - Deploy completed" >> $LOG_FILE
