# 🚀 Hypersender Transportation Management App

A Laravel 11 + Filament 3.0 application built for the **Hypersender Laravel Engineer Challenge**.

---

## 📦 Quick Setup

```bash
git clone https://github.com/AsmaaShAli/hypersender-filament
cd hypersender-filament

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## 🏗️ What It Does
- Companies → manage Drivers, Vehicles, Trips.

- Drivers ↔ Vehicles (many-to-many) via Trips.

- Trips → assign a driver + vehicle in a time window.


## ✅ Business rules

- No overlapping trips (driver or vehicle).

- Trips must have a valid min/max duration.

- Manager dashboard with KPIs:

    - Active trips right now

    - Available drivers

    - Available vehicles

    - Trips completed this month

- Availability checker → see free drivers/vehicles in a time range.

## 🎨UI Enhancements

- Sidebar navigation for resources.

- Topbar badges for KPIs.

- Quick actions:

    - 🔍 Availability Checker
    
    - ➕ New Trip 
  
## ⚡ Performance & Reliability

- Caching for KPIs & dropdowns (via StatsCache).

- Cache invalidation via Trip observer.

- Eager loading to avoid N+1 issues.

## ✅ Testing

- Written with PestPHP

- Covers:

  - Overlap validation

  - Trip duration validation

  - KPI calculations

  - Core model relationships

Run
```bash 
  php artisan test
```

## 🔑 Assumptions

- Min trip duration = 30 minutes.

- Max trip duration = 12 hours. (maximum working hours for a single driver.)

- Back-to-back trips are allowed.
