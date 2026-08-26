<?php

function auditEnsureLoginTable($conn): bool {
    if (!$conn) {
        return false;
    }

    $sql = "CREATE TABLE IF NOT EXISTS AUDIT_LOGIN_LOGS (
        AUDIT_ID INT AUTO_INCREMENT PRIMARY KEY,
        ACTOR_TYPE VARCHAR(20) NOT NULL,
        USER_ID INT NULL,
        ADMIN_NUMBER INT NULL,
        USERNAME VARCHAR(255) NULL,
        EMAIL VARCHAR(255) NULL,
        LOGIN_METHOD VARCHAR(50) NOT NULL DEFAULT 'password',
        IP_ADDRESS VARCHAR(45) NULL,
        USER_AGENT VARCHAR(500) NULL,
        CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_audit_login_created_at (CREATED_AT),
        INDEX idx_audit_login_actor_type (ACTOR_TYPE),
        INDEX idx_audit_login_user_id (USER_ID),
        INDEX idx_audit_login_admin_number (ADMIN_NUMBER)
    )";

    return (bool) db_query($conn, $sql);
}

function auditClientIp(): string {
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
        $value = trim((string) ($_SERVER[$key] ?? ''));
        if ($value === '') {
            continue;
        }

        $parts = explode(',', $value);
        return trim($parts[0]);
    }

    return '';
}

function auditRecordLogin($conn, string $actorType, array $actor, string $loginMethod): void {
    if (!auditEnsureLoginTable($conn)) {
        return;
    }

    db_query(
        $conn,
        "INSERT INTO AUDIT_LOGIN_LOGS
            (ACTOR_TYPE, USER_ID, ADMIN_NUMBER, USERNAME, EMAIL, LOGIN_METHOD, IP_ADDRESS, USER_AGENT)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        [
            $actorType,
            $actor['user_id'] ?? null,
            $actor['admin_number'] ?? null,
            $actor['username'] ?? null,
            $actor['email'] ?? null,
            $loginMethod,
            auditClientIp(),
            substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500)
        ]
    );
}

