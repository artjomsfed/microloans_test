<?php
declare(strict_types=1);


class FunctionAccessChecker
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function isAccessAllowed(string $userName, string $functionName): bool
    {
        $isGrantedForGroup = $this->isAccessGrantedForGroup($userName, $functionName);
        if ($isGrantedForGroup) {
            return true;
        }

        return $this->isAccessGrantedForUser($userName, $functionName);
    }

    private function isAccessGrantedForGroup(string $userName, string $functionName): bool
    {
        $statement = $this->pdo->prepare('
            SELECT
                COUNT(*) AS granted_count 
            FROM users u
            LEFT JOIN group_module_rights gmr ON u.group_id = gmr.group_id
            LEFT JOIN modules m ON gmr.module_id = m.module_id
            LEFT JOIN functions gf ON gf.module_id = m.module_id
            LEFT JOIN group_function_rights gfr ON u.group_id = gfr.group_id
            LEFT JOIN functions f ON gfr.function_id = f.function_id
            WHERE u.username = :username
            AND (f.function_name = :functionName OR gf.function_name = :functionName)
        ');

        $statement->execute(['username' => $userName, 'functionName' => $functionName]);
        $result = $statement->fetch();

        return isset($result['granted_count']) && $result['granted_count'] > 0;
    }

    private function isAccessGrantedForUser(string $userName, string $functionName): bool
    {
        $statement = $this->pdo->prepare('
            SELECT
                COUNT(*) AS granted_count
            FROM users u
            LEFT JOIN user_module_rights umr ON u.user_id = umr.user_id
            LEFT JOIN modules m ON umr.module_id = m.module_id
            LEFT JOIN functions mf ON mf.module_id = m.module_id
            LEFT JOIN user_function_rights ufr ON u.user_id = ufr.user_id
            LEFT JOIN functions f ON ufr.function_id = f.function_id
            WHERE u.username = :username
            AND (f.function_name = :functionName OR mf.function_name = :functionName)
        ');

        $statement->execute(['username' => $userName, 'functionName' => $functionName]);
        $result = $statement->fetch();

        return isset($result['granted_count']) && $result['granted_count'] > 0;
    }
}