<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Profile Page
 * ------------------------------------------------------------
 * Allows client to:
 * - View and update name/email
 * - Change password
 * - See account info
 * ------------------------------------------------------------
 */
?>

<section class="profile-page" data-aos="fade-up">
  <div class="container">
    <h1>My Profile</h1>
    <p class="subtitle">Manage your account details and preferences securely.</p>

    <div class="profile-card">
      <form id="profileForm" action="/account/profile/update" method="POST">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" value="<?= e($user['name']) ?>" required>
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" value="<?= e($user['email']) ?>" disabled>
        </div>

        <div class="form-group">
          <label for="password">New Password</label>
          <input type="password" id="password" name="password" placeholder="Enter new password (optional)">
        </div>

        <button type="submit" class="btn primary">Save Changes</button>
      </form>

      <div class="account-info">
        <h3>Account Information</h3>
        <ul>
          <li><strong>Role:</strong> <?= e($user['role']) ?></li>
          <li><strong>Member Since:</strong> <?= date('F Y', strtotime($user['created_at'])) ?></li>
          <li><strong>Last Login:</strong> <?= e($user['last_login'] ?? 'N/A') ?></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<style>
.profile-page {
  padding: 80px 0;
}
.profile-card {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 40px;
  background: #fff;
  border-radius: 14px;
  padding: 40px;
  border: 1px solid #eee;
  box-shadow: 0 3px 10px rgba(0,0,0,0.04);
}
.profile-card h3 {
  margin-bottom: 15px;
}
.form-group {
  margin-bottom: 20px;
}
.form-group label {
  display: block;
  color: #555;
  margin-bottom: 6px;
  font-weight: 500;
}
.form-group input {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 1rem;
}
.btn.primary {
  background: #007aff;
  color: white;
  padding: 12px 30px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.2s ease;
}
.btn.primary:hover {
  background: #005ecb;
}
.account-info ul {
  list-style: none;
  padding: 0;
  color: #333;
}
.account-info li {
  margin-bottom: 10px;
}
@media (max-width: 768px) {
  .profile-card {
    grid-template-columns: 1fr;
    padding: 25px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('profileForm');
  if (!form) return;

  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = new FormData(this);

    const res = await fetch(this.action, { method: 'POST', body: data });
    const json = await res.json();

    showToast(json.message, json.success ? 'success' : 'error');
  });
});
</script>
