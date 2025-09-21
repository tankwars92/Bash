<?php
session_start();
require_once 'config.php';

$admin_password = 'admin123'; // Обязательно измените!
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $is_logged_in = true;
    } else {
        $error = 'Неверный пароль';
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    $is_logged_in = false;
}

if ($is_logged_in && isset($_POST['action'])) {
    $quote_id = (int)$_POST['quote_id'];
    $action = $_POST['action'];
    
    if ($action == 'approve') {
        $stmt = $pdo->prepare("UPDATE quotes SET approved = 1, approved_at = datetime('now'), approved_by = 'Administrator' WHERE id = ?");
        $stmt->execute([$quote_id]);
    } elseif ($action == 'reject') {
        $stmt = $pdo->prepare("DELETE FROM quotes WHERE id = ? AND approved = 0");
        $stmt->execute([$quote_id]);
    }
}

if (!$is_logged_in) {
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html>
    <head>
        <title>Админ-панель</title>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body link="#0000FF" vlink="#0000FF" alink="#0000FF">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr><td align="center">
                <table width="600" cellpadding="0" cellspacing="5">
                    <tr>
                        <td align="center">
                            <h2>Вход в админ-панель</h2>
                        </td>
                    </tr>
                    <?php if (isset($error)): ?>
                    <tr>
                        <td align="center">
                            <font color="RED"><i><?php echo htmlspecialchars($error); ?></i></font>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td align="center">
                            <form method="POST">
                                <table cellpadding="5" cellspacing="0">
                                    <tr>
                                        <td>Пароль:</td>
                                        <td><input type="password" name="password" size="20"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="center">
                                            <input type="submit" name="login" value="Войти">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                    </tr>
                </table>
            </td></tr>
        </table>
    </body>
    </html>
    <?php
    exit;
}

$stmt = $pdo->query("SELECT * FROM quotes WHERE approved = 0 ORDER BY created_at DESC");
$pending_quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>Админ-панель</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body link="#0000FF" vlink="#0000FF" alink="#0000FF">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr><td align="center">
            <table width="800" cellpadding="0" cellspacing="5">
                <tr>
                    <td align="center" colspan="2">
                        <h2>Админ-панель</h2>
                    </td>
                </tr>
                <tr>
                    <td align="left">
                        <b>Цитаты на рассмотрении (<?php echo count($pending_quotes); ?>)</b>
                    </td>
                    <td align="right">
                        <form method="POST" style="display: inline;">
                            <input type="submit" name="logout" value="Выйти">
                        </form>
                    </td>
                </tr>
                
                <?php if (empty($pending_quotes)): ?>
                <tr>
                    <td colspan="2" align="center">
                        <i>Нет цитат на рассмотрении.</i>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($pending_quotes as $quote): ?>
                    <tr>
                        <td colspan="2">
                            <table width="100%" cellpadding="5" cellspacing="0" border="1" bordercolor="#ccc">
                                <tr>
                                    <td colspan="2">
                                        <b>ID: <?php echo $quote['id']; ?></b> | 
                                        Добавлена: <?php echo date('Y-m-d H:i:s', strtotime($quote['created_at'])); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="dat">
                                        <?php echo nl2br(htmlspecialchars($quote['text'])); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="quote_id" value="<?php echo $quote['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="submit" value="Одобрить">
                                        </form>
                                    </td>
                                    <td align="right">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="quote_id" value="<?php echo $quote['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="submit" value="Отклонить" onclick="return confirm('Удалить цитату?')">
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <tr>
                    <td colspan="2" align="center">
                        <br>
                        <a href="index.php">← Вернуться на главную</a>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
