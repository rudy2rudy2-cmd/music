# Configurare NGINX - Marketing Platform

Dacă primești **404 Not Found** sau erori de fișiere care lipsesc, folosește această configurație:

## 1. Configurație Recomandată
Aceasta este configurația standard care ar trebui să funcționeze indiferent dacă root-ul este în folderul principal sau în cel `/public`.

```nginx
server {
    listen 80;
    server_name music.sglprime.com;
    root /www/wwwroot/music.sglprime.com; # Root-ul proiectului

    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/tmp/php-cgi-82.sock; # Sau versiunea ta de PHP
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## 2. Permisiuni obligatorii
Rulează aceste comenzi pentru a te asigura că platforma poate scrie fișierele necesare:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www:www . # Schimbă 'www' cu userul serverului tău (ex: www-data)
```

## 3. Restart Nginx
```bash
nginx -t
service nginx restart
```
