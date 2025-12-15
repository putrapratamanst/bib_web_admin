# Contract Approval Feature

## Overview
Fitur ini menambahkan sistem approval untuk contract dengan 2 role user: **Admin** dan **Approver**.

## Database Changes

### Contracts Table
Field baru yang ditambahkan:
- `approval_status` (enum: pending, approved, rejected) - default: pending
- `approved_by` (foreign key to users) - user yang melakukan approve/reject
- `approved_at` (timestamp) - waktu approve/reject
- `rejection_reason` (text, nullable) - alasan reject

### Users Table
Field baru yang ditambahkan:
- `role` (enum: admin, approver) - default: admin

## User Roles & Permissions

### Admin Role
- **Create Contract**: Membuat contract baru (status approval otomatis: pending)
- **Edit Contract**: Hanya bisa edit contract dengan status `pending` atau `rejected`
- **View Contract**: Bisa melihat semua contract
- **Cannot**: Approve atau reject contract

### Approver Role
- **Approve Contract**: Approve contract dengan status `pending`
- **Reject Contract**: Reject contract dengan status `pending` dengan memberikan alasan
- **View Contract**: Bisa melihat semua contract
- **Cannot**: Create atau edit contract

## API Endpoints

### Contract Management
```
POST   /api/contract                    - Create contract (Admin only)
PUT    /api/contract/{id}               - Update contract (Admin only, if pending/rejected)
GET    /api/contract/{id}               - Get contract details
```

### Approval Actions
```
POST   /api/contracts/{id}/approve      - Approve contract (Approver only)
POST   /api/contracts/{id}/reject       - Reject contract (Approver only)
       Body: { rejection_reason: "..." }
```

## Web Routes

```
GET    /transaction/contracts           - List all contracts
GET    /transaction/contracts/create    - Form create contract
GET    /transaction/contracts/{id}      - Detail contract
GET    /transaction/contracts/{id}/edit - Edit contract (Admin only)
```

## UI Features

### Detail Contract Page
- **Badge Status**: Menampilkan approval status (pending/approved/rejected) di header
- **Approval Info**: Menampilkan informasi approver dan tanggal approval
- **Rejection Info**: Menampilkan alasan reject jika status rejected
- **Action Buttons**:
  - **Edit Button**: Muncul untuk admin jika status pending/rejected
  - **Approve/Reject Buttons**: Muncul untuk approver jika status pending

### Create/Edit Form
- Saat create: approval_status otomatis set ke `pending`
- Saat edit: approval_status di-reset ke `pending` untuk re-approval

## Business Logic

### Contract Creation
1. Admin membuat contract baru
2. Status approval otomatis: `pending`
3. Contract perlu approval dari approver

### Contract Approval Flow
1. **Pending** → Admin create contract
2. **Approved** → Approver approve contract (tidak bisa diedit lagi)
3. **Rejected** → Approver reject dengan reason (bisa diedit oleh admin)

### Edit Rules
- Admin hanya bisa edit jika approval_status = `pending` atau `rejected`
- Setelah edit, status kembali ke `pending` untuk re-approval
- Contract yang sudah `approved` tidak bisa diedit

## Security
- Authorization check dilakukan di controller level
- API endpoints protected dengan role checking
- View level authorization menggunakan `@auth` dan role checking

## Usage Example

### Set User Role
```sql
-- Set user sebagai admin
UPDATE users SET role = 'admin' WHERE id = 1;

-- Set user sebagai approver
UPDATE users SET role = 'approver' WHERE id = 2;
```

### Workflow
1. Admin login → Create contract → Save (status: pending)
2. Approver login → View contract detail → Click Approve/Reject
3. If Rejected → Admin edit contract → Save (status kembali pending)
4. If Approved → Contract locked, tidak bisa diedit

## Testing
Untuk testing fitur ini:
1. Buat 2 user dengan role berbeda (admin & approver)
2. Login sebagai admin → create contract
3. Login sebagai approver → approve/reject contract
4. Verifikasi admin bisa edit jika rejected, tidak bisa edit jika approved

## Migration Files
- `2025_12_15_161957_add_approval_status_to_contracts_table.php`
- `2025_12_15_162002_add_role_to_users_table.php`

## Updated Files
### Models
- `app/Models/Contract.php` - Added approval fields & approvedBy relationship
- `app/Models/User.php` - Added role field

### Controllers
- `app/Http/Controllers/Api/ContractController.php` - Added approve, reject, update methods
- `app/Http/Controllers/Transaction/ContractController.php` - Added edit method

### Views
- `resources/views/transaction/contract/show.blade.php` - Added approval UI & buttons

### Routes
- `routes/api.php` - Added approval endpoints
- `routes/web.php` - Added edit route
