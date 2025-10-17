<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Admin Services Management
 * ------------------------------------------------------------
 * Allows admin to:
 * - Add/Edit/Delete Services
 * - Manage options (for quotes)
 * - Update icons, descriptions, and prices
 * ------------------------------------------------------------
 */
?>

<section class="admin-services" data-aos="fade-up">
  <div class="container">
    <h1>Manage Services</h1>
    <p class="subtitle">Add or modify your agency’s services. These appear on the main site and in the quote configurator.</p>

    <!-- ADD NEW SERVICE BUTTON -->
    <button class="btn primary" id="addServiceBtn">+ Add New Service</button>

    <!-- SERVICES TABLE -->
    <div class="services-table-container">
      <table class="services-table">
        <thead>
          <tr>
            <th>Icon</th>
            <th>Title</th>
            <th>Slug</th>
            <th>Price Range</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="servicesList">
          <?php foreach ($services as $s): ?>
            <tr>
              <td><img src="<?= base_url($s['icon']) ?>" alt="" class="service-icon"></td>
              <td><?= e($s['title']) ?></td>
              <td><?= e($s['slug']) ?></td>
              <td>₹<?= e($s['min_price']) ?> - ₹<?= e($s['max_price']) ?></td>
              <td>
                <span class="badge <?= $s['status'] === 'active' ? 'active' : 'inactive' ?>">
                  <?= ucfirst($s['status']) ?>
                </span>
              </td>
              <td>
                <button class="btn-small edit-btn" data-id="<?= $s['id'] ?>">Edit</button>
                <button class="btn-small danger delete-btn" data-id="<?= $s['id'] ?>">Delete</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ADD/EDIT SERVICE MODAL -->
<div id="serviceModal" class="modal">
  <div class="modal-content">
    <span class="close-modal">&times;</span>
    <h2 id="modalTitle">Add New Service</h2>

    <form id="serviceForm" action="/admin/services/save" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="serviceId">

      <div class="form-group">
        <label>Title *</label>
        <input type="text" name="title" id="title" required>
      </div>

      <div class="form-group">
        <label>Slug *</label>
        <input type="text" name="slug" id="slug" placeholder="auto-generated" required>
      </div>

      <div class="form-group">
        <label>Description *</label>
        <textarea name="description" id="description" rows="4" required></textarea>
      </div>

      <div class="form-group">
        <label>Icon (Upload or URL)</label>
        <input type="file" name="icon" id="icon">
      </div>

      <div class="form-group">
        <label>Minimum Price (₹)</label>
        <input type="number" name="min_price" id="min_price" step="0.01">
      </div>

      <div class="form-group">
        <label>Maximum Price (₹)</label>
        <input type="number" name="max_price" id="max_price" step="0.01">
      </div>

      <div class="form-group">
        <label>Status</label>
        <select name="status" id="status">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>

      <button type="submit" class="btn primary">Save Service</button>
    </form>
  </div>
</div>

<style>
.admin-services {
  padding: 70px 0;
}
.subtitle {
  color: #666;
  margin-bottom: 20px;
}
.services-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 3px 8px rgba(0,0,0,0.04);
}
.services-table th, .services-table td {
  padding: 14px;
  border-bottom: 1px solid #eee;
  text-align: left;
}
.service-icon {
  width: 40px;
  height: 40px;
  border-radius: 6px;
}
.badge.active {
  background: #28a745;
  color: white;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.85rem;
}
.badge.inactive {
  background: #ccc;
  color: #222;
  padding: 4px 10px;
  border-radius: 6px;
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
.modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.modal-content {
  background: #fff;
  border-radius: 12px;
  padding: 30px;
  width: 100%;
  max-width: 600px;
  position: relative;
}
.close-modal {
  position: absolute;
  top: 12px;
  right: 20px;
  cursor: pointer;
  font-size: 1.4rem;
  color: #555;
}
.form-group {
  margin-bottom: 16px;
}
.form-group label {
  display: block;
  color: #333;
  margin-bottom: 6px;
  font-weight: 500;
}
.form-group input, .form-group textarea, .form-group select {
  width: 100%;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 10px;
  font-size: 1rem;
}
@media (max-width: 768px) {
  .modal-content {
    width: 90%;
    padding: 20px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('serviceModal');
  const addBtn = document.getElementById('addServiceBtn');
  const closeBtn = document.querySelector('.close-modal');

  addBtn.addEventListener('click', () => {
    modal.style.display = 'flex';
    document.getElementById('modalTitle').innerText = 'Add New Service';
    document.getElementById('serviceForm').reset();
  });

  closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  // Handle edit buttons
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const res = await fetch(`/admin/services/get?id=${id}`);
      const data = await res.json();

      if (data.success) {
        const s = data.service;
        document.getElementById('serviceId').value = s.id;
        document.getElementById('title').value = s.title;
        document.getElementById('slug').value = s.slug;
        document.getElementById('description').value = s.description;
        document.getElementById('min_price').value = s.min_price;
        document.getElementById('max_price').value = s.max_price;
        document.getElementById('status').value = s.status;
        modal.style.display = 'flex';
        document.getElementById('modalTitle').innerText = 'Edit Service';
      }
    });
  });

  // Handle delete buttons
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      if (!confirm('Are you sure you want to delete this service?')) return;
      const id = btn.dataset.id;
      const res = await fetch('/admin/services/delete', {
        method: 'POST',
        body: new URLSearchParams({ id })
      });
      const data = await res.json();
      showToast(data.message, data.success ? 'success' : 'error');
      if (data.success) setTimeout(() => window.location.reload(), 1200);
    });
  });

  // AJAX form submission
  const form = document.getElementById('serviceForm');
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const res = await fetch(form.action, { method: 'POST', body: formData });
    const data = await res.json();
    showToast(data.message, data.success ? 'success' : 'error');
    if (data.success) setTimeout(() => location.reload(), 1000);
  });
});
</script>
