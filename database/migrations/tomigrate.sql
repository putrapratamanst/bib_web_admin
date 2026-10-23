
-- 1. Ubah role users agar mendukung role luminous
ALTER TABLE `users`
MODIFY COLUMN `role` VARCHAR(30) NOT NULL DEFAULT 'admin';

-- 2. Buat tabel tagihan Luminous
CREATE TABLE `luminous_invoices` (
    `id` CHAR(36) NOT NULL,
    `number` VARCHAR(100) NOT NULL,
    `recipient` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `date` DATE NOT NULL,
    `due_date` DATE NOT NULL,
    `amount` DECIMAL(18,2) NOT NULL,
    `paid_amount` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    `document_image` VARCHAR(255) NULL,
    `status` ENUM('unpaid', 'partial', 'paid') NOT NULL DEFAULT 'unpaid',
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `luminous_invoices_number_unique` (`number`),

    CONSTRAINT `luminous_invoices_created_by_foreign`
        FOREIGN KEY (`created_by`)
        REFERENCES `users` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `users`
    (`name`, `email`, `password`, `role`, `created_at`, `updated_at`)
VALUES
    (
        'Luminous User',
        'luminous@example.com',
        '$2y$12$q0hr5HdJj3h9X087Ifb7muY/uBCQpxOCTye7UtOwsCSFNChCw7fXS',
        'luminous',
        NOW(),
        NOW()
    );

    ALTER TABLE `luminous_invoices`
    ADD COLUMN `city` VARCHAR(100) NOT NULL DEFAULT 'Jakarta' AFTER `date`,
    ADD COLUMN `subject` VARCHAR(255) NOT NULL DEFAULT 'Tagihan Biaya Sosialisasi' AFTER `description`,
    ADD COLUMN `bank_name` VARCHAR(255) NULL AFTER `document_image`,
    ADD COLUMN `bank_account` VARCHAR(100) NULL AFTER `bank_name`,
    ADD COLUMN `account_name` VARCHAR(255) NULL AFTER `bank_account`,
    ADD COLUMN `signatory_name` VARCHAR(255) NULL AFTER `account_name`,
    ADD COLUMN `signatory_title` VARCHAR(255) NULL AFTER `signatory_name`;

    -- 1. Tambah asuransi pada tagihan
ALTER TABLE `luminous_invoices`
    ADD COLUMN `insurance_id` CHAR(36) NULL AFTER `number`,
    ADD CONSTRAINT `luminous_invoices_insurance_id_foreign`
        FOREIGN KEY (`insurance_id`) REFERENCES `contacts` (`id`);

-- 2. Rincian Hutang Asuransi (Basos) per tagihan
--    cashout_id UNIQUE: satu Hutang Asuransi hanya bisa ditagihkan satu kali
CREATE TABLE `luminous_invoice_items` (
    `id` CHAR(36) NOT NULL,
    `luminous_invoice_id` CHAR(36) NOT NULL,
    `cashout_id` CHAR(36) NOT NULL,
    `basos_amount` DECIMAL(18,2) NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `luminous_invoice_items_cashout_id_unique` (`cashout_id`),
    CONSTRAINT `luminous_invoice_items_luminous_invoice_id_foreign`
        FOREIGN KEY (`luminous_invoice_id`) REFERENCES `luminous_invoices` (`id`) ON DELETE CASCADE,
    CONSTRAINT `luminous_invoice_items_cashout_id_foreign`
        FOREIGN KEY (`cashout_id`) REFERENCES `cashouts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Riwayat pembayaran bertanggal
CREATE TABLE `luminous_payments` (
    `id` CHAR(36) NOT NULL,
    `luminous_invoice_id` CHAR(36) NOT NULL,
    `date` DATE NOT NULL,
    `amount` DECIMAL(18,2) NOT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `luminous_payments_luminous_invoice_id_foreign`
        FOREIGN KEY (`luminous_invoice_id`) REFERENCES `luminous_invoices` (`id`) ON DELETE CASCADE,
    CONSTRAINT `luminous_payments_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;