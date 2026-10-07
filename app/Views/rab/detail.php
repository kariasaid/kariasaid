<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-file-text"></i> Detail RAB</h4>
        <small class="text-muted"><?= esc($header['nama_proyek']) ?> &middot; <?= esc($header['kode_proyek']) ?></small>
    </div>
    <div class="btn-group">
        <a href="<?= site_url('rab/rekap-bahan/' . $header['id']) ?>" class="btn btn-success btn-sm">
            <i class="bi bi-box-seam"></i> Rekap Bahan
        </a>
        <a href="<?= site_url('rab/rekap-upah/' . $header['id']) ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-people"></i> Rekap Upah
        </a>
        <a href="<?= site_url('rab/recalculate/' . $header['id']) ?>" class="btn btn-outline-primary btn-sm"
           onclick="return confirm('Hitung ulang breakdown?')">
            <i class="bi bi-arrow-repeat"></i> Re-calculate
        </a>
        <a href="<?= site_url('rab') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<!-- Info Header -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body py-3">
                <small class="text-muted">Status</small>
                <?php
                $badge = match($header['status']) {
                    'selesai' => 'success',
                    'proses'  => 'warning',
                    'error'   => 'danger',
                    default   => 'secondary',
                };
                ?>
                <div><span class="badge bg-<?= $badge ?> fs-6"><?= esc($header['status']) ?></span></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body py-3">
                <small class="text-muted">Matched / Unmatched</small>
                <div>
                    <span class="text-success fw-bold"><?= $matched ?></span> /
                    <span class="text-danger fw-bold"><?= $unmatched ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body py-3">
                <small class="text-muted">Total Material</small>
                <div class="fw-bold">Rp <?= number_format($totals['material'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body py-3">
                <small class="text-muted">Total Upah</small>
                <div class="fw-bold">Rp <?= number_format($totals['upah'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Detail -->
<div class="card">
    <div class="card-header bg-white">
        <strong>Item Pekerjaan (hasil import)</strong>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Pekerjaan</th>
                        <th>Satuan</th>
                        <th class="text-end">Volume</th>
                        <th>Match Status</th>
                        <th>AHSP Terikat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($details)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada data detail.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($details as $d): ?>
                        <tr class="<?= $d['match_status'] === 'unmatched' ? 'table-warning' : '' ?>">
                            <td><?= esc($d['no_urut']) ?></td>
                            <td><code><?= esc($d['kode_pekerjaan'] ?? '-') ?></code></td>
                            <td><?= esc($d['nama_pekerjaan']) ?></td>
                            <td><?= esc($d['satuan'] ?? '-') ?></td>
                            <td class="text-end"><?= number_format($d['volume'], 4, ',', '.') ?></td>
                            <td>
                                <?php
                                $mBadge = match($d['match_status']) {
                                    'matched' => 'success',
                                    'manual'  => 'info',
                                    default   => 'danger',
                                };
                                ?>
                                <span class="badge bg-<?= $mBadge ?>"><?= esc($d['match_status']) ?></span>
                            </td>
                            <td>
                                <?php if ($d['kode_ahsp']): ?>
                                    <code><?= esc($d['kode_ahsp']) ?></code>
                                    <small class="text-muted d-block"><?= esc($d['nama_ahsp'] ?? '') ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
