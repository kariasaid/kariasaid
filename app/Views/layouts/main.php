<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Aplikasi Konstruksi') ?> | AHSP & RAB</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { background-color: #f4f6f9; }
        .sidebar {
            min-height: 100vh;
            background: #212529;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            border-radius: 0.375rem;
            margin-bottom: 2px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: #0d6efd;
        }
        .sidebar .nav-link i { width: 1.5rem; }
        .content-wrapper { padding: 1.5rem; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); }
        .table th { white-space: nowrap; }
        .stat-card { border-left: 4px solid; }
        .stat-card.primary { border-left-color: #0d6efd; }
        .stat-card.success { border-left-color: #198754; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.info { border-left-color: #0dcaf0; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 col-lg-2 d-md-block sidebar p-3">
            <div class="text-white mb-4">
                <h5 class="mb-0"><i class="bi bi-building"></i> Konstruksi</h5>
                <small class="text-secondary">AHSP & RAB Manager</small>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() === '' || uri_string() === '/' ? 'active' : '' ?>" href="<?= site_url('/') ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'ahsp') ? 'active' : '' ?>" href="<?= site_url('ahsp') ?>">
                        <i class="bi bi-journal-text"></i> Manajemen AHSP
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'rab') ? 'active' : '' ?>" href="<?= site_url('rab') ?>">
                        <i class="bi bi-cloud-upload"></i> Upload RAB
                    </a>
                </li>
            </ul>
            <hr class="text-secondary">
            <div class="text-secondary small px-2">
                CodeIgniter 4 &middot; Bootstrap 5
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 col-lg-10 content-wrapper">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Helper: tambah baris rincian AHSP
function addRincianRow() {
    const tbody = document.getElementById('rincian-body');
    if (!tbody) return;

    const options = document.getElementById('sd-options-template');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="sumber_daya_id[]" class="form-select form-select-sm" required>
                <option value="">-- Pilih Sumber Daya --</option>
                ${options ? options.innerHTML : ''}
            </select>
        </td>
        <td>
            <input type="number" name="koefisien[]" class="form-control form-control-sm" 
                   step="0.000001" min="0" value="0" required>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
