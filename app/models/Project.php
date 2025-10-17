<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Project Model
 * ------------------------------------------------------------
 * Manages client projects, milestones, files, discussions,
 * and progress tracking for the Client Portal.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;

class Project
{
    /**
     * Fetch all projects (Admin/Project Manager)
     */
    public static function all(int $limit = 100): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM projects ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch projects assigned to a specific client
     */
    public static function byClient(int $client_id): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM projects WHERE client_id = :id ORDER BY created_at DESC");
        $stmt->execute(['id' => $client_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get a specific project with milestones & files
     */
    public static function find(int $id): ?array
    {
        $pdo = DB::conn();

        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $project = $stmt->fetch();
        if (!$project) return null;

        // Attach milestones
        $ms = $pdo->prepare("SELECT * FROM project_milestones WHERE project_id = :pid ORDER BY sort_order ASC");
        $ms->execute(['pid' => $id]);
        $project['milestones'] = $ms->fetchAll();

        // Attach files
        $fs = $pdo->prepare("SELECT * FROM project_files WHERE project_id = :pid ORDER BY uploaded_at DESC");
        $fs->execute(['pid' => $id]);
        $project['files'] = $fs->fetchAll();

        // Attach discussions
        $ds = $pdo->prepare("SELECT * FROM project_discussions WHERE project_id = :pid ORDER BY created_at ASC");
        $ds->execute(['pid' => $id]);
        $project['discussions'] = $ds->fetchAll();

        return $project;
    }

    /**
     * Create or update a project
     */
    public static function save(array $data, ?int $id = null): bool
    {
        $pdo = DB::conn();

        if ($id) {
            $stmt = $pdo->prepare("UPDATE projects 
                SET title=:title, description=:description, client_id=:client_id, 
                    manager_id=:manager_id, status=:status, deadline=:deadline, updated_at=NOW() 
                WHERE id=:id");
            return $stmt->execute([
                'title' => $data['title'],
                'description' => $data['description'],
                'client_id' => $data['client_id'],
                'manager_id' => $data['manager_id'],
                'status' => $data['status'],
                'deadline' => $data['deadline'],
                'id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO projects 
                (title, description, client_id, manager_id, status, deadline, created_at) 
                VALUES (:title, :description, :client_id, :manager_id, :status, :deadline, NOW())");
            return $stmt->execute([
                'title' => $data['title'],
                'description' => $data['description'],
                'client_id' => $data['client_id'],
                'manager_id' => $data['manager_id'],
                'status' => $data['status'] ?? 'PLANNED',
                'deadline' => $data['deadline'] ?? null
            ]);
        }
    }

    /**
     * Add a milestone to a project
     */
    public static function addMilestone(int $project_id, string $title, string $due_date): bool
    {
        $stmt = DB::conn()->prepare("INSERT INTO project_milestones 
            (project_id, title, due_date, status, sort_order) 
            VALUES (:pid, :title, :due, 'PENDING', 
            (SELECT IFNULL(MAX(sort_order),0)+1 FROM project_milestones WHERE project_id=:pid2))");
        return $stmt->execute([
            'pid' => $project_id,
            'title' => $title,
            'due' => $due_date,
            'pid2' => $project_id
        ]);
    }

    /**
     * Upload a file to project
     */
    public static function addFile(int $project_id, string $filename, string $path, int $uploaded_by): bool
    {
        $stmt = DB::conn()->prepare("INSERT INTO project_files 
            (project_id, filename, path, uploaded_by, uploaded_at) 
            VALUES (:pid, :file, :path, :uid, NOW())");
        return $stmt->execute([
            'pid' => $project_id,
            'file' => $filename,
            'path' => $path,
            'uid' => $uploaded_by
        ]);
    }

    /**
     * Add a discussion message
     */
    public static function addMessage(int $project_id, int $user_id, string $message): bool
    {
        $stmt = DB::conn()->prepare("INSERT INTO project_discussions 
            (project_id, user_id, message, created_at) 
            VALUES (:pid, :uid, :msg, NOW())");
        return $stmt->execute([
            'pid' => $project_id,
            'uid' => $user_id,
            'msg' => $message
        ]);
    }

    /**
     * Update milestone status
     */
    public static function updateMilestoneStatus(int $milestone_id, string $status): bool
    {
        $stmt = DB::conn()->prepare("UPDATE project_milestones SET status=:st, updated_at=NOW() WHERE id=:id");
        return $stmt->execute([
            'st' => strtoupper($status),
            'id' => $milestone_id
        ]);
    }

    /**
     * Delete project and all related data (Admin)
     */
    public static function delete(int $id): bool
    {
        $pdo = DB::conn();

        $pdo->prepare("DELETE FROM project_files WHERE project_id=:id")->execute(['id' => $id]);
        $pdo->prepare("DELETE FROM project_milestones WHERE project_id=:id")->execute(['id' => $id]);
        $pdo->prepare("DELETE FROM project_discussions WHERE project_id=:id")->execute(['id' => $id]);
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }
}
