# Copilot Instructions - Insurance Management System

You are top 1% Senior PHP Developer in the world and fully understand about insurance finance.

## Project Overview

This is a Laravel 11 insurance management system handling contracts, accounting, and financial reporting for property and automobile insurance. The system uses UUIDs/ULIDs for primary keys and follows Indonesian business practices.

## Architecture Patterns

### Model Conventions

- **UUIDs/ULIDs**: Most models use `HasUuids` or `HasUlids` traits instead of auto-incrementing IDs
- **Audit Fields**: Models track `created_by` and `updated_by` with User relationships
- **Formatted Attributes**: Financial models append formatted versions (e.g., `amount_formatted` using Indonesian number format: `number_format($value, 2, ",", ".")`)
- **Display Names**: Key models like `ChartOfAccount` append `display_name` combining code and name: `"$code - $name"`

### Controller Structure

- **Dual Controllers**: Web controllers for views (`app/Http/Controllers/`) and API controllers (`app/Http/Controllers/Api/`) with identical naming
- **DataTables Integration**: API controllers have `datatables()` methods returning Yajra DataTables responses
- **Select2 Support**: Many API controllers have `select2()` methods for dropdown data
- **Route Grouping**: Routes grouped by function: `master/`, `transaction/`, `report/`

### Financial Domain

- **Chart of Accounts**: Account codes with categories, balance types (debit/credit)
- **Contract Management**: Insurance contracts with property/automobile units, premium calculations with discounts
- **Billing & Collections**: DebitNote → CreditNote/PaymentAllocation flow for receivables
- **Financial Reports**: Balance Sheet, P&L, Cash Flow, Piutang (Receivables) reports

## Key Dependencies

- **Livewire 3.6**: Used for interactive financial reports (`app/Livewire/Report/`)
- **Yajra DataTables**: Standard for all data listings with server-side processing
- **Maatwebsite Excel**: Export functionality via dedicated Export classes (`app/Exports/`)
- **Laravel Sanctum**: API authentication

## Development Workflow

### Local Development

```bash
# Laragon-based development (Windows)
# Database: MySQL on port 3307
# Application runs on standard Laragon Apache/Nginx setup
```

### Docker Setup

```bash
# Production containerization available
docker-compose up -d  # MySQL on port 3307, Nginx on port 8000
```

### Asset Building

```bash
npm run dev    # Development with Vite HMR
npm run build  # Production build
```

## Database Patterns

- **Soft Deletes**: Not used - hard deletes preferred
- **Timestamps**: Standard Laravel `created_at`/`updated_at` on all tables
- **Foreign Keys**: Follow Laravel conventions with proper cascade rules
- **Indexing**: Account codes, contract numbers, and reference fields are indexed

## API Conventions

- **Consistent Endpoints**: GET (index/datatables), POST (store), PUT (update)
- **Response Format**: JSON responses for `format=json`, Excel downloads for `format=excel`
- **Filtering**: Date ranges, client filters using `when()` query conditionals
- **Relationships**: Eager loading with `with()` for performance

## Financial Calculations

- **Currency Handling**: Multi-currency support with exchange rates stored on contracts
- **Premium Calculations**: `gross_premium - (gross_premium * discount/100) + stamp_fee = amount`
- **Outstanding Amounts**: `debit_amount - credit_notes - payment_allocations`
- **As-of-Date Reports**: All calculations respect report date parameters using Carbon date comparisons

## File Naming

- **Models**: Singular PascalCase (`ChartOfAccount`, `DebitNote`)
- **Controllers**: Match model name + "Controller" (`ChartOfAccountController`)
- **Views**: kebab-case directories matching routes (`master/chart-of-accounts/index.blade.php`)
- **Exports**: ModelName + "Export" (`PiutangReportExport`)

## Report Generation

- **Livewire Reports**: Interactive reports with real-time filtering in `app/Livewire/Report/`
- **Excel Exports**: Separate Export classes using Maatwebsite Excel package
- **PDF Reports**: Not currently implemented - Excel is primary export format
- **Report APIs**: Dual endpoints - view rendering and JSON/Excel responses

## Testing

- **PHPUnit**: Standard Laravel testing setup (currently minimal test coverage)
- **Feature Tests**: Focus on API endpoints and critical business logic
- **Database**: Uses SQLite for testing (`database/database.sqlite`)


## **Aplikasi Manajemen Asuransi (Insurance Management System)**

Ini adalah sistem manajemen asuransi berbasis web yang dibangun menggunakan Laravel 11. Aplikasi ini dirancang untuk mengelola bisnis asuransi properti dan kendaraan bermotor dengan fitur akuntansi dan pelaporan keuangan yang lengkap.

## **Fitur-Fitur Utama:**

### **1. Master Data Management**

* **Chart of Accounts (Bagan Akun)** : Mengelola kode-kode akun dengan kategori dan tipe saldo
* **Contact Management** : Mengelola data kontak klien dengan grup kontak
* **Contact Groups** : Pengelompokan kontak berdasarkan kategori

### **2. Transaction Management**

* **Cash Bank** : Pengelolaan transaksi kas dan bank
* **Insurance Contracts** :
* Kontrak asuransi untuk properti dan kendaraan
* Perhitungan premi dengan diskon dan biaya materai
* Multi-currency dengan nilai tukar
* Periode coverage dan installment
* **Journal Entries** : Pencatatan jurnal akuntansi
* **Debit Notes** : Tagihan kepada klien dengan sistem billing
* **Credit Notes** : Nota kredit untuk pengurangan tagihan
* **Payment Allocations** : Alokasi pembayaran dari klien

### **3. Financial Reporting**

* **Balance Sheet (Neraca)** : Laporan posisi keuangan
* **Profit & Loss (Laba Rugi)** : Laporan pendapatan dan biaya
* **Cash Flow** : Laporan arus kas
* **Piutang Report** : Laporan outstanding receivables dengan detail:
* Outstanding amount per client
* Credit notes applied
* Payment allocations
* As-of-date calculations
