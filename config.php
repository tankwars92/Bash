<?php
$db_path = 'quotes.db';

try {
    $pdo = new PDO("sqlite:$db_path");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS quotes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            text TEXT NOT NULL,
            rating INTEGER DEFAULT 0,
            approved BOOLEAN DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            approved_at DATETIME,
            approved_by VARCHAR(100)
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS votes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            quote_id INTEGER,
            ip_address VARCHAR(45),
            vote_type VARCHAR(10),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(quote_id, ip_address)
        )
    ");
    
    
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

function hasVoted($pdo, $quote_id) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("SELECT id FROM votes WHERE quote_id = ? AND ip_address = ?");
    $stmt->execute([$quote_id, $ip_address]);
    return $stmt->fetch() !== false;
}

function generateVoteLinks($pdo, $quote_id, $rating, $return_page = '', $return_params = '') {
    $has_voted = hasVoted($pdo, $quote_id);
    
    if ($has_voted) {
        return '[ + ' . $rating . ' - ]';
    } else {
        $params = '';
        if ($return_params) {
            $params = '&amp;params=' . urlencode($return_params);
        }
        
        return '[ <a href="chrate.php?num=' . $quote_id . '&amp;act=up&amp;return=' . $return_page . $params . '">+</a> ' . $rating . ' <a href="chrate.php?num=' . $quote_id . '&amp;act=down&amp;return=' . $return_page . $params . '">-</a> ]';
    }
}
?>
