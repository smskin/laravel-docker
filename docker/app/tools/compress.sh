#!/bin/bash

###########################################################################
# Usage:
#        pre-compress-web-assets [.|folder_name]
#
# This script will recursively look for the files in the $ext variable
# and compress/re-compress them.
#
# By default it will look into the current working folder,
# but one can provide a path for the script to crawl.
# Source: https://gist.githubusercontent.com/rabin-io/63000f48d1f9170f17ea5f73bcf84d66/raw/pre-compress-web-assets
#
###########################################################################
# Flags

set -e # break on error.
set -u # break on using undefined variable.

base_name=$(basename "${0}")

print_fail() {
    echo "$*"
    exit1
}

compressResource() {

    gzip -c9 "${1}" >"${1}.gz"
    touch -c --reference="${1}" "${1}.gz"

    echo "Compressed: ${1} > ${1}.gz"
}

appDir=${1-${PWD?WTF}}
echo "Preocessing ${appDir}"

ext="css|js|eot|svg|ttf|woff|html"

# fetch all existing gzipped CSS/JavaScript/webfont files and remove files that do not have a base uncompressed file
find "$appDir" -type f -regextype posix-extended -iregex ".*\.(${ext})\.gz$" -print0 | while read -d '' compressFile; do
    if [[ ! -f ${compressFile%.gz} ]]; then
        # remove orphan gzipped file
        rm "${compressFile}" && echo "Removed: ${compressFile}"
    fi
done

# fetch all source CSS/JS/webfont files - excluding *.src.* variants (pre-minified CSS/JavaScript)
# gzip each file and give timestamp identical to that of the uncompressed source file
find "$appDir" -type f -regextype posix-extended \( -iregex ".*\.(${ext})$" \) \( ! -name "*.src.css" -and ! -name "*.src.js" \) -print0 | while read -d '' sourceFile; do
    if [[ -f "${sourceFile}.gz" ]]; then
        # only re-gzip if source file is different in timestamp to the existing gzip file
        if [[ (${sourceFile} -nt "${sourceFile}.gz") || (${sourceFile} -ot "${sourceFile}.gz") ]]; then
            # re-compress
            compressResource "${sourceFile}"
        fi
    else
        compressResource "${sourceFile}"
    fi
done
