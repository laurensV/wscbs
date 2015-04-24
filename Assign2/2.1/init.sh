apt-get -y install apache2
apt-get -y install php5 libapache2-mod-php5
apt-get -y install php5-sqlite
ssh-keyscan github.com >> /root/.ssh/known_hosts
ssh-keyscan github.com >> /home/ubuntu/.ssh/known_hosts
git clone https://github.com/laurensV/wscbs.git /home/ubuntu/wscbs
rm /var/www/html/index.html
cp /home/ubuntu/wscbs/Assign1/REST/index.php /var/www/html/
cp /home/ubuntu/wscbs/Assign1/REST/.htaccess /var/www/html/
a2enmod rewrite
touch /etc/apache2/conf-enabled/rewrite-mod.conf
echo 'extension=sqlite3.so' >> /etc/php5/apache2/php.ini
{
  echo '<Directory /var/www/>'
  echo 'Options Indexes FollowSymLinks'
  echo 'AllowOverride All'
  echo 'Require all granted'
  echo '</Directory>'
} >/etc/apache2/conf-enabled/rewrite-mod.conf
/etc/init.d/apache2 restart
chown www-data /var/www/html/