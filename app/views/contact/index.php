<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Contact Page
 * ------------------------------------------------------------
 * Includes:
 * - Company contact info
 * - Embedded Google Map
 * - AJAX-powered contact form
 * ------------------------------------------------------------
 */
?>
<section class="contact-hero" data-aos="fade-up">
  <div class="container contact-hero-content">
    <h1>Let's Build Something Amazing Together</h1>
    <p>We’d love to hear from you — whether you’re planning a project, need a quote, or just want to say hello.</p>
  </div>
</section>

<section class="contact-section" data-aos="fade-up">
  <div class="container contact-grid">
    
    <!-- Contact Info -->
    <div class="contact-info">
      <h2>Get in Touch</h2>
      <p>We’re here to help you scale your digital presence with innovative web, app, and marketing solutions.</p>

      <div class="info-block">
        <strong>Email:</strong>
        <a href="mailto:hello@flyboostmedia.com">hello@flyboostmedia.com</a>
      </div>

      <div class="info-block">
        <strong>Phone:</strong>
        <a href="tel:+919876543210">+91 98765 43210</a>
      </div>

      <div class="info-block">
        <strong>Address:</strong>
        <p>WorkLoft, 4th Floor, Indore Tech Park, MP 452001</p>
      </div>

      <div class="social-links">
        <a href="#" target="_blank"><img src="/assets/img/icons/facebook.svg" alt="Facebook"></a>
        <a href="#" target="_blank"><img src="/assets/img/icons/linkedin.svg" alt="LinkedIn"></a>
        <a href="#" target="_blank"><img src="/assets/img/icons/twitter.svg" alt="Twitter"></a>
        <a href="#" target="_blank"><img src="/assets/img/icons/instagram.svg" alt="Instagram"></a>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="contact-form">
      <h2>Send Us a Message</h2>

      <form id="contactForm" action="/contact/submit" method="POST">
        <div class="form-group">
          <label for="name">Full Name *</label>
          <input type="text" name="name" id="name" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
          <label for="email">Email Address *</label>
          <input type="email" name="email" id="email" placeholder="you@example.com" required>
        </div>

        <div class="form-group">
          <label for="phone">Phone Number *</label>
          <input type="text" name="phone" id="phone" placeholder="+91 9876543210" required>
        </div>

        <div class="form-group">
          <label for="service">Interested In</label>
          <select name="service" id="service">
            <option value="Website Development">Website Development</option>
            <option value="App Development">App Development</option>
            <option value="Digital Marketing">Digital Marketing</option>
            <option value="Branding & Design">Branding & Design</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div class="form-group">
          <label for="message">Message *</label>
          <textarea name="message" id="message" rows="4" placeholder="Tell us more about your project" required></textarea>
        </div>

        <button type="submit" class="btn primary">Submit Message</button>
      </form>
    </div>
  </div>
</section>

<!-- MAP SECTION -->
<section class="map-section" data-aos="fade-up">
  <div class="map-container">
    <iframe 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3682.694750139268!2d75.8655!3d22.7239!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39631d81b33e8d5f%3A0x894218a2c90d2f5!2sIndore%20Tech%20Park!5e0!3m2!1sen!2sin!4v1700000000000"
      width="100%" height="420" style="border:0; border-radius: 16px;" allowfullscreen="" loading="lazy">
    </iframe>
  </div>
</section>
