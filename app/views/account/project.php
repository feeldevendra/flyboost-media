<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Project Detail Page
 * ------------------------------------------------------------
 * Displays:
 * - Project overview and milestones
 * - File attachments
 * - Real-time chat section
 * ------------------------------------------------------------
 */
?>

<section class="project-detail" data-aos="fade-up">
  <div class="container">
    <a href="/account" class="back-link">← Back to Dashboard</a>
    <h1><?= e($project['title']) ?></h1>
    <p class="project-meta">
      Status: <span class="badge <?= strtolower($project['status']) ?>"><?= e($project['status']) ?></span>
      | Progress: <?= e($project['progress']) ?>%
    </p>

    <div class="project-layout">

      <!-- LEFT: PROJECT DETAILS -->
      <div class="project-info">
        <h3>Overview</h3>
        <div class="description"><?= $project['description'] ?></div>

        <?php if (!empty($project['milestones'])): ?>
          <h3>Milestones</h3>
          <ul class="milestone-list">
            <?php foreach (json_decode($project['milestones'], true) as $m): ?>
              <li>
                <span class="dot <?= $m['completed'] ? 'done' : '' ?>"></span>
                <?= e($m['title']) ?> — <em><?= e($m['deadline']) ?></em>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <?php if (!empty($project['files'])): ?>
          <h3>Files</h3>
          <ul class="file-list">
            <?php foreach (json_decode($project['files'], true) as $file): ?>
              <li>
                <a href="<?= base_url($file['path']) ?>" target="_blank"><?= e($file['name']) ?></a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <!-- RIGHT: CHAT BOX -->
      <div class="project-chat">
        <h3>Project Chat</h3>
        <div id="chatMessages" class="chat-messages">
          <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $msg): ?>
              <div class="chat-msg <?= $msg['user_id'] === user('id') ? 'mine' : 'theirs' ?>">
                <div class="chat-bubble">
                  <p><?= nl2br(e($msg['message'])) ?></p>
                  <span class="time"><?= date('H:i', strtotime($msg['created_at'])) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="no-msg">No messages yet. Start the conversation below 👇</p>
          <?php endif; ?>
        </div>

        <form id="chatForm" action="/account/project/<?= e($project['id']) ?>/message" method="POST" enctype="multipart/form-data">
          <textarea name="message" id="message" rows="2" placeholder="Type your message..." required></textarea>
          <div class="chat-actions">
            <input type="file" name="file" id="file" style="display:none;">
            <label for="file" class="upload-btn">📎</label>
            <button type="submit" class="btn primary">Send</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<style>
.project-detail {
  padding: 60px 0;
}
.back-link {
  display: inline-block;
  margin-bottom: 15px;
  color: #007aff;
}
.project-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
}
.project-info h3 {
  margin-top: 25px;
  color: #111;
}
.description {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}
.milestone-list {
  list-style: none;
  padding-left: 0;
}
.milestone-list li {
  position: relative;
  padding-left: 25px;
  margin-bottom: 10px;
  color: #444;
}
.milestone-list .dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #bbb;
  position: absolute;
  top: 7px;
  left: 0;
}
.milestone-list .dot.done {
  background: #28a745;
}
.file-list li {
  margin-bottom: 8px;
}
.file-list a {
  color: #007aff;
  text-decoration: none;
}
.file-list a:hover {
  text-decoration: underline;
}

/* CHAT SECTION */
.project-chat {
  background: #fff;
  border-radius: 14px;
  border: 1px solid #eee;
  box-shadow: 0 3px 8px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
  height: 600px;
  overflow: hidden;
}
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  background: #fafafa;
}
.chat-msg {
  margin-bottom: 12px;
}
.chat-msg.mine {
  text-align: right;
}
.chat-bubble {
  display: inline-block;
  padding: 10px 15px;
  border-radius: 12px;
  background: #f1f1f1;
  color: #222;
  max-width: 75%;
  position: relative;
}
.chat-msg.mine .chat-bubble {
  background: #007aff;
  color: white;
}
.chat-bubble .time {
  display: block;
  font-size: 0.75rem;
  opacity: 0.7;
  margin-top: 5px;
}
#chatForm {
  display: flex;
  flex-direction: column;
  padding: 10px 20px 15px;
  border-top: 1px solid #eee;
  background: #fff;
}
#chatForm textarea {
  resize: none;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
  margin-bottom: 10px;
}
.chat-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.upload-btn {
  font-size: 1.3rem;
  cursor: pointer;
  color: #555;
  padding: 4px 8px;
  border-radius: 8px;
  transition: 0.2s ease;
}
.upload-btn:hover {
  background: #eee;
}
@media (max-width: 768px) {
  .project-layout {
    grid-template-columns: 1fr;
  }
  .project-chat {
    height: auto;
  }
}
</style>
