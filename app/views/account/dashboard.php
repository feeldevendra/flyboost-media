<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Client Dashboard
 * ------------------------------------------------------------
 * Displays:
 * - Active projects
 * - Subscriptions summary
 * - Recent payments
 * - Unread message notifications
 * ------------------------------------------------------------
 */
?>

<section class="account-dashboard" data-aos="fade-up">
  <div class="container">
    <h1>Welcome back, <?= e($user['name']) ?> 👋</h1>
    <p class="subtitle">Here’s an overview of your account and projects at Flyboost Media.</p>

    <!-- STAT CARDS -->
    <div class="dashboard-cards">
      <div class="card" data-aos="zoom-in">
        <h3><?= count($projects) ?></h3>
        <p>Active Projects</p>
      </div>
      <div class="card" data-aos="zoom-in">
        <h3><?= count($subscriptions) ?></h3>
        <p>Subscriptions</p>
      </div>
      <div class="card" data-aos="zoom-in">
        <h3><?= $unreadCount ?></h3>
        <p>Unread Messages</p>
      </div>
    </div>

    <!-- PROJECTS LIST -->
    <div class="dashboard-section" data-aos="fade-up">
      <h2>Your Projects</h2>
      <?php if (!empty($projects)): ?>
        <div class="projects-grid">
          <?php foreach ($projects as $project): ?>
            <div class="project-card">
              <h3><?= e($project['title']) ?></h3>
              <p><?= e(substr(strip_tags($project['description']), 0, 80)) ?>...</p>
              <div class="progress-bar">
                <div class="progress" style="width:<?= e($project['progress'] ?? 0) ?>%"></div>
              </div>
              <p class="status <?= strtolower($project['status']) ?>">Status: <?= e($project['status']) ?></p>
              <a href="/account/project/<?= e($project['id']) ?>" class="btn-small">View Details</a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p>No active projects found.</p>
      <?php endif; ?>
    </div>

    <!-- SUBSCRIPTIONS -->
    <div class="dashboard-section" data-aos="fade-up">
      <h2>Your Subscriptions</h2>
      <?php if (!empty($subscriptions)): ?>
        <div class="subscriptions-grid">
          <?php foreach ($subscriptions as $sub): ?>
            <div class="subscription-card">
              <h3><?= e($sub['plan_name']) ?></h3>
              <p>Amount: ₹<?= e($sub['amount']) ?> / <?= e($sub['interval_unit']) ?></p>
              <p>Next Billing: <?= e($sub['next_billing_date']) ?></p>
              <span class="badge <?= strtolower($sub['status']) ?>"><?= e($sub['status']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p>You don’t have any active subscriptions yet.</p>
      <?php endif; ?>
    </div>

    <!-- RECENT PAYMENTS -->
    <div class="dashboard-section" data-aos="fade-up">
      <h2>Recent Payments</h2>
      <?php if (!empty($payments)): ?>
        <table class="payment-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($payments as $p): ?>
              <tr>
                <td><?= e($p['order_id']) ?></td>
                <td>₹<?= e($p['amount']) ?></td>
                <td><span class="badge <?= strtolower($p['status']) ?>"><?= e($p['status']) ?></span></td>
                <td><?= date('M d, Y', strtotime($p['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No recent transactions found.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<style>
.account-dashboard {
  padding: 80px 0;
}
.account-dashboard h1 {
  font-size: 2rem;
  color: #111;
  margin-bottom: 10px;
}
.subtitle {
  color: #666;
  margin-bottom: 40px;
}
.dashboard-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 20px;
  margin-bottom: 50px;
}
.dashboard-cards .card {
  background: #fff;
  border-radius: 14px;
  padding: 25px;
  text-align: center;
  border: 1px solid #eee;
  box-shadow: 0 3px 10px rgba(0,0,0,0.03);
}
.dashboard-cards h3 {
  font-size: 2rem;
  color: #007aff;
}
.dashboard-section {
  margin-bottom: 60px;
}
.projects-grid, .subscriptions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 24px;
}
.project-card, .subscription-card {
  background: #fff;
  border-radius: 14px;
  padding: 25px;
  border: 1px solid #eee;
  box-shadow: 0 3px 12px rgba(0,0,0,0.03);
}
.progress-bar {
  background: #f0f0f0;
  border-radius: 10px;
  height: 6px;
  margin: 10px 0;
  overflow: hidden;
}
.progress {
  background: #007aff;
  height: 6px;
}
.badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.85rem;
  color: #fff;
  text-transform: capitalize;
}
.badge.active { background: #28a745; }
.badge.pending { background: #ffb700; color: #111; }
.badge.cancelled { background: #ff3b30; }
.payment-table {
  width: 100%;
  border-collapse: collapse;
}
.payment-table th, .payment-table td {
  padding: 12px 10px;
  border-bottom: 1px solid #eee;
}
.payment-table th {
  text-align: left;
  color: #666;
  font-weight: 600;
}
.payment-table td {
  color: #333;
}
</style>
