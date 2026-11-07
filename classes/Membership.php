<?php
class Membership {
    public function __construct(private PDO $db) {}
    public function plans(): array { return $this->db->query('SELECT * FROM membership_plans WHERE is_active=1 ORDER BY price')->fetchAll(); }
    public function activeFor(int $userId): array|false {
        $s=$this->db->prepare("SELECT s.*, p.name AS plan_name FROM subscriptions s JOIN membership_plans p ON p.id=s.plan_id WHERE s.user_id=? AND s.status='active' AND s.end_date>=CURDATE() ORDER BY s.end_date DESC LIMIT 1");
        $s->execute([$userId]); return $s->fetch();
    }
}
