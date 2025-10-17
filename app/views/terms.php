<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Terms & Conditions Page
 * ------------------------------------------------------------
 * Defines usage terms, client obligations, and service disclaimers.
 * ------------------------------------------------------------
 */
?>

<section class="terms-page" data-aos="fade-up">
  <div class="container">
    <h1>Terms & Conditions</h1>
    <p class="intro">
      Welcome to <strong>Flyboost Media</strong>. By using our website or services, you agree to the terms and conditions described below.
      Please read them carefully before proceeding with any project or purchase.
    </p>

    <h2>1. Service Engagement</h2>
    <p>
      By signing a project agreement or making a payment, you engage Flyboost Media to provide digital marketing, development, or related services
      as per the scope defined in your proposal. Any change in scope requires written approval and may affect cost or delivery timelines.
    </p>

    <h2>2. Payment & Billing</h2>
    <p>
      Payments must be made according to the agreed schedule. Flyboost Media reserves the right to pause work on overdue accounts.
      All invoices are payable in INR (₹) unless otherwise specified. Payment gateways (Cashfree, Razorpay, Stripe) process transactions securely.
    </p>

    <h2>3. Refund Policy</h2>
    <p>
      Due to the nature of digital services, all confirmed payments are non-refundable once the work has commenced.
      Exceptions may apply in case of duplicate transactions or service unavailability, as determined by management.
    </p>

    <h2>4. Intellectual Property</h2>
    <p>
      Upon full payment, clients receive rights to use the final project deliverables. Flyboost Media retains the right to display
      non-confidential work samples in portfolios and case studies. Source files remain our property unless stated otherwise.
    </p>

    <h2>5. Confidentiality</h2>
    <p>
      Both parties agree to maintain confidentiality of shared data, credentials, and project information.
      Flyboost Media ensures that all staff and partners adhere to strict non-disclosure standards.
    </p>

    <h2>6. Client Responsibilities</h2>
    <ul>
      <li>Provide timely feedback, content, and approvals to avoid delays.</li>
      <li>Ensure all materials provided (text, images, media) are legally owned or licensed.</li>
      <li>Maintain communication channels during project execution.</li>
    </ul>

    <h2>7. Warranties & Limitations</h2>
    <p>
      Flyboost Media makes no guarantee of specific marketing or SEO results. While we use best practices,
      final performance depends on third-party algorithms, audience behavior, and client consistency.
    </p>

    <h2>8. Termination</h2>
    <p>
      Either party may terminate the agreement with written notice if the other fails to fulfill obligations.
      In such cases, completed work up to the termination date will be billed accordingly.
    </p>

    <h2>9. Liability</h2>
    <p>
      Flyboost Media is not liable for indirect, incidental, or consequential damages arising from the use of our services or website.
      Our maximum liability is limited to the total amount paid by the client for the specific project.
    </p>

    <h2>10. Governing Law</h2>
    <p>
      These Terms & Conditions are governed by the laws of India. Any disputes shall be subject to the jurisdiction of courts in Indore, Madhya Pradesh.
    </p>

    <h2>11. Contact Information</h2>
    <p>
      For questions or concerns regarding these Terms, please contact us at:
    </p>
    <address>
      Flyboost Media Pvt. Ltd.<br>
      Indore Tech Park, Madhya Pradesh, India<br>
      Email: <a href="mailto:hello@flyboostmedia.com">hello@flyboostmedia.com</a>
    </address>

    <p class="update-date">Last updated: <?= date('F Y') ?></p>
  </div>
</section>

<style>
.terms-page {
  padding: 80px 0;
}
.terms-page h1 {
  font-size: 2.2rem;
  margin-bottom: 20px;
  color: #111;
}
.terms-page .intro {
  font-size: 1.05rem;
  color: #555;
  margin-bottom: 30px;
  line-height: 1.7;
}
.terms-page h2 {
  color: #007aff;
  margin-top: 30px;
  margin-bottom: 10px;
  font-size: 1.25rem;
}
.terms-page p, 
.terms-page li {
  color: #333;
  line-height: 1.8;
  margin-bottom: 10px;
}
.terms-page ul {
  margin: 10px 0 20px 20px;
}
.terms-page address {
  font-style: normal;
  color: #444;
  margin-top: 10px;
}
.terms-page a {
  color: #007aff;
  text-decoration: none;
}
.terms-page a:hover {
  text-decoration: underline;
}
.update-date {
  text-align: right;
  font-size: 0.9rem;
  color: #777;
  margin-top: 40px;
}
@media (max-width: 768px) {
  .terms-page {
    padding: 50px 20px;
  }
}
</style>
