#!/bin/bash

hadTransferSymlink=false
hadUtilsSymlink=false

if [ -L "vendor/olcs/olcs-transfer" ]
then
    echo "Removing symlink"
    hadTransferSymlink=true
    rm vendor/olcs/olcs-transfer
fi

if [ -L "vendor/olcs/olcs-utils" ]
then
    echo "Removing symlink"
    hadUtilsSymlink=true
    rm vendor/olcs/olcs-utils
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

if [ "$hadUtilsSymlink" = true ] || [ "$1" = "--force" ];
then
    if [ -d "vendor/olcs/olcs-utils" ] ;
    then
        echo "Recreating symlink"
        rm -rf vendor/olcs/olcs-utils
        (cd vendor/olcs && ln -s ../../../olcs-utils/ olcs-utils)
    fi
fi
