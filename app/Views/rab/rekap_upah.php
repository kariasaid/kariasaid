<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-people"></i> Rekapitulasi Kebutuhan Upah</h4>
        <small class="text-muted"><?= esc($header['nama_proyek']) ?> &middot; <?= esc($header['kode_proyek']) ?></small>
    </div>
    <div class="btn-group">
        <a href="<?= site_url('rab/rekap-bahan/' . $header['id']) ?>" class="btn btn-outline-success btn-sm">
            <i class="bi bi-box-seam"></i> Lihat Bahan
        </a>
        <a href="<?= site_url('rab/detail/' . $header['id']) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Detail RAB
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="50">No</th>
                        <th>Kode</th>
                        <th>Pekerja / Tukang</th>
                        <th>Satuan</th>
                        <th class="text-end">Total OH</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rekap)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data breakdown upah. Pastikan item RAB sudah matched dan kalkulasi sudah dijalankan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; $grand = 0; foreach ($rekap as $r): $grand += $r['total_harga']; ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><code><?= esc($r['kode_sd']) ?></code></td>
                            <td><?= esc($r['nama_sd']) ?></td>
                            <td><?= esc($r['satuan']) ?></td>
                            <td class="text-end"><?= number_format($r['total_volume'], 4, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($r['harga_satuan'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($rekap)): ?>
                <tfoot>
                    <tr class="table-secondary fw-bold">
                        <td colspan="6" class="text-end">TOTAL UPAH / TENAGA KERJA</td>
                        <td class="text-end">Rp <?= number_format($grand, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
