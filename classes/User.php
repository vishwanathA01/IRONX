<?php
class User {
    public function __construct(private PDO $db) {}
    public function findByEmail(string $email): array|false {
        $s = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $s->execute([$email]); return $s->fetch();
    }
    public function create(string $name, string $email, string $password, string $role = 'member'): int {
        $s = $this->db->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)');
        $s->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
        return (int)$this->db->lastInsertId();
    }
}
