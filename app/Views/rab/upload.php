<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-cloud-upload"></i> Upload & Import RAB</h4>
</div>

<div class="row g-4">
    <!-- Form Upload -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-file-earmark-excel"></i> Form Upload File RAB
            </div>
            <div class="card-body">
                <form action="<?= site_url('rab/upload') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Nama Proyek <span class="text-danger">*</span></label>
                        <input type="text" name="nama_proyek" class="form-control" required
                               placeholder="Contoh: Pembangunan Gedung Kantor">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control"
                               placeholder="Kota / Kabupaten">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pemilik / Owner</label>
                        <input type="text" name="pemilik" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File RAB (Excel / CSV) <span class="text-danger">*</span></label>
                        <input type="file" name="file_rab" class="form-control"
                               accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">
                            Format kolom: <strong>No | Kode | Uraian | Satuan | Volume | Harga Satuan</strong><br>
                            Data dimulai dari baris ke-2 (baris 1 = header).
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-cloud-upload"></i> Upload & Proses Otomatis
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body small text-muted">
                <strong>Catatan:</strong>
                <ul class="mb-0 ps-3">
                    <li>Sistem akan mencoba matching Kode / Nama Pekerjaan dengan database AHSP.</li>
                    <li>Setelah matching, kalkulasi kebutuhan bahan & upah dijalankan otomatis.</li>
                    <li>Item yang tidak match bisa dilihat di halaman Detail.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Daftar Proyek -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white">
                <strong>Daftar Proyek RAB</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Proyek</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th width="160">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($projek)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada proyek. Upload file RAB untuk memulai.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($projek as $p): ?>
                                <tr>
                                    <td><code class="small"><?= esc($p['kode_proyek']) ?></code></td>
                                    <td>
                                        <?= esc($p['nama_proyek']) ?>
                                        <?php if ($p['lokasi']): ?>
                                            <br><small class="text-muted"><?= esc($p['lokasi']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badge = match($p['status']) {
                                            'selesai' => 'success',
                                            'proses'  => 'warning',
                                            'error'   => 'danger',
                                            default   => 'secondary',
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= esc($p['status']) ?></span>
                                    </td>
                                    <td class="small"><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= site_url('rab/detail/' . $p['id']) ?>"
                                               class="btn btn-outline-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= site_url('rab/rekap-bahan/' . $p['id']) ?>"
                                               class="btn btn-outline-success" title="Rekap Bahan">
                                                <i class="bi bi-box-seam"></i>
                                            </a>
                                            <a href="<?= site_url('rab/rekap-upah/' . $p['id']) ?>"
                                               class="btn btn-outline-warning" title="Rekap Upah">
                                                <i class="bi bi-people"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
