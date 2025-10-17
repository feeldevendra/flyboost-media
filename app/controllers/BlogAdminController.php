<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Blog Admin Controller
 * ------------------------------------------------------------
 * Handles:
 * - Blog CRUD (Create, Read, Update, Delete)
 * - AdSense visibility toggle
 * - Auto-post to social media
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\Blog;
use App\Models\Notification;
use App\Config;

class BlogAdminController
{
    /**
     * List all blog posts
     */
    public function list(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $blogs = Blog::all(200);

        $meta = [
            'title' => 'Manage Blogs | Admin',
            'description' => 'Create, edit, and manage blog posts for Flyboost Media.'
        ];

        view('admin/blogs/list', compact('blogs', 'meta'));
    }

    /**
     * Render blog creation form
     */
    public function create(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $meta = [
            'title' => 'Create Blog Post | Admin',
            'description' => 'Write and publish new blog posts with AdSense and SEO settings.'
        ];

        view('admin/blogs/create', compact('meta'));
    }

    /**
     * Handle blog creation (POST)
     */
    public function createPost(): void
    {
        if (!hasRole('ADMIN')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $data = [
            'title' => $_POST['title'] ?? '',
            'slug' => strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $_POST['slug'] ?? $_POST['title']))),
            'content' => $_POST['content'] ?? '',
            'feature_image' => $_POST['feature_image'] ?? '',
            'meta_title' => $_POST['meta_title'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
            'show_ads' => isset($_POST['show_ads']) ? 1 : 0
        ];

        if (Blog::save($data)) {
            if ($data['is_published']) {
                Blog::autoPostSocial($data);
                Notification::broadcast([
                    'subject' => '📰 New Blog Published!',
                    'message' => "A new blog post was published: {$data['title']}",
                ]);
            }
            echo json_encode(['success' => true, 'message' => 'Blog created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create blog.']);
        }
    }

    /**
     * Edit existing blog post
     */
    public function edit(string $id): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $pdo = \App\DB::conn();
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id=:id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $blog = $stmt->fetch();

        if (!$blog) {
            view('errors/404');
            return;
        }

        $meta = [
            'title' => 'Edit Blog Post | Admin',
            'description' => 'Modify blog content, SEO, and ad settings.'
        ];

        view('admin/blogs/edit', compact('blog', 'meta'));
    }

    /**
     * Save blog edits (POST)
     */
    public function editPost(string $id): void
    {
        if (!hasRole('ADMIN')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $data = [
            'title' => $_POST['title'] ?? '',
            'slug' => strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $_POST['slug'] ?? $_POST['title']))),
            'content' => $_POST['content'] ?? '',
            'feature_image' => $_POST['feature_image'] ?? '',
            'meta_title' => $_POST['meta_title'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
            'show_ads' => isset($_POST['show_ads']) ? 1 : 0
        ];

        if (Blog::save($data, (int) $id)) {
            if ($data['is_published']) {
                Blog::autoPostSocial($data);
                Notification::broadcast([
                    'subject' => '📰 Blog Updated & Published',
                    'message' => "Blog '{$data['title']}' has been updated and published.",
                ]);
            }

            echo json_encode(['success' => true, 'message' => 'Blog updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update blog.']);
        }
    }

    /**
     * Delete blog post
     */
    public function delete(string $id): void
    {
        if (!hasRole('ADMIN')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if (Blog::delete((int) $id)) {
            echo json_encode(['success' => true, 'message' => 'Blog deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete blog.']);
        }
    }
}
