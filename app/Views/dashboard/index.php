<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-speedometer2"></i> Dashboard</h4>
</div>

<!-- Statistik -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Total AHSP</h6>
                        <h3 class="mb-0"><?= number_format($total_ahsp) ?></h3>
                    </div>
                    <i class="bi bi-journal-text fs-1 text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Proyek RAB</h6>
                        <h3 class="mb-0"><?= number_format($total_proyek) ?></h3>
                    </div>
                    <i class="bi bi-folder fs-1 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Material</h6>
                        <h3 class="mb-0"><?= number_format($total_material) ?></h3>
                    </div>
                    <i class="bi bi-box-seam fs-1 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Upah / Tukang</h6>
                        <h3 class="mb-0"><?= number_format($total_upah) ?></h3>
                    </div>
                    <i class="bi bi-people fs-1 text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Status Proyek -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <strong>Status Proyek</strong>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-check-circle text-success"></i> Selesai</span>
                    <strong><?= $proyek_selesai ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-hourglass-split text-warning"></i> Proses</span>
                    <strong><?= $proyek_proses ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span><i class="bi bi-folder text-secondary"></i> Total</span>
                    <strong><?= $total_proyek ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Proyek Terbaru -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Proyek RAB Terbaru</strong>
                <a href="<?= site_url('rab') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Proyek</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($proyek_terbaru)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada proyek</td></tr>
                            <?php else: ?>
                                <?php foreach ($proyek_terbaru as $p): ?>
                                <tr>
                                    <td><code><?= esc($p['kode_proyek']) ?></code></td>
                                    <td><?= esc($p['nama_proyek']) ?></td>
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
                                    <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= site_url('rab/detail/' . $p['id']) ?>" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
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
