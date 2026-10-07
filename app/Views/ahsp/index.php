<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-journal-text"></i> Manajemen AHSP</h4>
    <a href="<?= site_url('ahsp/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah AHSP
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="50">No</th>
                        <th>Kode AHSP</th>
                        <th>Nama Pekerjaan</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada data AHSP. <a href="<?= site_url('ahsp/create') ?>">Tambah sekarang</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($list as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><code><?= esc($row['kode_ahsp']) ?></code></td>
                            <td><?= esc($row['nama_pekerjaan']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= esc($row['nama_kategori']) ?></span>
                            </td>
                            <td><?= esc($row['satuan']) ?></td>
                            <td>
                                <a href="<?= site_url('ahsp/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= site_url('ahsp/delete/' . $row['id']) ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   title="Hapus"
                                   onclick="return confirm('Yakin ingin menonaktifkan AHSP ini?')">
                                    <i class="bi bi-trash"></i>
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

<?= $this->endSection() ?>
