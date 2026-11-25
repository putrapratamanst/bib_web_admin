-- Run once on the existing MySQL database after taking a backup.
-- Existing business records are assigned to BIB; existing Luminous users and invoices to Luminous.

CREATE TABLE companies (
    id CHAR(36) NOT NULL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(30) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

SET @bib_company_id = UUID();
SET @luminous_company_id = UUID();

INSERT INTO companies (id, name, code, created_at, updated_at)
VALUES
    (@bib_company_id, 'BIB', 'BIB', NOW(), NOW()),
    (@luminous_company_id, 'Luminous Risk Management', 'LUMINOUS', NOW(), NOW());

ALTER TABLE users ADD company_id CHAR(36) NULL;
ALTER TABLE account_categories ADD company_id CHAR(36) NULL;
ALTER TABLE chart_of_accounts ADD company_id CHAR(36) NULL;
ALTER TABLE contacts ADD company_id CHAR(36) NULL;
ALTER TABLE contact_types ADD company_id CHAR(36) NULL;
ALTER TABLE contact_groups ADD company_id CHAR(36) NULL;
ALTER TABLE cash_banks ADD company_id CHAR(36) NULL;
ALTER TABLE cash_bank_details ADD company_id CHAR(36) NULL;
ALTER TABLE contract_types ADD company_id CHAR(36) NULL;
ALTER TABLE contracts ADD company_id CHAR(36) NULL;
ALTER TABLE contract_details ADD company_id CHAR(36) NULL;
ALTER TABLE contract_endorsements ADD company_id CHAR(36) NULL;
ALTER TABLE journal_entries ADD company_id CHAR(36) NULL;
ALTER TABLE journal_entry_details ADD company_id CHAR(36) NULL;
ALTER TABLE debit_notes ADD company_id CHAR(36) NULL;
ALTER TABLE debit_note_details ADD company_id CHAR(36) NULL;
ALTER TABLE debit_note_billings ADD company_id CHAR(36) NULL;
ALTER TABLE credit_notes ADD company_id CHAR(36) NULL;
ALTER TABLE payment_allocations ADD company_id CHAR(36) NULL;
ALTER TABLE cashouts ADD company_id CHAR(36) NULL;
ALTER TABLE billing_addresses ADD company_id CHAR(36) NULL;
ALTER TABLE luminous_invoices ADD company_id CHAR(36) NULL;
ALTER TABLE luminous_invoice_items ADD company_id CHAR(36) NULL;
ALTER TABLE luminous_payments ADD company_id CHAR(36) NULL;

UPDATE users SET company_id = @bib_company_id;
UPDATE users SET company_id = @luminous_company_id, role = 'admin' WHERE role = 'luminous';
UPDATE account_categories SET company_id = @bib_company_id;
UPDATE chart_of_accounts SET company_id = @bib_company_id;
UPDATE contacts SET company_id = @bib_company_id;
UPDATE contact_types SET company_id = @bib_company_id;
UPDATE contact_groups SET company_id = @bib_company_id;
UPDATE cash_banks SET company_id = @bib_company_id;
UPDATE cash_bank_details SET company_id = @bib_company_id;
UPDATE contract_types SET company_id = @bib_company_id;
UPDATE contracts SET company_id = @bib_company_id;
UPDATE contract_details SET company_id = @bib_company_id;
UPDATE contract_endorsements SET company_id = @bib_company_id;
UPDATE journal_entries SET company_id = @bib_company_id;
UPDATE journal_entry_details SET company_id = @bib_company_id;
UPDATE debit_notes SET company_id = @bib_company_id;
UPDATE debit_note_details SET company_id = @bib_company_id;
UPDATE debit_note_billings SET company_id = @bib_company_id;
UPDATE credit_notes SET company_id = @bib_company_id;
UPDATE payment_allocations SET company_id = @bib_company_id;
UPDATE cashouts SET company_id = @bib_company_id;
UPDATE billing_addresses SET company_id = @bib_company_id;
UPDATE luminous_invoices SET company_id = @luminous_company_id;
UPDATE luminous_invoice_items SET company_id = @luminous_company_id;
UPDATE luminous_payments SET company_id = @luminous_company_id;

ALTER TABLE users MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX users_company_id_index (company_id),
    ADD CONSTRAINT users_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);

