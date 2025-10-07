# Dokumentasi Implementasi Cashout - Complete

## Overview
Fitur cashout telah diimplementasi secara lengkap untuk menangani alur uang keluar dari broker ke insurance company ketika debit note diposting, termasuk UI management, journal entries integration, dan reporting.

## Struktur Database

### Table: cashouts
```sql
- id (UUID, Primary Key)
- debit_note_id (UUID, Foreign Key ke debit_notes)
- insurance_id (UUID, Foreign Key ke contacts - insurance company)
- number (String, Nomor cashout)
- date (Date, Tanggal cashout)
- due_date (Date, Tanggal jatuh tempo)
- currency_code (String, Kode mata uang)
- exchange_rate (Decimal, Nilai tukar)
- amount (Decimal, Jumlah yang harus dibayar ke insurance)
- description (Text, Deskripsi cashout)
- status (Enum: pending, paid, cancelled)
- created_by, updated_by (User IDs)
- timestamps
```

## Flow Bisnis Lengkap

### 1. Posting Debit Note
**Endpoint**: `POST /api/debit-note/{id}/post`

**Process**:
1. **Validasi**: 
   - Debit note harus status `active`
   - Belum pernah di-posting sebelumnya
   - Harus memiliki `debit_note_details` (pembagian ke insurance)

2. **Auto Create Cashouts**:
   - Untuk setiap record di `debit_note_details`
   - Dibuat 1 cashout per insurance company
   - Status default: `pending`
   - Nomor otomatis: `CO/YYYYMMDD/0001`

3. **Auto Journal Entry (Provision)**:
   - **Debit**: Insurance Expense Account
   - **Credit**: Insurance Payable Account
   - Purpose: Record provision untuk pembayaran ke insurance

### 2. Management Cashouts

#### Web Interface
- **List**: `/transaction/cashouts` - List semua cashouts dengan filtering
- **Detail**: `/transaction/cashouts/{id}` - Detail cashout dengan action buttons
- **Actions**: Mark as Paid, Cancel

#### API Interface
- **DataTables**: `/api/cashout/datatables` - Server-side processing
- **Mark Paid**: `/api/cashout/{id}/mark-paid`
- **Mark Cancelled**: `/api/cashout/{id}/mark-cancelled`

### 3. Auto Journal Entries

#### When Cashout Created (Pending)
```
Dr. Insurance Expense          XXX
    Cr. Insurance Payable          XXX
```

#### When Cashout Paid
```
Dr. Insurance Payable          XXX
    Cr. Cash/Bank                  XXX
```

### 4. Reporting System

#### Cashout Report
**Endpoint**: `/api/report/cashout`
- **Web UI**: `/report/cashout`
- **Features**:
  - Filter by date range, insurance company, status
  - Summary by status (pending, paid, cancelled)
  - Excel export functionality

#### Cashout Reconciliation
**Endpoint**: `/api/report/cashout-reconciliation`
- **Web UI**: `/report/cashout-reconciliation`
- **Features**:
  - Group by insurance company
  - Outstanding amounts calculation
  - As-of-date reporting
  - Excel export with summary

## API Endpoints Reference

### Debit Note
```
GET /api/debit-note/{id}
- Detail debit note dengan info cashouts

POST /api/debit-note/{id}/post
- Posting debit note dan auto create cashouts
```

### Cashout Management
```
GET /api/cashout
- List cashouts

GET /api/cashout/datatables  
- DataTables untuk UI

GET /api/cashout/{id}
- Detail cashout

PUT /api/cashout/{id}
- Update cashout

POST /api/cashout/{id}/mark-paid
- Mark sebagai paid (auto create journal)

POST /api/cashout/{id}/mark-cancelled
- Mark sebagai cancelled
```

### Reporting
```
GET /api/report/cashout
- Cashout report dengan filtering
- Parameters: from_date, to_date, insurance_id, status
- Formats: json, excel

GET /api/report/cashout-reconciliation
- Reconciliation report by insurance
- Parameters: as_of_date, insurance_id
- Formats: json, excel
```

## Web Routes Reference

### Transaction Management
```
GET /transaction/cashouts
- List cashouts dengan DataTables

GET /transaction/cashouts/{id}
- Detail cashout dengan action buttons

POST /transaction/cashouts/{id}/mark-paid
- Web form untuk mark as paid

POST /transaction/cashouts/{id}/mark-cancelled
- Web form untuk cancel cashout
```

### Reporting
```
GET /report/cashout
- Cashout report interface

GET /report/cashout-reconciliation
- Reconciliation report interface
```

## Model Relationships & Features

