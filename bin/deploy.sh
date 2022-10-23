#!/bin/bash

SITE_URL="elpadi@thejackmag.com"
SSH_PORT=2222

if [[ $ENV == '' ]]; then
    echo "Must set the environment to deploy to."
    exit 1
fi

if [[ $ENV == "staging" ]]; then
    SYMFONY_REMOTE_DIR="/home1/elpadi/staging.thejackmag.com/symfony"
    PUBLIC_REMOTE_DIR="/home1/elpadi/staging.thejackmag.com/symfony/public"
    NODE_ENV="dev"
elif [[ $ENV == "prod" ]]; then
    SYMFONY_REMOTE_DIR="/home1/elpadi/symfony"
    PUBLIC_REMOTE_DIR="/home1/elpadi/public_html"
    NODE_ENV="production"
fi

while [[ $# > 0 ]]; do
	if [[ "$1" == "symfony" ]]; then
        echo -e "Packaging up symfony code \n"
        tar -czf symfony.tar.gz src/ vendor/ templates/

        echo -e "Uploading symfony code package \n"
        scp symfony.tar.gz "$SITE_URL:$SYMFONY_REMOTE_DIR/symfony.tar.gz"

        echo -e "Unpacking symfony code \n"
        echo -e "ssh $SITE_URL 'cd $SYMFONY_REMOTE_DIR && tar -xzf symfony.tar.gz && rm symfony.tar.gz' \n"
        ssh $SITE_URL "cd $SYMFONY_REMOTE_DIR && tar -xzf symfony.tar.gz && rm symfony.tar.gz"

        rm symfony.tar.gz
    fi
	if [[ "$1" == "public" ]]; then
        echo -e "Building front-end assets \n"
        lando npm run build

        cd public

        echo -e "Packaging up public dir code \n"
        tar -czf public.tar.gz build/

        echo -e "Uploading public dir code package \n"
        scp public.tar.gz "$SITE_URL:$PUBLIC_REMOTE_DIR/public.tar.gz"

        echo -e "Unpacking public dir code \n"
        echo -e "ssh $SITE_URL 'cd $PUBLIC_REMOTE_DIR && tar -xzf public.tar.gz && rm public.tar.gz' \n"
        ssh $SITE_URL "cd $PUBLIC_REMOTE_DIR && tar -xzf public.tar.gz && rm public.tar.gz"

        rm public.tar.gz

        cd ..
	fi
	shift
done
