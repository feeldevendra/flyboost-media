<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Admin Settings Page
 * ------------------------------------------------------------
 * Allows admin to:
 * - Update general site settings
 * - Manage API keys, chatbot, analytics
 * - Configure payment gateways & AdSense
 * ------------------------------------------------------------
 */
?>

<section class="admin-settings" data-aos="fade-up">
  <div class="container">
    <h1>Site Settings</h1>
    <p class="subtitle">Manage Flyboost Media configuration — branding, analytics, chatbot, and more.</p>

    <!-- SETTINGS FORM -->
    <form id="settingsForm" action="/admin/settings/save" method="POST">
      <!-- TABS -->
      <div class="tabs">
        <button type="button" class="tab-btn active" data-tab="general">🌐 General</button>
        <button type="button" class="tab-btn" data-tab="branding">🎨 Branding</button>
        <button type="button" class="tab-btn" data-tab="seo">🔍 SEO</button>
        <button type="button" class="tab-btn" data-tab="payments">💳 Payments</button>
        <button type="button" class="tab-btn" data-tab="chatbot">🤖 Chatbot</button>
        <button type="button" class="tab-btn" data-tab="analytics">📈 Analytics</button>
        <button type="button" class="tab-btn" data-tab="adsense">💰 AdSense</button>
        <button type="button" class="tab-btn" data-tab="smtp">📨 SMTP</button>
      </div>

      <!-- TAB CONTENTS -->
      <div class="tab-content active" id="general">
        <h2>🌐 General Settings</h2>
        <div class="form-grid">
          <div class="form-group">
            <label>Site Name</label>
            <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Base URL</label>
            <input type="text" name="base_url" value="<?= e($settings['base_url'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Admin Email</label>
            <input type="email" name="admin_email" value="<?= e($settings['admin_email'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="tab-content" id="branding">
        <h2>🎨 Branding</h2>
        <div class="form-grid">
          <div class="form-group">
            <label>Logo (Light)</label>
            <input type="file" name="logo_light">
          </div>
          <div class="form-group">
            <label>Logo (Dark)</label>
            <input type="file" name="logo_dark">
          </div>
          <div class="form-group">
            <label>Primary Color</label>
            <input type="color" name="primary_color" value="<?= e($settings['primary_color'] ?? '#007aff') ?>">
          </div>
        </div>
      </div>

      <div class="tab-content" id="seo">
        <h2>🔍 SEO & Meta</h2>
        <div class="form-grid">
          <div class="form-group">
            <label>Default Meta Title</label>
            <input type="text" name="default_meta_title" value="<?= e($settings['default_meta_title'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Default Meta Description</label>
            <textarea name="default_meta_desc"><?= e($settings['default_meta_desc'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <div class="tab-content" id="payments">
        <h2>💳 Payment Gateways</h2>
        <div class="form-grid">
          <div class="form-group">
            <label>Cashfree App ID</label>
            <input type="text" name="CASHFREE_APP_ID" value="<?= e($settings['CASHFREE_APP_ID'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Cashfree Secret Key</label>
            <input type="text" name="CASHFREE_SECRET_KEY" value="<?= e($settings['CASHFREE_SECRET_KEY'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Razorpay Key ID</label>
            <input type="text" name="RAZORPAY_KEY_ID" value="<?= e($settings['RAZORPAY_KEY_ID'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Stripe Secret Key</label>
            <input type="text" name="STRIPE_SECRET" value="<?= e($settings['STRIPE_SECRET'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="tab-content" id="chatbot">
        <h2>🤖 Chatbot Configuration</h2>
        <div class="form-grid">
          <div class="form-group">
            <label>Chatbot Type</label>
            <select name="CHATBOT_TYPE">
              <option value="dialogflow" <?= ($settings['CHATBOT_TYPE'] ?? '') === 'dialogflow' ? 'selected' : '' ?>>Dialogflow</option>
              <option value="custom" <?= ($settings['CHATBOT_TYPE'] ?? '') === 'custom' ? 'selected' : '' ?>>Custom AI</option>
            </select>
          </div>
          <div class="form-group">
            <label>Dialogflow Project ID</label>
            <input type="text" name="DIALOGFLOW_PROJECT_ID" value="<?= e($settings['DIALOGFLOW_PROJECT_ID'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>OpenAI API Key</label>
            <input type="text" name="OPENAI_API_KEY" value="<?= e($settings['OPENAI_API_KEY'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="tab-content" id="analytics">
        <h2>📈 Analytics & Integrations</h2>
        <div class="form-grid">
          <div class="form-group">
            <label>Google Analytics ID</label>
            <input type="text" name="GOOGLE_ANALYTICS_ID" value="<?= e($settings['GOOGLE_ANALYTICS_ID'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Matomo URL</label>
            <input type="text" name="MATOMO_URL" value="<?= e($settings['MATOMO_URL'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Matomo Token</label>
            <input type="text" name="MATOMO_TOKEN" value="<?= e($settings['MATOMO_TOKEN'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="tab-content" id="adsense">
        <h2>💰 Google AdSense</h2>
        <div class="form-grid">
          <div class="form-group">
            <label>AdSense Client ID</label>
            <input type="text" name="ADSENSE_CLIENT_ID" value="<?= e($settings['ADSENSE_CLIENT_ID'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Ad Slot ID</label>
            <input type="text" name="ADSENSE_SLOT_ID" value="<?= e($settings['ADSENSE_SLOT_ID'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="tab-content" id="smtp">
        <h2>📨 Email (SMTP)</h2>
        <div class="form-grid">
          <div class="form-group">
            <label>SMTP Host</label>
            <input type="text" name="SMTP_HOST" value="<?= e($settings['SMTP_HOST'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>SMTP Username</label>
            <input type="text" name="SMTP_USER" value="<?= e($settings['SMTP_USER'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>SMTP Password</label>
            <input type="password" name="SMTP_PASS" value="<?= e($settings['SMTP_PASS'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>SMTP Port</label>
            <input type="number" name="SMTP_PORT" value="<?= e($settings['SMTP_PORT'] ?? '587') ?>">
          </div>
        </div>
      </div>

      <button type="submit" class="btn primary save-btn">💾 Save All Settings</button>
    </form>
  </div>
</section>

<style>
.admin-settings {
  padding: 70px 0;
}
.subtitle {
  color: #666;
  margin-bottom: 30px;
}
.tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 25px;
}
.tab-btn {
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  background: #f0f0f0;
  color: #333;
  cursor: pointer;
  transition: 0.2s ease;
}
.tab-btn.active {
  background: #007aff;
  color: #fff;
}
.tab-content {
  display: none;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #eee;
  padding: 25px;
  margin-bottom: 40px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.03);
}
.tab-content.active {
  display: block;
}
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}
.form-group label {
  display: block;
  margin-bottom: 6px;
  color: #555;
}
.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 10px;
}
.save-btn {
  display: block;
  margin: 0 auto;
  padding: 12px 30px;
  font-weight: 600;
}
@media (max-width: 768px) {
  .tabs {
    flex-direction: column;
  }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab-btn");
  const contents = document.querySelectorAll(".tab-content");

  tabs.forEach(btn => {
    btn.addEventListener("click", () => {
      tabs.forEach(b => b.classList.remove("active"));
      contents.forEach(c => c.classList.remove("active"));
      btn.classList.add("active");
      document.getElementById(btn.dataset.tab).classList.add("active");
    });
  });

  // Handle Save via AJAX
  document.getElementById("settingsForm").addEventListener("submit", async e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch(e.target.action, { method: "POST", body: formData });
    const data = await res.json();
    showToast(data.message, data.success ? "success" : "error");
  });
});
</script>
