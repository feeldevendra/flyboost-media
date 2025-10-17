<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Media Controller
 * ------------------------------------------------------------
 * Handles:
 * - Admin media upload
 * - Media listing & preview
 * - Deletion from storage
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\Media;
use App\Config;

class MediaController
{
    /**
     * Display Media Library page
     */
    public function index(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $media = Media::all(300);

        $meta = [
            'title' => 'Media Library | Admin',
            'description' => 'Upload and manage images, PDFs, and assets used across Flyboost Media.'
        ];

        view('admin/media', compact('media', 'meta'));
    }

    /**
     * Handle file upload via form or AJAX
     */
    public function upload(): void
    {
        if (!hasRole('ADMIN')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if (empty($_FILES['file'])) {
            echo json_encode(['success' => false, 'message' => 'No file provided.']);
            return;
        }

        $file = $_FILES['file'];
        $user = user();
        $filePath = Media::handleUpload($file, $user['id']);

        if ($filePath) {
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully!', 'path' => $filePath]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or failed upload.']);
        }
    }

    /**
     * Delete a media file (AJAX)
     */
    public function delete(): void
    {
        if (!hasRole('ADMIN')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Missing media ID.']);
            return;
        }

        if (Media::delete($id)) {
            echo json_encode(['success' => true, 'message' => 'File deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete file.']);
        }
    }
}