### DebitNote Model
- `hasMany(Cashout::class)` - Cashouts yang dibuat dari posting
- `getIsPostedAttribute()` - Check apakah sudah di-posting
- `getCashoutAmountAttribute()` - Total amount cashouts
- `postDebitNote()` - Method untuk posting
- **Appends**: `is_posted` untuk UI checks

### Cashout Model
- `belongsTo(DebitNote::class)` - Debit note asal
- `belongsTo(Contact::class, 'insurance_id')` - Insurance company
- `morphMany(JournalEntry::class)` - Related journal entries
- **Auto Events**: Create journal entries on create/update
- **Scopes**: `pending()`, `paid()`, `byInsurance()`, `byDebitNote()`
- **Formatted Attributes**: Indonesian number format

### Journal Integration
- **Auto Creation**: Journal entries created otomatis
- **Event Driven**: Laravel model events (created, updated)
- **Account Mapping**: Auto detect COA berdasarkan naming pattern

## Export Classes

### CashoutReportExport
- **Purpose**: Export detailed cashout data
- **Format**: Excel dengan headers dan mapping
- **Includes**: All cashout details + summary

### CashoutReconciliationExport
- **Purpose**: Export reconciliation by insurance
- **Format**: Excel dengan summary per insurance
- **Includes**: Outstanding amounts, status breakdown

## Frontend Features

### Debit Note Detail
- **Posting Button**: Conditional display berdasarkan status
- **AJAX Posting**: Real-time feedback dengan SweetAlert
- **Cashouts Link**: Quick access ke cashouts setelah posting

### Cashout List
- **DataTables**: Server-side processing dengan search/filter
- **Status Badges**: Visual status indicators
- **Action Buttons**: Context-aware actions (paid/cancel)
- **Responsive**: Mobile-friendly layout

### Cashout Detail
- **Complete Info**: All related data (DN, contract, client, insurance)
- **Status Management**: In-page action buttons
- **Audit Trail**: Created/updated by information
- **Navigation**: Easy navigation between related records

## Security & Validation

### API Validation
- **Status Checks**: Proper status validation before actions
- **Existence Checks**: Validate records exist before operations
- **Business Rules**: Prevent double posting, invalid status changes

### Authentication
- **User Tracking**: Created/updated by fields
- **CSRF Protection**: Web forms protected
- **API Headers**: Proper authentication headers

## Usage Examples

### 1. Complete Posting Flow
```javascript
// 1. Post Debit Note
POST /api/debit-note/DN001/post
Response: {
    "success": true,
    "message": "Debit Note berhasil di-posting. Cashout telah dibuat otomatis.",
    "data": {
        "cashouts_count": 2
    }
}

// 2. View Created Cashouts
GET /api/cashout?debit_note_id=DN001

// 3. Mark Cashout as Paid
POST /api/cashout/CO001/mark-paid
Response: {
    "message": "Cashout marked as paid"
}
```

### 2. Reporting Usage
```javascript
// Cashout Report with filters
GET /api/report/cashout?from_date=2025-01-01&to_date=2025-01-31&format=json

// Reconciliation as of date
GET /api/report/cashout-reconciliation?as_of_date=2025-01-31&format=excel
```

## Performance Considerations

### Database Optimization
- **Indexes**: Proper indexing on foreign keys dan status
- **Eager Loading**: Relationships loaded dengan `with()`
- **Chunking**: For large data exports

### Caching
- **COA Lookup**: Chart of accounts dapat di-cache
- **Number Generation**: Sequence generation optimized

## Error Handling

### API Responses
- **Consistent Format**: Standard success/error responses
- **Detailed Messages**: User-friendly error messages
- **HTTP Status**: Proper HTTP status codes

### Frontend
- **SweetAlert**: User-friendly notifications
- **Form Validation**: Client-side dan server-side validation
- **Loading States**: Visual feedback during operations

## Monitoring & Logging

### Audit Trail
- **Created/Updated By**: Track user actions
- **Timestamps**: Full audit timestamps
- **Status Changes**: Track status change history

### Journal Entries
- **Automatic Tracking**: All cashout transactions recorded
- **Reference Fields**: Easy traceability
- **Consistent Numbering**: Sequential journal numbers

## Next Development Ideas

1. **Email Notifications**: Notify insurance companies of cashouts
2. **Payment Integration**: Connect with payment gateways
3. **Approval Workflow**: Multi-level approval for large amounts
4. **Dashboard**: Real-time cashout metrics
5. **Mobile App**: Mobile interface for field agents
6. **API Integration**: Connect dengan insurance company systems