#!/bin/bash

hadTransferSymlink=false

if [ -L "vendor/olcs/olcs-transfer" ]
then
    echo "Removing symlink"
    hadTransferSymlink=true
    rm vendor/olcs/olcs-transfer
fi

if [ -f composer.phar ] ;
then
    php composer.phar update
else
    composer update
fi

if [ "$hadTransferSymlink" = true ] || [ "$1" = "--force" ];
then
    if [ -d "vendor/olcs/olcs-transfer" ] ;
    then
        echo "Recreating symlink"
        rm -rf vendor/olcs/olcs-transfer
        (cd vendor/olcs && ln -s ../../../olcs-transfer/ olcs-transfer)
    fi
fi
