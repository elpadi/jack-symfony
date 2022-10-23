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
elif [[ $ENV == "prod" ]]; then
    SYMFONY_REMOTE_DIR="/home1/elpadi/symfony"
    PUBLIC_REMOTE_DIR="/home1/elpadi/public_html"
fi

echo -e "Packaging up symfony code \n"
tar -czf symfony.tar.gz src/ vendor/ templates/

echo -e "Uploading symfony code package \n"
scp "$SITE_URL:$SYMFONY_REMOTE_DIR/symfony.tar.gz" symfony.tar.gz

echo -e "Unpacking symfony code \n"
echo -e "ssh $SITE_URL 'cd $SYMFONY_REMOTE_DIR && tar -xzf symfony.tar.gz && rm symfony.tar.gz' \n"
ssh $SITE_URL "cd $SYMFONY_REMOTE_DIR && tar -xzf symfony.tar.gz && rm symfony.tar.gz"

rm symfony.tar.gz

echo -e "Building front-end assets \n"
lando npm run build

cd public

echo -e "Packaging up public dir code \n"
tar -czf public.tar.gz build/

echo -e "Uploading public dir code package \n"
scp "$SITE_URL:$PUBLIC_REMOTE_DIR/public.tar.gz" public.tar.gz

echo -e "Unpacking public dir code \n"
echo -e "ssh $SITE_URL 'cd $PUBLIC_REMOTE_DIR && tar -xzf public.tar.gz && rm public.tar.gz' \n"
ssh $SITE_URL "cd $PUBLIC_REMOTE_DIR && tar -xzf public.tar.gz && rm public.tar.gz"

rm public.tar.gz
