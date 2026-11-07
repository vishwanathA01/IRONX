<?php
class Payment {
    public function __construct(private PDO $db) {}
    public function history(int $userId): array {
        $s=$this->db->prepare('SELECT * FROM payments WHERE user_id=? ORDER BY created_at DESC'); $s->execute([$userId]); return $s->fetchAll();
    }
}
