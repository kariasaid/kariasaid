-- =====================================================
-- DATABASE: konstruksi_ahsp
-- Aplikasi Manajemen Konstruksi - AHSP & RAB
-- CodeIgniter 4
-- =====================================================

CREATE DATABASE IF NOT EXISTS konstruksi_ahsp
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE konstruksi_ahsp;

-- -----------------------------------------------------
-- 1. Tabel Kategori AHSP
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS ahsp_kategori (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_kategori   VARCHAR(20) NOT NULL UNIQUE,
    nama_kategori   VARCHAR(100) NOT NULL,
    deskripsi       TEXT NULL,
    urutan          SMALLINT DEFAULT 0,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- 2. Tabel Master Sumber Daya (Material, Upah, Alat)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS sumber_daya (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_sd         VARCHAR(30) NOT NULL UNIQUE,
    nama_sd         VARCHAR(150) NOT NULL,
    jenis           ENUM('material','upah','alat') NOT NULL,
    satuan          VARCHAR(20) NOT NULL,
    harga_satuan    DECIMAL(18,2) NOT NULL DEFAULT 0,
    keterangan      TEXT NULL,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_jenis (jenis),
    INDEX idx_kode_sd (kode_sd)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- 3. Tabel Master AHSP
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS ahsp_master (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kategori_id     INT UNSIGNED NOT NULL,
    kode_ahsp       VARCHAR(30) NOT NULL UNIQUE,
    nama_pekerjaan  VARCHAR(255) NOT NULL,
    satuan          VARCHAR(20) NOT NULL,
    keterangan      TEXT NULL,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ahsp_kategori 
        FOREIGN KEY (kategori_id) REFERENCES ahsp_kategori(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_kode_ahsp (kode_ahsp),
    INDEX idx_nama (nama_pekerjaan(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- 4. Tabel Rincian AHSP (Pivot + Koefisien)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS ahsp_rincian (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ahsp_id         INT UNSIGNED NOT NULL,
    sumber_daya_id  INT UNSIGNED NOT NULL,
    koefisien       DECIMAL(12,6) NOT NULL DEFAULT 0,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rincian_ahsp 
        FOREIGN KEY (ahsp_id) REFERENCES ahsp_master(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_rincian_sd 
        FOREIGN KEY (sumber_daya_id) REFERENCES sumber_daya(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY uk_ahsp_sd (ahsp_id, sumber_daya_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- 5. Tabel Header RAB (Proyek)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS rab_header (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_proyek     VARCHAR(50) NOT NULL UNIQUE,
    nama_proyek     VARCHAR(255) NOT NULL,
    lokasi          VARCHAR(255) NULL,
    pemilik         VARCHAR(150) NULL,
    file_asli       VARCHAR(255) NULL,
    total_nilai_rab DECIMAL(20,2) DEFAULT 0,
    status          ENUM('draft','proses','selesai','error') DEFAULT 'draft',
    catatan         TEXT NULL,
    created_by      INT UNSIGNED NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- 6. Tabel Detail RAB (Hasil Import)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS rab_detail (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rab_header_id   INT UNSIGNED NOT NULL,
    no_urut         INT DEFAULT 0,
    kode_pekerjaan  VARCHAR(50) NULL,
    nama_pekerjaan  VARCHAR(255) NOT NULL,
    satuan          VARCHAR(20) NULL,
    volume          DECIMAL(18,4) NOT NULL DEFAULT 0,
    harga_satuan    DECIMAL(18,2) DEFAULT 0,
    jumlah          DECIMAL(20,2) DEFAULT 0,
    ahsp_id         INT UNSIGNED NULL,
    match_status    ENUM('matched','unmatched','manual') DEFAULT 'unmatched',
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_detail_header 
        FOREIGN KEY (rab_header_id) REFERENCES rab_header(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detail_ahsp 
        FOREIGN KEY (ahsp_id) REFERENCES ahsp_master(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_match (match_status),
    INDEX idx_kode_pekerjaan (kode_pekerjaan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- 7. Tabel Breakdown Hasil Kalkulasi
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS rab_breakdown (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rab_header_id   INT UNSIGNED NOT NULL,
    rab_detail_id   INT UNSIGNED NOT NULL,
    sumber_daya_id  INT UNSIGNED NOT NULL,
    jenis           ENUM('material','upah','alat') NOT NULL,
    volume_rab      DECIMAL(18,4) NOT NULL,
    koefisien       DECIMAL(12,6) NOT NULL,
    kebutuhan       DECIMAL(18,6) NOT NULL,
    harga_satuan    DECIMAL(18,2) NOT NULL,
    total_harga     DECIMAL(20,2) NOT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bd_header 
        FOREIGN KEY (rab_header_id) REFERENCES rab_header(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_bd_detail 
        FOREIGN KEY (rab_detail_id) REFERENCES rab_detail(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_bd_sd 
        FOREIGN KEY (sumber_daya_id) REFERENCES sumber_daya(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_jenis (jenis),
    INDEX idx_header_jenis (rab_header_id, jenis)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SAMPLE DATA
-- =====================================================

INSERT INTO ahsp_kategori (kode_kategori, nama_kategori, urutan) VALUES
('A', 'Pekerjaan Tanah', 1),
('B', 'Pekerjaan Beton', 2),
('C', 'Pekerjaan Pasangan', 3),
('D', 'Pekerjaan Kayu', 4),
('E', 'Pekerjaan Atap', 5),
('F', 'Pekerjaan Finishing', 6);

INSERT INTO sumber_daya (kode_sd, nama_sd, jenis, satuan, harga_satuan) VALUES
-- Material
('M-001', 'Semen Portland', 'material', 'zak', 65000),
('M-002', 'Pasir Beton', 'material', 'm3', 250000),
('M-003', 'Kerikil / Split', 'material', 'm3', 280000),
('M-004', 'Batu Bata Merah', 'material', 'bh', 850),
('M-005', 'Pasir Pasang', 'material', 'm3', 180000),
('M-006', 'Besi Beton Ø10', 'material', 'kg', 12000),
('M-007', 'Besi Beton Ø12', 'material', 'kg', 12000),
('M-008', 'Kawat Bendrat', 'material', 'kg', 18000),
('M-009', 'Kayu Balok 5/7', 'material', 'm3', 4500000),
('M-010', 'Paku 2-3 inch', 'material', 'kg', 25000),
-- Upah
('U-001', 'Pekerja', 'upah', 'OH', 120000),
('U-002', 'Tukang Batu', 'upah', 'OH', 150000),
('U-003', 'Kepala Tukang', 'upah', 'OH', 180000),
('U-004', 'Tukang Kayu', 'upah', 'OH', 155000),
('U-005', 'Tukang Besi', 'upah', 'OH', 160000),
-- Alat
('A-001', 'Concrete Mixer', 'alat', 'hari', 350000),
('A-002', 'Vibrator', 'alat', 'hari', 75000);

-- Sample AHSP Master
INSERT INTO ahsp_master (kategori_id, kode_ahsp, nama_pekerjaan, satuan) VALUES
(1, 'A.1.1.1', 'Galian tanah biasa sedalam 1 m', 'm3'),
(1, 'A.1.1.2', 'Urugan kembali tanah galian', 'm3'),
(2, 'B.1.1.1', 'Beton ready mix fc 20 MPa', 'm3'),
(2, 'B.2.1.1', 'Pembesian tulangan beton', 'kg'),
(3, 'C.1.1.1', 'Pasangan bata 1/2 batu', 'm2'),
(3, 'C.1.2.1', 'Plesteran 1:4 tebal 15 mm', 'm2');

-- Sample Rincian AHSP (koefisien)
-- A.1.1.1 Galian tanah
INSERT INTO ahsp_rincian (ahsp_id, sumber_daya_id, koefisien) VALUES
(1, 11, 0.750000),  -- Pekerja 0.75 OH
(1, 13, 0.050000);  -- Kepala Tukang 0.05 OH

-- A.1.1.2 Urugan
INSERT INTO ahsp_rincian (ahsp_id, sumber_daya_id, koefisien) VALUES
(2, 11, 0.500000),
(2, 13, 0.030000);

-- B.1.1.1 Beton
INSERT INTO ahsp_rincian (ahsp_id, sumber_daya_id, koefisien) VALUES
(3, 1, 7.000000),   -- Semen 7 zak
(3, 2, 0.520000),   -- Pasir 0.52 m3
(3, 3, 0.780000),   -- Kerikil 0.78 m3
(3, 11, 1.200000),  -- Pekerja
(3, 12, 0.300000),  -- Tukang Batu
(3, 16, 0.100000);  -- Mixer

-- B.2.1.1 Pembesian
INSERT INTO ahsp_rincian (ahsp_id, sumber_daya_id, koefisien) VALUES
(4, 6, 1.050000),   -- Besi +5% waste
(4, 8, 0.015000),   -- Kawat
(4, 15, 0.020000);  -- Tukang Besi

-- C.1.1.1 Pasangan bata
INSERT INTO ahsp_rincian (ahsp_id, sumber_daya_id, koefisien) VALUES
(5, 4, 70.000000),  -- Bata 70 bh
(5, 1, 0.255000),   -- Semen
(5, 5, 0.043000),   -- Pasir pasang
(5, 11, 0.350000),  -- Pekerja
(5, 12, 0.150000);  -- Tukang Batu

-- C.1.2.1 Plesteran
INSERT INTO ahsp_rincian (ahsp_id, sumber_daya_id, koefisien) VALUES
(6, 1, 0.102000),
(6, 5, 0.025000),
(6, 11, 0.150000),
(6, 12, 0.100000);
