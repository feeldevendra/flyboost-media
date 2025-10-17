<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Thank You Page
 * ------------------------------------------------------------
 * Used after successful:
 * - Contact or Quote submission
 * - Payment confirmation
 * - Newsletter subscription
 * ------------------------------------------------------------
 */
?>

<section class="thank-you-page" data-aos="fade-up">
  <div class="container text-center">
    <div class="success-icon">
      ✅
    </div>
    <h1>Thank You!</h1>
    <p class="message">
      Your submission was received successfully.<br>
      Our team will get in touch with you shortly.
    </p>

    <?php if (!empty($_GET['type']) && $_GET['type'] === 'payment'): ?>
      <p class="subtext">
        Your payment has been processed successfully. A receipt has been sent to your registered email address.
      </p>
    <?php elseif (!empty($_GET['type']) && $_GET['type'] === 'quote'): ?>
      <p class="subtext">
        Your quote request has been received. One of our specialists will review it and respond soon.
      </p>
    <?php else: ?>
      <p class="subtext">
        We appreciate your interest in Flyboost Media — let's create something amazing together!
      </p>
    <?php endif; ?>

    <div class="actions">
      <a href="/" class="btn primary">Back to Home</a>
      <a href="/services" class="btn secondary">Explore Our Services</a>
    </div>
  </div>
</section>

<style>
.thank-you-page {
  padding: 100px 0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  background: #fff;
  color: #111;
  min-height: 80vh;
}
.success-icon {
  font-size: 4rem;
  margin-bottom: 20px;
  animation: popIn 0.6s ease;
}
@keyframes popIn {
  0% { transform: scale(0.3); opacity: 0; }
  80% { transform: scale(1.1); opacity: 1; }
  100% { transform: scale(1); }
}
h1 {
  font-size: 2.4rem;
  margin-bottom: 15px;
  color: #007aff;
}
.message {
  font-size: 1.1rem;
  color: #444;
  margin-bottom: 10px;
}
.subtext {
  font-size: 0.95rem;
  color: #666;
  margin-bottom: 40px;
}
.actions {
  display: flex;
  gap: 15px;
  justify-content: center;
}
.btn {
  display: inline-block;
  border-radius: 8px;
  padding: 12px 28px;
  text-decoration: none;
  transition: 0.25s ease;
  font-weight: 500;
}
.btn.primary {
  background: #007aff;
  color: white;
}
.btn.primary:hover {
  background: #005ecb;
}
.btn.secondary {
  border: 2px solid #007aff;
  color: #007aff;
  background: transparent;
}
.btn.secondary:hover {
  background: #007aff;
  color: white;
}
@media (max-width: 768px) {
  .thank-you-page {
    padding: 60px 20px;
    text-align: center;
  }
  .actions {
    flex-direction: column;
  }
}
</style>