ALTER TABLE account_categories MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX account_categories_company_id_index (company_id),
    ADD CONSTRAINT account_categories_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE chart_of_accounts MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX chart_of_accounts_company_id_index (company_id),
    ADD CONSTRAINT chart_of_accounts_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE contacts MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX contacts_company_id_index (company_id),
    ADD CONSTRAINT contacts_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE contact_types MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX contact_types_company_id_index (company_id),
    ADD CONSTRAINT contact_types_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE contact_groups MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX contact_groups_company_id_index (company_id),
    ADD CONSTRAINT contact_groups_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE cash_banks MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX cash_banks_company_id_index (company_id),
    ADD CONSTRAINT cash_banks_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE cash_bank_details MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX cash_bank_details_company_id_index (company_id),
    ADD CONSTRAINT cash_bank_details_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE contract_types MODIFY company_id CHAR(36) NOT NULL,
    DROP INDEX contract_types_code_unique,
    ADD UNIQUE INDEX contract_types_company_code_unique (company_id, code),
    ADD CONSTRAINT contract_types_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE contracts MODIFY company_id CHAR(36) NOT NULL,
    DROP INDEX contracts_number_unique,
    ADD UNIQUE INDEX contracts_company_number_unique (company_id, number),
    ADD CONSTRAINT contracts_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE contract_details MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX contract_details_company_id_index (company_id),
    ADD CONSTRAINT contract_details_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE contract_endorsements MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX contract_endorsements_company_id_index (company_id),
    ADD CONSTRAINT contract_endorsements_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE journal_entries MODIFY company_id CHAR(36) NOT NULL,
    DROP INDEX journal_entries_number_unique,
    ADD UNIQUE INDEX journal_entries_company_number_unique (company_id, number),
    ADD CONSTRAINT journal_entries_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE journal_entry_details MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX journal_entry_details_company_id_index (company_id),
    ADD CONSTRAINT journal_entry_details_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE debit_notes MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX debit_notes_company_id_index (company_id),
    ADD CONSTRAINT debit_notes_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE debit_note_details MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX debit_note_details_company_id_index (company_id),
    ADD CONSTRAINT debit_note_details_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE debit_note_billings MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX debit_note_billings_company_id_index (company_id),
    ADD CONSTRAINT debit_note_billings_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE credit_notes MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX credit_notes_company_id_index (company_id),
    ADD CONSTRAINT credit_notes_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE payment_allocations MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX payment_allocations_company_id_index (company_id),
    ADD CONSTRAINT payment_allocations_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE cashouts MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX cashouts_company_id_index (company_id),
    ADD CONSTRAINT cashouts_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE billing_addresses MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX billing_addresses_company_id_index (company_id),
    ADD CONSTRAINT billing_addresses_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE luminous_invoices MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX luminous_invoices_company_id_index (company_id),
    ADD CONSTRAINT luminous_invoices_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE luminous_invoice_items MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX luminous_invoice_items_company_id_index (company_id),
    ADD CONSTRAINT luminous_invoice_items_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);
ALTER TABLE luminous_payments MODIFY company_id CHAR(36) NOT NULL,
    ADD INDEX luminous_payments_company_id_index (company_id),
    ADD CONSTRAINT luminous_payments_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id);

ALTER TABLE luminous_invoice_items
    ADD cashout_number VARCHAR(100) NULL,
    ADD debit_note_number VARCHAR(100) NULL,
    ADD policy_number VARCHAR(100) NULL,
    ADD insured_name VARCHAR(255) NULL,
    ADD insurance_name VARCHAR(255) NULL,
    ADD cashout_date DATE NULL;

UPDATE luminous_invoice_items AS li
JOIN cashouts AS c ON c.id = li.cashout_id
JOIN debit_notes AS dn ON dn.id = c.debit_note_id
JOIN contracts AS ct ON ct.id = dn.contract_id
LEFT JOIN billing_addresses AS ba ON ba.id = dn.billing_address_id AND ba.company_id = c.company_id
LEFT JOIN contacts AS insured ON insured.id = ct.contact_id AND insured.company_id = c.company_id
LEFT JOIN contacts AS insurance ON insurance.id = c.insurance_id AND insurance.company_id = c.company_id
SET li.cashout_number = c.number,
    li.debit_note_number = dn.number,
    li.policy_number = COALESCE(NULLIF(ct.policy_number, ''), ct.cover_note_number),
    li.insured_name = COALESCE(ba.name, insured.display_name, '-'),
    li.insurance_name = COALESCE(insurance.display_name, '-'),
    li.cashout_date = c.date;

ALTER TABLE cash_banks
    DROP INDEX cash_banks_number_unique,
    ADD UNIQUE INDEX cash_banks_company_number_unique (company_id, number);
