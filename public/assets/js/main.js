/* ------------------------------------------------------------
   Flyboost Media 360° — Main Frontend Script
   ------------------------------------------------------------
   Handles:
   - Mobile menu toggle
   - Smooth scroll animations
   - AJAX contact & quote forms
   - Toast notifications
   ------------------------------------------------------------ */

// ========== MOBILE NAVIGATION ==========
document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.getElementById("menuToggle");
  const navMenu = document.getElementById("navMenu");

  if (menuToggle && navMenu) {
    menuToggle.addEventListener("click", () => {
      navMenu.classList.toggle("active");
      menuToggle.classList.toggle("open");
    });
  }

  // ========== SMOOTH SCROLL FOR INTERNAL LINKS ==========
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function (e) {
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        e.preventDefault();
        window.scrollTo({
          top: target.offsetTop - 70,
          behavior: "smooth"
        });
      }
    });
  });
});

// ========== TOAST NOTIFICATIONS ==========
function showToast(message, type = "success") {
  let toast = document.createElement("div");
  toast.className = `toast ${type}`;
  toast.innerText = message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.classList.add("visible");
  }, 100);

  setTimeout(() => {
    toast.classList.remove("visible");
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}

// ========== AJAX FORM HANDLER (Contact + Quote) ==========
function handleAjaxForm(formSelector) {
  const form = document.querySelector(formSelector);
  if (!form) return;

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const action = this.getAttribute("action");

    try {
      const res = await fetch(action, {
        method: "POST",
        body: formData
      });
      const data = await res.json();

      if (data.success) {
        showToast(data.message || "Form submitted successfully!");
        this.reset();
      } else {
        showToast(data.message || "Something went wrong!", "error");
      }
    } catch (error) {
      console.error(error);
      showToast("Network error. Try again later.", "error");
    }
  });
}

// ========== INIT FORMS ==========
document.addEventListener("DOMContentLoaded", () => {
  handleAjaxForm("#contactForm");
  handleAjaxForm("#quoteForm");
});

// ========== TOAST STYLES ==========
const style = document.createElement("style");
style.innerHTML = `
.toast {
  position: fixed;
  bottom: 40px;
  right: 20px;
  background: #007aff;
  color: white;
  padding: 14px 22px;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 500;
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.3s ease;
  z-index: 10000;
}
.toast.error {
  background: #ff3b30;
}
.toast.visible {
  opacity: 1;
  transform: translateY(0);
}
`;
document.head.appendChild(style);
