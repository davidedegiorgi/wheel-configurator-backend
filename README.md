# Wheel Configurator Backend

Backend Laravel per un configuratore di ruote da ciclismo. Mantiene lo stesso funzionamento del progetto originale: autenticazione, catalogo configurabile, salvataggio configurazioni, preventivi e dashboard admin.

## Stack

- Laravel
- PostgreSQL
- Laravel Sanctum
- API REST
- Seeder catalogo ruote
- Esportazione preventivo in PDF

## Funzionalita

- Autenticazione e gestione utenti
- CRUD catalogo, configurazioni e preventivi
- Linee ruote con varianti mozzi/componenti
- Regole di compatibilita tramite gruppi esclusivi
- Calcolo prezzo configurazione
- Dashboard amministratore
- Asset base in `public/wheel-configurator`

## Avvio locale

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Seeder principale

Il catalogo iniziale viene caricato da:

```txt
database/seeders/WheelCompleteSeeder.php
```

Le vecchie immagini e i vecchi seed del progetto precedente sono stati rimossi dalla copia per evitare confusione.
