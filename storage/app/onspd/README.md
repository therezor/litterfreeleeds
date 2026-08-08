# ONS Postcode Directory

Drop an extracted ONSPD release in here — it is the default source for
`php artisan postcodes:import`.

Download a release from <https://geoportal.statistics.gov.uk/search?tags=onspd> (the
download URL is versioned per quarterly release, so there is nothing to automate) and
unzip it here, keeping its own folder structure. Extract the **whole** zip — `Documents/`
holds the lookups that turn ONS codes into ward and city names, and without it picks get
codes but no names:

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
vendor/bin/sail artisan postcodes:import
```

The contents of this directory are git-ignored — the CSV is around 1GB.
