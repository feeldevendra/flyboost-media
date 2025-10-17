<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Messages (Client Inbox)
 * ------------------------------------------------------------
 * Displays:
 * - All project chat threads
 * - Message list with send box
 * - Real-time messaging area
 * ------------------------------------------------------------
 */
?>

<section class="messages-page" data-aos="fade-up">
  <div class="container">

    <h1>Messages</h1>
    <p class="subtitle">Stay connected with your project manager and team at Flyboost Media.</p>

    <div class="messages-layout">

      <!-- LEFT: PROJECT THREADS -->
      <aside class="thread-list">
        <h3>Your Projects</h3>
        <ul>
          <?php if (!empty($projects)): ?>
            <?php foreach ($projects as $p): ?>
              <li class="<?= ($project['id'] ?? '') == $p['id'] ? 'active' : '' ?>">
                <a href="/account/messages/<?= e($p['id']) ?>">
                  <?= e($p['title']) ?>
                  <?php $unread = \App\Models\Chat::unreadCount(user('id')); ?>
                  <?php if ($unread > 0): ?>
                    <span class="badge unread"><?= $unread ?></span>
                  <?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="padding:10px; color:#777;">No projects available.</p>
          <?php endif; ?>
        </ul>
      </aside>

      <!-- RIGHT: MESSAGE THREAD -->
      <div class="chat-thread">
        <?php if (!empty($messages)): ?>
          <div class="chat-header">
            <h3><?= e($project['title'] ?? 'Select a Project') ?></h3>
          </div>

          <div id="chatMessages" class="chat-body">
            <?php foreach ($messages as $msg): ?>
              <div class="chat-msg <?= $msg['user_id'] === user('id') ? 'mine' : 'theirs' ?>">
                <div class="chat-bubble">
                  <p><?= nl2br(e($msg['message'])) ?></p>
                  <?php if (!empty($msg['file_path'])): ?>
                    <a href="<?= base_url($msg['file_path']) ?>" target="_blank" class="file-attachment">📎 View Attachment</a>
                  <?php endif; ?>
                  <span class="time"><?= date('H:i', strtotime($msg['created_at'])) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- SEND BOX -->
          <form id="chatForm" action="/account/project/<?= e($project['id']) ?>/message" method="POST" enctype="multipart/form-data">
            <textarea name="message" placeholder="Type your message..." rows="2" required></textarea>
            <div class="chat-actions">
              <input type="file" name="file" id="fileInput" hidden>
              <label for="fileInput" class="upload-btn">📎</label>
              <button type="submit" class="btn primary">Send</button>
            </div>
          </form>
        <?php else: ?>
          <div class="no-thread">
            <p>Select a project to view messages.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<style>
.messages-page {
  padding: 70px 0;
}
.messages-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 24px;
}
.thread-list {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #eee;
  height: 600px;
  overflow-y: auto;
}
.thread-list h3 {
  font-size: 1rem;
  padding: 16px;
  border-bottom: 1px solid #eee;
}
.thread-list ul {
  list-style: none;
  margin: 0;
  padding: 0;
}
.thread-list li {
  border-bottom: 1px solid #f3f3f3;
}
.thread-list li a {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
  color: #333;
  transition: 0.2s ease;
}
.thread-list li.active a,
.thread-list li a:hover {
  background: #007aff;
  color: white;
}
.badge.unread {
  background: #ff3b30;
  color: #fff;
  border-radius: 8px;
  padding: 3px 8px;
  font-size: 0.8rem;
}
.chat-thread {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #eee;
  display: flex;
  flex-direction: column;
  height: 600px;
}
.chat-header {
  padding: 15px 20px;
  border-bottom: 1px solid #eee;
}
.chat-body {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  background: #fafafa;
}
.chat-msg {
  margin-bottom: 10px;
}
.chat-msg.mine {
  text-align: right;
}
.chat-bubble {
  display: inline-block;
  background: #f1f1f1;
  padding: 10px 15px;
  border-radius: 10px;
  max-width: 75%;
}
.chat-msg.mine .chat-bubble {
  background: #007aff;
  color: white;
}
.chat-bubble .time {
  display: block;
  font-size: 0.75rem;
  opacity: 0.7;
  margin-top: 4px;
}
.file-attachment {
  display: inline-block;
  margin-top: 6px;
  font-size: 0.9rem;
  color: #ffd700;
}
#chatForm {
  display: flex;
  flex-direction: column;
  border-top: 1px solid #eee;
  padding: 10px 20px;
}
#chatForm textarea {
  border-radius: 8px;
  border: 1px solid #ddd;
  padding: 10px;
  resize: none;
  margin-bottom: 10px;
}
.chat-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.upload-btn {
  cursor: pointer;
  font-size: 1.4rem;
  color: #555;
  padding: 4px 8px;
  border-radius: 8px;
}
.upload-btn:hover {
  background: #eee;
}
@media (max-width: 768px) {
  .messages-layout {
    grid-template-columns: 1fr;
  }
  .thread-list {
    height: auto;
  }
  .chat-thread {
    height: auto;
  }
}
</style>
