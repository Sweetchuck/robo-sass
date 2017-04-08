#!/usr/bin/env bash

set -x
set -e

rm -rf "$HOME/.sassphp"
git clone https://github.com/absalomedia/sassphp.git "$HOME/.sassphp"
cd "$HOME/.sassphp"
git checkout PR3
git submodule init
git submodule update
php ./install.php
make install
iniDir=$(php -r '$f = explode(",\n", php_ini_scanned_files()); echo dirname(reset($f)), "\n";')
echo 'extension=sass.so' > "$iniDir/sass.ini"

cd "$TRAVIS_BUILD_DIR"
