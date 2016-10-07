#!/bin/sh
chmod -R 707 *
rsync -auvz --delete --exclude='*.sh' --exclude='app/webroot/audio/roudoku/*' --exclude='app/Config/core.php' --exclude='app/Config/database.php' --exclude='*.wav' --exclude='.DS_store' --exclude='app/tmp/*' . roudoku:/var/www/html
rsync -auvz --delete --exclude='*.sh' --exclude='app/webroot/audio/roudoku/*' --exclude='app/Config/core.php' --exclude='app/Config/database.php' --exclude='*.wav' --exclude='.DS_store' --exclude='app/tmp/*' . roudoku_sub:/var/www/html

