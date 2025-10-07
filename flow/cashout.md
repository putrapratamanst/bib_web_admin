## 🏦 **Sistem Cashout - Fitur Baru untuk Manajemen Pembayaran Broker ke Asuransi**

### **1. Database & Model Layer**

**Tabel Cashouts:**

* Menyimpan data pembayaran dari broker ke perusahaan asuransi
* Field utama: `cashout_number`, `cashout_date`, `due_date`, `amount`, `status`
* Relasi ke `debit_notes`, `contacts` (insurance), `contracts`, `journal_entries`
* Status tracking: Pending → Paid → Cancelled

**Model Cashout:**

* Menggunakan UUID sebagai primary key
* Format nomor otomatis (CSH-YYYY-MM-XXXXXX)
* Format mata uang Indonesia (1.234.567,89)
* Audit trail dengan `created_by` dan `updated_by`

### **2. Business Logic & Controllers**

**Auto-Posting dari Debit Note:**

* []()
* []()
* []()
* []()

**Web Controller (`CashoutController`):**

* CRUD lengkap untuk manajemen cashout
* Update status pembayaran
* Integrasi dengan journal entries

**API Controller (`Api\CashoutController`):**

* DataTables server-side processing
* Select2 dropdown data
* JSON responses untuk AJAX

### **3. Journal Entry Integration**

**Auto-Create Journal Entries:** Ketika cashout dibayar, otomatis buat jurnal:

Dr. Insurance Payable    [Amount]
    Cr. Cash/Bank            [Amount]

**Integrasi Chart of Accounts:**

* Menggunakan account codes yang sudah ada
* Proper debit/credit posting
* Tracking reference ke cashout

### **4. User Interface (Frontend)**

**Halaman Cashout Management:**

* Listing dengan DataTables dan pagination
* Filter berdasarkan tanggal, insurance, status
* Modal forms untuk create/edit
* Status badges dengan warna (Pending=kuning, Paid=hijau, Cancelled=merah)
* Bulk actions dan export Excel

**Features UI:**

* Search dan filter real-time
* Responsive design untuk mobile
* Loading states dengan SweetAlert
* Form validation dengan error handling

### **5. Reporting System**

**Cashout Report (`/report/cashout`):**

* **Summary Cards** : Total records, breakdown by status
* **Filtering** : Tanggal, perusahaan asuransi, status
* **Detail Table** : Cashout number, dates, amounts, status
* **Export Excel** : Format professional dengan formatting Indonesia

**Cashout Reconciliation Report (`/report/cashout-reconciliation`):**

* **Reconciliation Logic** : Matching cashout vs journal entries
* **Variance Detection** : Identify amount differences
* **Summary Metrics** : Total, matched, unmatched, variance %
* **Detailed Analysis** : Per-item reconciliation status

### **6. Business Flow Yang Diotomasikan**

**Alur Pembayaran:**

1. **Customer bayar ke broker** → DebitNote dibuat
2. **DebitNote di-post** → Cashout otomatis dibuat (status: Pending)
3. **Broker bayar ke asuransi** → Update cashout ke status Paid
4. **Journal entry otomatis** → Pencatatan akuntansi yang benar
5. **Reporting tersedia** → Tracking dan reconciliation

### **7. Menu Navigation**

**Transaction Menu:**

* Tambahan menu "Cashouts" dengan icon money-bill-wave
* Quick access ke manajemen cashout

**Report Menu:**

* "Cashout Report" untuk laporan umum
* "Cashout Reconciliation" untuk reconciliation analysis

### **8. Technical Features**

**Multi-Currency Support:**

* Mendukung berbagai mata uang
* Format display sesuai locale Indonesia
* Exchange rate tracking

**Indonesian Formatting:**

* Angka: 1.234.567,89 (titik ribuan, koma desimal)
* Tanggal: DD/MM/YYYY format
* Mata uang: IDR prefix

**Performance Optimization:**

* Eager loading relationships
* Indexed database fields
* Server-side pagination

### **9. Security & Audit**

**Audit Trail:**

* Semua perubahan tracked dengan user info
* Created/updated timestamps
* User permissions untuk edit/delete

**Data Validation:**

* Required field validation
* Amount validation (positive numbers)
* Date validation (logical date ranges)

---

## **Manfaat untuk Bisnis:**

1. **Otomatisasi** : Mengurangi manual entry dan human error
2. **Tracking** : Visibility penuh atas outstanding payments ke asuransi
3. **Reconciliation** : Memastikan akurasi pencatatan keuangan
4. **Reporting** : Insight untuk cash flow management
5. **Compliance** : Proper accounting records untuk audit

Sistem ini secara signifikan meningkatkan efisiensi operasional broker dengan mengotomatisasi tracking pembayaran ke perusahaan asuransi sambil memastikan akurasi pencatatan akuntansi.
