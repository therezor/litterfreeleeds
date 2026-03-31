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
- PHP 8.2+
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
   php artisan migrate
   ```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
