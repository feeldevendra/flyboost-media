<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Admin Backups Page
 * ------------------------------------------------------------
 * Allows admin to:
 * - Generate database & file backups
 * - Download or delete old backups
 * - Track backup history
 * ------------------------------------------------------------
 */
?>

<section class="admin-backups" data-aos="fade-up">
  <div class="container">
    <h1>System Backups</h1>
    <p class="subtitle">Manage your Flyboost Media backups to protect your data and configuration files.</p>

    <!-- BACKUP ACTIONS -->
    <div class="backup-actions">
      <button id="createBackupBtn" class="btn primary">+ Create New Backup</button>
      <a href="/storage/backups/" class="btn secondary" target="_blank">📁 Open Backup Folder</a>
    </div>

    <!-- BACKUPS TABLE -->
    <div class="backups-table-container">
      <table class="backups-table">
        <thead>
          <tr>
            <th>Backup Name</th>
            <th>Size</th>
            <th>Created On</th>
            <th>Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="backupList">
          <?php if (!empty($backups)): ?>
            <?php foreach ($backups as $b): ?>
              <tr>
                <td><?= e($b['name']) ?></td>
                <td><?= e($b['size']) ?></td>
                <td><?= e($b['date']) ?></td>
                <td><?= strtoupper($b['type']) ?></td>
                <td>
                  <a href="/storage/backups/<?= e($b['name']) ?>" class="btn-small">⬇ Download</a>
                  <button class="btn-small danger delete-btn" data-id="<?= e($b['id']) ?>">Delete</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" style="text-align:center;">No backups found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<style>
.admin-backups {
  padding: 70px 0;
}
.subtitle {
  color: #666;
  margin-bottom: 30px;
}
.backup-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 25px;
}
.backups-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 3px 10px rgba(0,0,0,0.03);
}
.backups-table th, .backups-table td {
  padding: 14px;
  border-bottom: 1px solid #eee;
}
.backups-table th {
  background: #f8f9fb;
  text-align: left;
  font-weight: 600;
  color: #444;
}
.btn-small {
  background: #007aff;
  color: white;
  border: none;
  padding: 6px 14px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.85rem;
}
.btn-small.danger {
  background: #ff3b30;
}
@media (max-width: 768px) {
  .backups-table {
    font-size: 0.9rem;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Create new backup
  document.getElementById('createBackupBtn').addEventListener('click', async () => {
    if (!confirm('Create a new backup now? This may take a few seconds.')) return;
    showToast('Starting backup...', 'info');

    const res = await fetch('/admin/backups/create', { method: 'POST' });
    const data = await res.json();
    showToast(data.message, data.success ? 'success' : 'error');
    if (data.success) setTimeout(() => location.reload(), 1500);
  });

  // Delete backup
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      if (!confirm('Are you sure you want to delete this backup file?')) return;
      const id = btn.dataset.id;
      const res = await fetch('/admin/backups/delete', {
        method: 'POST',
        body: new URLSearchParams({ id })
      });
      const data = await res.json();
      showToast(data.message, data.success ? 'success' : 'error');
      if (data.success) setTimeout(() => location.reload(), 1200);
    });
  });
});
</script>
