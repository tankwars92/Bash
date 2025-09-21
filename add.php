<?php
session_start();
require_once 'config.php';

$message = '';
$success = false;

if (isset($_GET['success']) && $_GET['success'] == '1') {
    $message = 'Спасибо! Ваша цитата отправлена на рассмотрение. Пожалуйста, ждите, пока ваша цитата будет одобрена администратором.';
    $success = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['text'])) {
    $text = trim($_POST['text']);
    
    if (empty($text)) {
        $message = 'Пожалуйста, введите текст цитаты.';
    } elseif (strlen($text) < 10) {
        $message = 'Цитата слишком короткая. Минимум 10 символов.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO quotes (text, approved) VALUES (?, 0)");
            $stmt->execute([$text]);
            
            header("Location: add.php?success=1");
            exit;
        } catch (PDOException $e) {
            $message = 'Ошибка при сохранении цитаты. Попробуйте еще раз.';
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>

	<title>bash.org.ru - Цитатник Рунета</title>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="icon" href="favicon.ico" type="image/x-icon">
	<link rel="STYLESHEET" type="text/css" href="files/main.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body link="#0000FF" vlink="#0000FF" alink="#0000FF">
 

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tbody><tr><td align="center">
<table width="800" cellpadding="0" cellspacing="5">
	<tbody><tr>
		<td><img src="files/logo.gif" width="300" height="40" alt="" border="0">
		</td>
		<td width="100%" align="center"> <p class="vbig">bash.org.ru - Цитатник Рунета</p>
		</td>
	</tr>
	<tr>
		<td align="justify" colspan="2">
			[<a href="index.php">последние 10</a>] [<a href="quote.php">случайные 10</a>] [<a href="index.php?type=best">лучшие 10</a>] [<a href="browse.php">все цитаты</a>] [<b>добавить</b>] [<a href="search.php">поиск</a>]
		</td>
	</tr>
	<tr>
		<td colspan="2"> Внимание, отправка цитаты на рассмотрение не означает
 стопроцентную вероятность появления ее на сайте. Дело в том, что 
чувство юмора админов не обязательно аналогично вашему.
		Если цитата вызовет вывих челюсти у всего администраторского состава, 
она (цитата, а не челюсть), скорее всего, уйдет в /dev/null.
		</td>
	</tr>
<?php if ($message): ?>
	<tr>
		<td colspan="2">
			<?php if ($success): ?>
				<i><?php echo htmlspecialchars($message); ?></i>
			<?php else: ?>
				<font color="RED"><i><?php echo htmlspecialchars($message); ?></i></font>
			<?php endif; ?>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<td class="dat" colspan="2">
			<form method="POST" action="add.php">
				<textarea name="text" cols="60" rows="30"><?php echo isset($_POST['text']) && !$success ? htmlspecialchars($_POST['text']) : ''; ?></textarea><br>
				<input type="submit" value="Отправить">
			</form>
		</td>
	</tr>
	<tr>
		<td align="justify" colspan="2">
			[<a href="index.php">последние 10</a>] [<a href="quote.php">случайные 10</a>] [<a href="index.php?type=best">лучшие 10</a>] [<a href="browse.php">все цитаты</a>] [<b>добавить</b>] [<a href="search.php">поиск</a>]<br><br>
			<hr><span class="small">General idea and stuff (c)bash.org crew, local version (c)DarkRider, 2004  </span>
		</td>
	</tr>

</tbody></table>

</td></tr>
</tbody></table>

</body></html>
