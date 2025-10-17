<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Admin Media Library
 * ------------------------------------------------------------
 * Allows admin to:
 * - Upload new media files (images, videos, PDFs)
 * - Preview and copy URLs
 * - Delete media files
 * ------------------------------------------------------------
 */
?>

<section class="admin-media" data-aos="fade-up">
  <div class="container">
    <h1>Media Library</h1>
    <p class="subtitle">Upload and manage all your images, videos, and assets used across Flyboost Media.</p>

    <!-- UPLOAD AREA -->
    <div class="upload-area" id="uploadArea">
      <form id="uploadForm" action="/admin/media/upload" method="POST" enctype="multipart/form-data">
        <input type="file" id="fileInput" name="file" required hidden>
        <div class="upload-zone" id="dropZone">
          <p>📤 Drag & drop files here or <span id="browseTrigger">browse</span></p>
        </div>
        <button type="submit" class="btn primary">Upload</button>
      </form>
    </div>

    <!-- MEDIA GRID -->
    <div class="media-grid" id="mediaGrid">
      <?php if (!empty($media)): ?>
        <?php foreach ($media as $m): ?>
          <div class="media-card" data-aos="zoom-in">
            <?php if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $m['file_path'])): ?>
              <img src="<?= base_url($m['file_path']) ?>" alt="<?= e($m['file_name']) ?>">
            <?php else: ?>
              <div class="file-icon">📁</div>
            <?php endif; ?>

            <div class="media-actions">
              <p class="file-name"><?= e($m['file_name']) ?></p>
              <div class="buttons">
                <button class="btn-small copy-btn" data-url="<?= base_url($m['file_path']) ?>">Copy URL</button>
                <button class="btn-small danger delete-btn" data-id="<?= e($m['id']) ?>">Delete</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center; color:#777;">No media uploaded yet.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<style>
.admin-media {
  padding: 70px 0;
}
.subtitle {
  color: #666;
  margin-bottom: 25px;
}
.upload-area {
  background: #f8f9fb;
  border: 2px dashed #ccc;
  border-radius: 14px;
  padding: 40px;
  text-align: center;
  margin-bottom: 40px;
  transition: 0.3s ease;
}
.upload-area.dragover {
  background: #e8f1ff;
  border-color: #007aff;
}
.upload-zone p {
  font-size: 1rem;
  color: #333;
}
.upload-zone span {
  color: #007aff;
  cursor: pointer;
  text-decoration: underline;
}
.media-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}
.media-card {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #eee;
  overflow: hidden;
  box-shadow: 0 3px 8px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 15px;
}
.media-card img {
  width: 100%;
  height: 150px;
  object-fit: cover;
  border-radius: 10px;
  margin-bottom: 10px;
}
.file-icon {
  font-size: 3rem;
  color: #007aff;
  margin-bottom: 10px;
}
.media-actions {
  text-align: center;
  width: 100%;
}
.media-actions .file-name {
  font-size: 0.9rem;
  color: #555;
  margin-bottom: 10px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.buttons {
  display: flex;
  justify-content: center;
  gap: 10px;
}
.btn-small {
  background: #007aff;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 0.85rem;
  cursor: pointer;
}
.btn-small.danger {
  background: #ff3b30;
}
@media (max-width: 768px) {
  .upload-area {
    padding: 30px 20px;
  }
  .media-card img {
    height: 120px;
  }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const dropZone = document.getElementById("dropZone");
  const fileInput = document.getElementById("fileInput");
  const browseTrigger = document.getElementById("browseTrigger");
  const form = document.getElementById("uploadForm");

  // Drag & Drop functionality
  dropZone.addEventListener("dragover", e => {
    e.preventDefault();
    dropZone.parentElement.classList.add("dragover");
  });

  dropZone.addEventListener("dragleave", e => {
    e.preventDefault();
    dropZone.parentElement.classList.remove("dragover");
  });

  dropZone.addEventListener("drop", e => {
    e.preventDefault();
    dropZone.parentElement.classList.remove("dragover");
    fileInput.files = e.dataTransfer.files;
  });

  browseTrigger.addEventListener("click", () => fileInput.click());

  // AJAX upload
  form.addEventListener("submit", async e => {
    e.preventDefault();
    const formData = new FormData(form);
    const res = await fetch(form.action, { method: "POST", body: formData });
    const data = await res.json();

    showToast(data.message, data.success ? "success" : "error");
    if (data.success) setTimeout(() => location.reload(), 1200);
  });

  // COPY URL
  document.querySelectorAll(".copy-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      navigator.clipboard.writeText(btn.dataset.url);
      showToast("Copied to clipboard!");
    });
  });

  // DELETE FILE
  document.querySelectorAll(".delete-btn").forEach(btn => {
    btn.addEventListener("click", async () => {
      if (!confirm("Delete this file permanently?")) return;
      const id = btn.dataset.id;
      const res = await fetch("/admin/media/delete", {
        method: "POST",
        body: new URLSearchParams({ id })
      });
      const data = await res.json();
      showToast(data.message, data.success ? "success" : "error");
      if (data.success) setTimeout(() => location.reload(), 1000);
    });
  });
});
</script>
