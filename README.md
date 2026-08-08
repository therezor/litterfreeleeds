# Litter Free Leeds App

Connecting and empowering the litter picking community in Leeds.

## About the Project

This application is designed to support the **Litter Free Leeds** community. Litter Free Leeds is a vibrant network of over 50 groups and thousands of volunteers who go out regularly to keep the City of Leeds clean and safe for everyone, including our local wildlife.

### Key Community Highlights
- **Active Volunteers:** Thousands of people across Leeds, from dedicated groups to individuals picking up litter on their regular walks.
- **Widespread Impact:** More than 50 active groups covering the entire city.
- **The Purple Bag:** Wherever you see a purple bag, it represents the hard work of a volunteer making a difference.
- **Wildlife Protection:** Our mission helps protect local wildlife from the dangers of littered items in their natural habitats.
- **Inclusivity:** Whether you have five minutes or five hours, every bag counts.

For more information about the community, visit [litterfreeleeds.co.uk](https://litterfreeleeds.co.uk/).

## Features (Built with Laravel & Filament)

This app provides a management platform for:
- Tracking cleanup activities and "purple bag" collections.
- Managing community cleanup groups and volunteer coordination.
- Locating resources like "Can Cages" and equipment distribution points.
- Stay updated with community news and upcoming picks.

## Getting Started

### Prerequisites
- PHP 8.5+
- Composer
- Docker (for Laravel Sail)
- PostgreSQL

### Installation

1. Clone the repository:
   ```bash
   git clone git@github.com:therezor/litterfreeleeds.git
   cd litterfreeleeds
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install && npm run dev
   ```

3. Set up environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Run with Docker (Sail):
   ```bash
   ./vendor/bin/sail up -d
   vendor/bin/sail artisan migrate
   vendor/bin/sail artisan db:seed
   ```

   `db:seed` loads the Shield roles/permissions and a small set of Leeds postcodes, so
   picks can be created straight away, and makes `test@example.com` a Super Admin locally. Tests must run through Sail — `.env` points at the
   `pgsql` container hostname, which does not resolve from the host.

## Postcode data

Pick locations are geocoded offline from the **ONS Postcode Directory (ONSPD)** — there are
no runtime geocoding API calls. A pick cannot be saved unless its postcode exists in the
`postcodes` table, so that table must be populated first.

`database/seeders/LeedsPostcodeSeeder.php` covers a handful of Leeds postcodes with
approximate coordinates for development. For real data, download a release from
[the ONS Geoportal](https://geoportal.statistics.gov.uk/search?tags=onspd) and unzip it into
`storage/app/onspd` — the directory ships with the repo and its contents are git-ignored,
since the CSV is around 1GB:

Extract the **whole** zip, not just the data file — the `Documents/` folder holds the
lookups that turn ONS codes into ward and city names:

```
storage/app/onspd/
├── Data/
│   └── ONSPD_MAY_2026_UK.csv
└── Documents/
    ├── WD Ward names and codes UK as at 05_25.csv
    └── LAD Local Authority District names and codes UK as at 04_25.csv
```

Then:

```bash
# All UK postcodes — around 1.8 million rows, a few minutes
vendor/bin/sail artisan postcodes:import

# Just the Leeds region, for a fast local import
vendor/bin/sail artisan postcodes:import --only=LS,BD,WF,HD,HX

# Drop postcodes ONS has since retired
vendor/bin/sail artisan postcodes:import --fresh

# Or point at a CSV somewhere else
vendor/bin/sail artisan postcodes:import /path/to/ONSPD_MAY_2026_UK.csv
```

ONSPD is released quarterly; re-running the import is idempotent. Column names change
between releases — May 2026 renamed `osward`/`oslaua` to `wd25cd`/`lad25cd` — so the
importer matches every column by name pattern and warns loudly if one goes missing. Adding a new Filament
resource means re-running `shield:generate`, then `shield:seeder --force` to refresh the
committed `ShieldSeeder`.

## Wards and districts

Every pick is placed automatically from its postcode — nothing is assigned by hand:

- **Ward** — the electoral ward.
- **District** — the local authority, i.e. the town or city.

They are shown together, labelled **Area** throughout the UI: "Headingley & Hyde Park,
Leeds", "Shipley, Bradford". In code they stay `ward` and `district`, matching ONS.

Each pick's page also shows a Google map of the meeting point. Set `GOOGLE_MAPS_API_KEY`
to use the official Embed API; without one it falls back to Google's keyless embed, which
works but is not a documented endpoint — set a key before going live.

Both are read *through* the postcode rather than copied onto the pick, so they always
reflect the current ONSPD release and `postcodes:import` never writes to the picks table.
Picks show no ward until an ONSPD release has been imported — the seeded Leeds postcodes
carry coordinates only.

Note the model is `CommunityPick` and the admin panel calls it "Community Picks"; the public
site keeps the `/upcoming-picks` URL and the "Upcoming Picks" wording.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
