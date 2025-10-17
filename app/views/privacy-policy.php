<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Privacy Policy Page
 * ------------------------------------------------------------
 * Static informational page with optional dynamic editing.
 * ------------------------------------------------------------
 */
?>

<section class="privacy-page" data-aos="fade-up">
  <div class="container">
    <h1>Privacy Policy</h1>
    <p class="intro">At <strong>Flyboost Media</strong>, we value your privacy and are committed to protecting your personal information. This Privacy Policy outlines how we collect, use, and safeguard your data when you interact with our services.</p>

    <h2>1. Information We Collect</h2>
    <p>We may collect personal details such as your name, email, phone number, and company information when you request quotes, contact us, or sign up for our services. We also collect usage analytics to improve user experience.</p>

    <h2>2. How We Use Your Information</h2>
    <ul>
      <li>To provide, maintain, and improve our services.</li>
      <li>To respond to inquiries and support requests.</li>
      <li>To send marketing or service-related communications (with your consent).</li>
      <li>To ensure website security and prevent unauthorized access.</li>
    </ul>

    <h2>3. Data Protection</h2>
    <p>We use encryption, secure hosting, and regular system audits to safeguard your data. Your information is never shared or sold to third parties, except as required by law or to fulfill legitimate service functions.</p>

    <h2>4. Cookies & Analytics</h2>
    <p>Our website uses cookies and analytics tools like Google Analytics to enhance your experience. You can disable cookies in your browser at any time.</p>

    <h2>5. Payment Information</h2>
    <p>For online transactions, payment details are processed securely through integrated payment gateways (Cashfree, Razorpay, or Stripe). We do not store your card or banking information on our servers.</p>

    <h2>6. Your Rights</h2>
    <p>You have the right to access, correct, or delete your data at any time. To make such requests, please contact us at <a href="mailto:privacy@flyboostmedia.com">privacy@flyboostmedia.com</a>.</p>

    <h2>7. Updates to this Policy</h2>
    <p>We may update this Privacy Policy periodically. The latest version will always be available on this page with the effective date.</p>

    <h2>8. Contact Us</h2>
    <p>If you have any questions or concerns about this Privacy Policy, please contact us at:</p>
    <address>
      Flyboost Media Pvt. Ltd.<br>
      Indore Tech Park, Madhya Pradesh, India<br>
      Email: <a href="mailto:hello@flyboostmedia.com">hello@flyboostmedia.com</a>
    </address>

    <p class="update-date">Last updated: <?= date('F Y') ?></p>
  </div>
</section>

<style>
.privacy-page {
  padding: 80px 0;
}
.privacy-page h1 {
  font-size: 2.2rem;
  margin-bottom: 20px;
  color: #111;
}
.privacy-page .intro {
  font-size: 1.05rem;
  color: #555;
  margin-bottom: 30px;
  line-height: 1.7;
}
.privacy-page h2 {
  color: #007aff;
  margin-top: 30px;
  margin-bottom: 10px;
  font-size: 1.25rem;
}
.privacy-page p, 
.privacy-page li {
  color: #333;
  line-height: 1.8;
  margin-bottom: 10px;
}
.privacy-page ul {
  margin: 10px 0 20px 20px;
}
.privacy-page address {
  font-style: normal;
  color: #444;
  margin-top: 10px;
}
.privacy-page a {
  color: #007aff;
  text-decoration: none;
}
.privacy-page a:hover {
  text-decoration: underline;
}
.update-date {
  text-align: right;
  font-size: 0.9rem;
  color: #777;
  margin-top: 40px;
}
@media (max-width: 768px) {
  .privacy-page {
    padding: 50px 20px;
  }
}
</style>
