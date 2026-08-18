FROM php:8.2-apache

# تفعيل الموديلات المطلوبة
RUN a2enmod rewrite

# تسطيب الإضافات الخاصة بقاعدة البيانات
RUN docker-php-ext-install pdo pdo_mysql mysqli

# حقن كود التوجيه مباشرة داخل إعدادات أباتشي بصيغة لينكس نقية متجاوزين الويندوز تماماً
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf && \
  echo '    DocumentRoot /var/www/html' >> /etc/apache2/sites-available/000-default.conf && \
  echo '    <Directory /var/www/html>' >> /etc/apache2/sites-available/000-default.conf && \
  echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
  echo '        FallbackResource /index.php' >> /etc/apache2/sites-available/000-default.conf && \
  echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf && \
  echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# نسخ الملفات وإصلاح الصلاحيات
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80