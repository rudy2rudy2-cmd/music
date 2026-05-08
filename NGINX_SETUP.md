# Configurare NGINX pentru Laravel & Filament

Dacă primești **404 Not Found** pe paginile `/admin`, asigură-te că fișierul de configurare Nginx pentru site-ul tău conține următoarele setări:

## 1. Setează Root-ul către folderul /public
Este foarte important ca `root` să puncteze către folderul `public` din interiorul proiectului, nu către folderul rădăcină al proiectului.

```nginx
server {
    listen 80;
    server_name music.sglprime.com;
    root /www/wwwroot/music.sglprime.com/public; # <--- ASIGURĂ-TE CĂ ARE /public LA FINAL

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

## 2. Permisiuni foldere
Dacă apar erori după configurare, rulează aceste comenzi în terminalul serverului (în folderul proiectului):

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data . # Sau userul folosit de serverul tău web (ex: www)
```

## 3. Restart Nginx
După ce modifici configurația, nu uita să dai restart la Nginx:
```bash
nginx -t
service nginx restart
```
