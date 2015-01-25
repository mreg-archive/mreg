#!/bin/bash 

# build html help to jsclient
markdown JSCLIENT.md > ../www/jsclient/tmpl/HELP.html

# build pdf for http download
#pandoc --variable mainfont=Georgia --variable sansfont=Arial --variable monofont="Bitstream Vera Sans Mono" --variable fontsize=12pt JSCLIENT.md -o ../www/static/docs/manual.pdf
#pandoc --variable mainfont=Georgia --variable sansfont=Arial --variable monofont="Bitstream Vera Sans Mono" --variable fontsize=12pt ECONOMY.md -o ../www/static/docs/ekonomi.pdf

# build pdf of all documents
mkdir build
cp rapporter/rapport-20120631.md build/README.md
cat README.md >> build/README.md
cat DB.md >> build/README.md
cat REST.md >> build/README.md
cat SECURITY.md >> build/README.md
cat JSCLIENT.md >> build/README.md
cat ECONOMY.md >> build/README.md
pandoc -N --variable mainfont=Georgia --variable sansfont=Arial --variable monofont="Bitstream Vera Sans Mono" --variable fontsize=12pt build/README.md --toc -o README.pdf
rm -r build
