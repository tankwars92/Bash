<?php
require_once 'config.php';

$quote_id = isset($_GET['num']) ? (int)$_GET['num'] : 0;
$action = isset($_GET['act']) ? $_GET['act'] : '';
$return_page = isset($_GET['return']) ? $_GET['return'] : 'quote.php';
$return_params = isset($_GET['params']) ? $_GET['params'] : '';

if ($quote_id > 0 && in_array($action, ['up', 'down'])) {
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM votes WHERE quote_id = ? AND ip_address = ?");
        $stmt->execute([$quote_id, $ip_address]);
        
        if ($stmt->fetch()) {
			
        } else {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("INSERT INTO votes (quote_id, ip_address, vote_type) VALUES (?, ?, ?)");
            $stmt->execute([$quote_id, $ip_address, $action]);
            
            if ($action == 'up') {
                $stmt = $pdo->prepare("UPDATE quotes SET rating = rating + 1 WHERE id = ? AND approved = 1");
            } else {
                $stmt = $pdo->prepare("UPDATE quotes SET rating = rating - 1 WHERE id = ? AND approved = 1");
            }
            $stmt->execute([$quote_id]);
            
            $pdo->commit();
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
}

$redirect_url = $return_page;
if ($return_params) {
    $redirect_url .= '?' . $return_params;
} elseif ($return_page == 'quote.php') {
    $redirect_url .= "?num=$quote_id";
}

header("Location: $redirect_url");
exit;
?>
