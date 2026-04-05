# Platformă Viziere Digitale (Digital Signage)

Această platformă permite gestionarea și afișarea conținutului multimedia (imagini și videoclipuri) pe televizoare Android, tablete sau orice dispozitiv cu browser, organizat pe canale (ex: Scara A, Scara B).

## Caracteristici Principală

- **Panou Administrare Modern:** Interfață "Glassmorphism" cu bară laterală și design responsiv.
- **Gestionare Canale:** Creare, ștergere și redenumire canale nelimitate.
- **Configurare per Canal:**
    - Încărcare logo personalizat (afișat în colțul stânga-sus).
    - Mesaj "Live Ticker" (text rulant în partea de sus).
    - Previzualizare live direct din admin.
- **Gestionare Media:**
    - Încărcare imagini (JPG, PNG, WEBP, GIF).
    - Încărcare videoclipuri (MP4, WebM).
    - Reordonare prin Drag-and-Drop.
    - Setare durată individuală pentru imagini.
- **Interfață Player (TV/Tabletă):**
    - Mod Full-screen cu temă premium întunecată.
    - Tranziții fluide (cross-fade) între elemente.
    - Selector de canale flotant pentru schimbarea rapidă a conținutului.
    - Sincronizare automată (verifică actualizările la fiecare 30 secunde fără refresh).

## Instalare

1. Încărcați fișierele pe serverul web (PHP + SQLite activat).
2. Accesați `install.php` în browser.
3. Configurați datele administratorului.
4. **IMPORTANT:** După instalare, ștergeți fișierul `install.php` pentru securitate.

## Tehnologii Utilizate

- **Backend:** PHP (Procedural) + SQLite (PDO).
- **Frontend:** Tailwind CSS, Font Awesome, SortableJS.
- **Securitate:** CSRF Protection, Password Hashing, Session Management.

---
Creat pentru viziere digitale moderne.
