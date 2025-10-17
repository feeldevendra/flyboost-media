<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Admin Leads Management
 * ------------------------------------------------------------
 * Allows admin to:
 * - View contact + quote inquiries
 * - Filter leads by date/service
 * - Export CSV
 * - Mark leads as contacted
 * ------------------------------------------------------------
 */
?>

<section class="admin-leads" data-aos="fade-up">
  <div class="container">
    <h1>Leads & Inquiries</h1>
    <p class="subtitle">All form submissions and quote requests from your Flyboost Media website are listed here.</p>

    <!-- FILTERS -->
    <div class="filter-bar">
      <form id="filterForm" method="GET" action="/admin/leads">
        <select name="service" id="filterService">
          <option value="">All Services</option>
          <?php foreach ($services as $srv): ?>
            <option value="<?= e($srv['title']) ?>" <?= ($_GET['service'] ?? '') === $srv['title'] ? 'selected' : '' ?>>
              <?= e($srv['title']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <input type="date" name="from" value="<?= e($_GET['from'] ?? '') ?>">
        <input type="date" name="to" value="<?= e($_GET['to'] ?? '') ?>">

        <button type="submit" class="btn primary">Filter</button>
        <a href="/admin/leads/export" class="btn secondary">⬇ Export CSV</a>
      </form>
    </div>

    <!-- LEADS TABLE -->
    <div class="leads-table-container">
      <table class="leads-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email / Phone</th>
            <th>Service</th>
            <th>Message</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($leads)): ?>
            <?php foreach ($leads as $lead): ?>
              <tr>
                <td><?= e($lead['name']) ?></td>
                <td>
                  <a href="mailto:<?= e($lead['email']) ?>"><?= e($lead['email']) ?></a><br>
                  <a href="tel:<?= e($lead['phone']) ?>"><?= e($lead['phone']) ?></a>
                </td>
                <td><?= e($lead['service']) ?></td>
                <td><?= e(substr($lead['message'], 0, 80)) ?>...</td>
                <td><?= date('M d, Y', strtotime($lead['created_at'])) ?></td>
                <td>
                  <span class="badge <?= $lead['status'] === 'contacted' ? 'active' : 'pending' ?>">
                    <?= ucfirst($lead['status']) ?>
                  </span>
                </td>
                <td>
                  <?php if ($lead['status'] !== 'contacted'): ?>
                    <button class="btn-small mark-btn" data-id="<?= $lead['id'] ?>">Mark Contacted</button>
                  <?php endif; ?>
                  <button class="btn-small danger delete-btn" data-id="<?= $lead['id'] ?>">Delete</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" style="text-align:center;">No leads found for this period.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<style>
.admin-leads {
  padding: 70px 0;
}
.subtitle {
  color: #666;
  margin-bottom: 20px;
}
.filter-bar {
  margin-bottom: 25px;
}
.filter-bar form {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
}
.filter-bar select, .filter-bar input {
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 0.95rem;
}
.leads-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 3px 10px rgba(0,0,0,0.03);
}
.leads-table th, .leads-table td {
  padding: 12px;
  border-bottom: 1px solid #eee;
  text-align: left;
  vertical-align: top;
}
.leads-table th {
  background: #f8f9fb;
  color: #444;
  font-weight: 600;
}
.badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  color: white;
  font-size: 0.85rem;
}
.badge.active { background: #28a745; }
.badge.pending { background: #ffb700; color: #111; }
.btn-small {
  background: #007aff;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 6px 12px;
  font-size: 0.85rem;
  cursor: pointer;
}
.btn-small.danger {
  background: #ff3b30;
}
@media (max-width: 768px) {
  .filter-bar form {
    flex-direction: column;
    align-items: stretch;
  }
  .leads-table {
    font-size: 0.9rem;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // MARK CONTACTED
  document.querySelectorAll('.mark-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const res = await fetch('/admin/leads/mark', {
        method: 'POST',
        body: new URLSearchParams({ id })
      });
      const data = await res.json();
      showToast(data.message, data.success ? 'success' : 'error');
      if (data.success) setTimeout(() => location.reload(), 1200);
    });
  });

  // DELETE LEAD
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      if (!confirm('Are you sure you want to delete this lead?')) return;
      const id = btn.dataset.id;
      const res = await fetch('/admin/leads/delete', {
        method: 'POST',
        body: new URLSearchParams({ id })
      });
      const data = await res.json();
      showToast(data.message, data.success ? 'success' : 'error');
      if (data.success) setTimeout(() => location.reload(), 1000);
    });
  });
});
</script>
