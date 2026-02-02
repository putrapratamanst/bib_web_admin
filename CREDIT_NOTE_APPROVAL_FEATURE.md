# Credit Note & Debit Note Approval Feature

## Overview
This feature implements a role-based approval workflow for both Credit Notes and Debit Notes. All Credit Notes and Debit Notes must be approved by users with "approver" role before they can be printed or exported.

## User Roles
The system supports two user roles:
- **admin**: Default role, can perform all actions except Credit Note approval
- **approver**: Can approve/reject Credit Notes in addition to all admin functions

## Authorization
- Only users with `role = 'approver'` can approve or reject Credit Notes and Debit Notes
- Other users will see approval status but cannot perform approval actions
- API endpoints return 403 Forbidden for unauthorized approval attempts

## Approval Status
Both Credit Notes and Debit Notes can have one of three approval statuses:
- **Pending**: Default status when created, cannot be printed
- **Approved**: Approved by authorized user, can be printed/exported
- **Rejected**: Rejected by authorized user, cannot be printed

## Database Changes
New fields added to both `credit_notes` and `debit_notes` tables:
- `approval_status` (enum): 'pending', 'approved', 'rejected'
- `approved_by` (bigint): User ID who performed approval/rejection
- `approved_at` (timestamp): When approval/rejection happened
- `approval_notes` (text): Optional notes for approval/rejection

## API Endpoints

### Approve Credit Note
`POST /api/credit-note/{id}/approve`

**Authorization:** Requires user with 'approver' role

**Request Body:**
```json
{
    "notes": "Optional approval notes"
}
```

**Response (Success):**
```json
{
    "message": "Credit Note has been approved successfully.",
    "data": {
        // CreditNote resource with updated approval status
    }
}
```

**Response (Unauthorized):**
```json
{
    "message": "You are not authorized to approve Credit Notes. Only users with approver role can perform this action."
}
```

### Reject Credit Note
`POST /api/credit-note/{id}/reject`

**Authorization:** Requires user with 'approver' role

**Request Body:**
```json
{
    "notes": "Required rejection reason"
}
```

**Response (Success):**
```json
{
    "message": "Credit Note has been rejected.",
    "data": {
        // CreditNote resource with updated approval status
    }
}
```

**Response (Unauthorized):**
```json
{
    "message": "You are not authorized to reject Credit Notes. Only users with approver role can perform this action."
}
```

## Debit Note API Endpoints

### Approve Debit Note
`POST /api/debit-note/{id}/approve`

**Authorization:** Requires user with 'approver' role

**Request Body:**
```json
{
    "notes": "Optional approval notes"
}
```

**Response (Success):**
```json
{
    "message": "Debit Note has been approved successfully.",
    "data": {
        // DebitNote resource with updated approval status
    }
}
```

**Response (Unauthorized):**
```json
{
    "message": "You are not authorized to approve Debit Notes. Only users with approver role can perform this action."
}
```

### Reject Debit Note
`POST /api/debit-note/{id}/reject`

**Authorization:** Requires user with 'approver' role

**Request Body:**
```json
{
    "notes": "Required rejection reason"
}
```

**Response (Success):**
```json
{
    "message": "Debit Note has been rejected.",
    "data": {
        // DebitNote resource with updated approval status
    }
}
```

**Response (Unauthorized):**
```json
{
    "message": "You are not authorized to reject Debit Notes. Only users with approver role can perform this action."
}
```

## Model Methods

### User Model
- `isApprover()`: Returns true if user role is 'approver'
- `canApproveCreditNotes()`: Returns true if user can approve credit notes

### CreditNote Model
- `canBePrinted()`: Returns true if approval_status is 'approved'
- `canBeApproved()`: Returns true if approval_status is 'pending'
- `getApprovalStatusBadgeAttribute()`: Returns HTML badge for status display
- `getApprovedAtFormattedAttribute()`: Returns formatted approval datetime

### DebitNote Model
- `canBePrinted()`: Returns true if approval_status is 'approved'
- `canBeApproved()`: Returns true if approval_status is 'pending'
- `getApprovalStatusBadgeAttribute()`: Returns HTML badge for status display
- `getApprovedAtFormattedAttribute()`: Returns formatted approval datetime

## UI Changes

### Credit Note Pages

#### Index Page (Credit Note List)
- Added "Approval Status" column with colored badges
- Added "Actions" column with approve/reject/print buttons based on status and user role
- Approve/Reject buttons only visible for users with 'approver' role
- JavaScript handlers for approval actions with role-based error handling

#### Detail Page (Credit Note Show)
- Shows current approval status with badge
- Shows approval/rejection information (who, when, notes)
- Print button only available for approved Credit Notes
- Approve/Reject buttons only for users with 'approver' role and pending Credit Notes
- Informational message for non-approver users on pending Credit Notes

### Debit Note Pages

#### Index Page (Debit Note List)
- Added "Approval Status" column with colored badges
- Added "Actions" column with approve/reject/print buttons based on status and user role
- Approve/Reject buttons only visible for users with 'approver' role
- JavaScript handlers for approval actions with role-based error handling

#### Detail Page (Debit Note Show)
- Shows current approval status with badge
- Shows approval/rejection information (who, when, notes)
- Print button only available for approved Debit Notes
- Approve/Reject buttons only for users with 'approver' role and pending Debit Notes
- Informational message for non-approver users on pending Debit Notes

## Permission & Security
- Credit Note and Debit Note approval restricted to users with 'approver' role only
- API endpoints validate user role before allowing approval actions
- UI conditionally displays approval controls based on user role
- Proper error messages for unauthorized attempts
- Audit trail maintained with user information for all approval actions

## Setup Instructions
1. Run migration to add role column to users table:
   ```bash
   php artisan migrate --path=database/migrations/2025_12_15_162002_add_role_to_users_table.php
   ```

2. Run migrations to add approval fields to both tables:
   ```bash
   php artisan migrate --path=database/migrations/2026_02_02_000001_add_approval_fields_to_credit_notes_table.php
   php artisan migrate --path=database/migrations/2026_02_02_000002_add_approval_fields_to_debit_notes_table.php
   ```

3. Run seeder to create approver user and update existing users:
   ```bash
   php artisan db:seed --class=ApproverUserSeeder
   ```

4. Login with approver credentials:
   - Email: approver@example.com
   - Password: password123

## Future Enhancements
- Multiple approval levels (e.g., supervisor, manager)
- Department-based approval routing
- Email notifications for approval actions
- Approval history/audit trail with detailed logs
- Bulk approval functionality
- PDF generation for approved Credit Notes