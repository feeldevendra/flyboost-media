<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Admin Dashboard
 * ------------------------------------------------------------
 * Displays:
 * - Key business metrics
 * - Analytics summary (GA4 / Matomo)
 * - Notifications
 * - Recent backups
 * - Quick shortcuts
 * ------------------------------------------------------------
 */
?>

<section class="admin-dashboard" data-aos="fade-up">
  <div class="container">
    <h1>Welcome, <?= e(user('name')) ?> 👋</h1>
    <p class="subtitle">Here’s your Flyboost Media overview and real-time system insights.</p>

    <!-- QUICK STATS -->
    <div class="admin-stats-grid">
      <div class="stat-card" data-aos="zoom-in">
        <h3><?= e($stats['services']) ?></h3>
        <p>Services</p>
      </div>
      <div class="stat-card" data-aos="zoom-in">
        <h3><?= e($stats['blogs']) ?></h3>
        <p>Published Blogs</p>
      </div>
      <div class="stat-card" data-aos="zoom-in">
        <h3><?= e($stats['leads']) ?></h3>
        <p>Leads / Inquiries</p>
      </div>
      <div class="stat-card" data-aos="zoom-in">
        <h3><?= e($stats['clients']) ?></h3>
        <p>Registered Clients</p>
      </div>
      <div class="stat-card" data-aos="zoom-in">
        <h3><?= e($stats['projects']) ?></h3>
        <p>Active Projects</p>
      </div>
      <div class="stat-card" data-aos="zoom-in">
        <h3><?= e($stats['payments']) ?></h3>
        <p>Total Payments</p>
      </div>
    </div>

    <!-- ANALYTICS OVERVIEW -->
    <section class="analytics-section" data-aos="fade-up">
      <h2>Analytics Overview</h2>
      <div class="analytics-cards">
        <div class="analytics-card">
          <h3><?= e($analytics['page_views']) ?></h3>
          <p>Page Views</p>
        </div>
        <div class="analytics-card">
          <h3><?= e($analytics['active_users'] ?? $analytics['visitors']) ?></h3>
          <p>Active Users</p>
        </div>
        <div class="analytics-card">
          <h3><?= e($analytics['avg_session_duration']) ?> sec</h3>
          <p>Avg. Session</p>
        </div>
        <div class="analytics-card">
          <h3><?= e($analytics['bounce_rate']) ?>%</h3>
          <p>Bounce Rate</p>
        </div>
      </div>
    </section>

    <!-- RECENT NOTIFICATIONS -->
    <section class="notifications" data-aos="fade-up">
      <h2>Recent Notifications</h2>
      <?php if (!empty($notifications)): ?>
        <ul class="notification-list">
          <?php foreach ($notifications as $n): ?>
            <li>
              <strong><?= e($n['channel']) ?>:</strong>
              <?= e($n['message']) ?> 
              <span class="time"><?= date('d M H:i', strtotime($n['created_at'])) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>No recent notifications.</p>
      <?php endif; ?>
    </section>

    <!-- RECENT BACKUPS -->
    <section class="backups-section" data-aos="fade-up">
      <h2>Recent Backups</h2>
      <?php if (!empty($recentBackups)): ?>
        <table class="backup-table">
          <thead>
            <tr>
              <th>File Name</th>
              <th>Size</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentBackups as $b): ?>
              <tr>
                <td><?= e($b['name']) ?></td>
                <td><?= e($b['size']) ?></td>
                <td><?= e($b['date']) ?></td>
                <td><a href="/storage/backups/<?= e($b['name']) ?>" class="btn-small">Download</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No backups found.</p>
      <?php endif; ?>
    </section>

    <!-- QUICK ACTIONS -->
    <section class="quick-actions" data-aos="fade-up">
      <h2>Quick Actions</h2>
      <div class="actions-grid">
        <a href="/admin/services" class="action-card">🧩 Manage Services</a>
        <a href="/admin/blogs" class="action-card">📰 Blog CMS</a>
        <a href="/admin/leads" class="action-card">📩 View Leads</a>
        <a href="/admin/media" class="action-card">📸 Media Library</a>
        <a href="/admin/settings" class="action-card">⚙️ Site Settings</a>
        <a href="/admin/backups" class="action-card">💾 System Backups</a>
      </div>
    </section>
  </div>
</section>

<style>
.admin-dashboard {
  padding: 70px 0;
}
.subtitle {
  color: #666;
  margin-bottom: 40px;
}
.admin-stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 24px;
  margin-bottom: 50px;
}
.stat-card {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 14px;
  text-align: center;
  padding: 30px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.03);
}
.stat-card h3 {
  font-size: 2rem;
  color: #007aff;
}
.analytics-section h2,
.notifications h2,
.backups-section h2,
.quick-actions h2 {
  margin-bottom: 20px;
  font-size: 1.5rem;
  color: #111;
}
.analytics-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 20px;
  margin-bottom: 50px;
}
.analytics-card {
  background: #f8f9fb;
  border-radius: 12px;
  padding: 25px;
  text-align: center;
}
.notification-list {
  list-style: none;
  padding-left: 0;
  margin-bottom: 50px;
}
.notification-list li {
  background: #fff;
  border-radius: 10px;
  border: 1px solid #eee;
  padding: 15px 20px;
  margin-bottom: 10px;
  font-size: 0.95rem;
}
.notification-list .time {
  float: right;
  color: #999;
  font-size: 0.8rem;
}
.backup-table {
  width: 100%;
  border-collapse: collapse;
}
.backup-table th, .backup-table td {
  padding: 10px 12px;
  border-bottom: 1px solid #eee;
}
.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin-top: 20px;
}
.action-card {
  background: #007aff;
  color: white;
  border-radius: 12px;
  padding: 18px 20px;
  text-align: center;
  font-weight: 500;
  transition: background 0.2s ease;
}
.action-card:hover {
  background: #005ecb;
}
@media (max-width: 768px) {
  .admin-stats-grid, .analytics-cards, .actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>
