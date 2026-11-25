ALTER TABLE cashouts
    ADD COLUMN paid_at TIMESTAMP NULL AFTER status;

ALTER TABLE debit_notes
    ADD COLUMN source_cashout_id CHAR(36) NULL AFTER contract_id,
    ADD UNIQUE INDEX debit_notes_source_cashout_id_unique (source_cashout_id),
    ADD CONSTRAINT debit_notes_source_cashout_id_foreign
        FOREIGN KEY (source_cashout_id)
        REFERENCES cashouts (id)
        ON DELETE SET NULL;