<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isEdit = !empty($ahsp); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-lg' ?>"></i>
        <?= $isEdit ? 'Edit AHSP' : 'Tambah AHSP' ?>
    </h4>
    <a href="<?= site_url('ahsp') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form action="<?= $isEdit ? site_url('ahsp/update/' . $ahsp['id']) : site_url('ahsp/store') ?>" method="post">
    <?= csrf_field() ?>

    <div class="row g-3">
        <!-- Data Utama -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><strong>Data Pekerjaan</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= $k['id'] ?>"
                                    <?= old('kategori_id', $ahsp['kategori_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                                    <?= esc($k['kode_kategori'] . ' - ' . $k['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode AHSP <span class="text-danger">*</span></label>
                        <input type="text" name="kode_ahsp" class="form-control"
                               value="<?= old('kode_ahsp', $ahsp['kode_ahsp'] ?? '') ?>"
                               placeholder="Contoh: A.1.1.1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pekerjaan" class="form-control"
                               value="<?= old('nama_pekerjaan', $ahsp['nama_pekerjaan'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="satuan" class="form-control"
                               value="<?= old('satuan', $ahsp['satuan'] ?? '') ?>"
                               placeholder="m3, m2, m', kg, ls, ..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"><?= old('keterangan', $ahsp['keterangan'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rincian Koefisien -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Rincian Koefisien Sumber Daya</strong>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRincianRow()">
                        <i class="bi bi-plus"></i> Tambah
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sumber Daya</th>
                                    <th width="120">Koefisien</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody id="rincian-body">
                                <?php if (!empty($rincian)): ?>
                                    <?php foreach ($rincian as $r): ?>
                                    <tr>
                                        <td>
                                            <select name="sumber_daya_id[]" class="form-select form-select-sm" required>
                                                <option value="">-- Pilih --</option>
                                                <?php foreach ($sumberDaya as $sd): ?>
                                                    <option value="<?= $sd['id'] ?>"
                                                        <?= $sd['id'] == $r['sumber_daya_id'] ? 'selected' : '' ?>>
                                                        [<?= esc($sd['jenis']) ?>] <?= esc($sd['kode_sd'] . ' - ' . $sd['nama_sd']) ?> (<?= esc($sd['satuan']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="koefisien[]" class="form-control form-control-sm"
                                                   step="0.000001" min="0" value="<?= esc($r['koefisien']) ?>" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="this.closest('tr').remove()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (empty($rincian)): ?>
                        <div class="p-3 text-muted small text-center">
                            Belum ada rincian. Klik "Tambah" untuk menambahkan koefisien.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> <?= $isEdit ? 'Perbarui' : 'Simpan' ?>
        </button>
        <a href="<?= site_url('ahsp') ?>" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>

<!-- Template options untuk JS -->
<select id="sd-options-template" class="d-none">
    <?php foreach ($sumberDaya as $sd): ?>
        <option value="<?= $sd['id'] ?>">
            [<?= esc($sd['jenis']) ?>] <?= esc($sd['kode_sd'] . ' - ' . $sd['nama_sd']) ?> (<?= esc($sd['satuan']) ?>)
        </option>
    <?php endforeach; ?>
</select>

<?= $this->endSection() ?>
