# Create full copy of dev DB and insert it to test DB
USER="root"
PASSWORD=""

mysqldump --force --opt --user=$USER cms > "temp.sql"
mysql -u$USER cms_test < "temp.sql"
rm "temp.sql"
