<?php
class Workout {
    public function __construct(private PDO $db) {}
    public function forMember(int $memberId): array {
        $s=$this->db->prepare('SELECT * FROM workout_plans WHERE member_id=? ORDER BY created_at DESC'); $s->execute([$memberId]); return $s->fetchAll();
    }
}
