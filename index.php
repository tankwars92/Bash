<?php
require_once 'config.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'latest';
$title = 'последние 10';
$sql = "SELECT * FROM quotes WHERE approved = 1";

switch ($type) {
    case 'random':
        $title = 'случайные 10';
        $sql .= " ORDER BY RANDOM() LIMIT 10";
        break;
    case 'best':
        $title = 'лучшие 10';
        $sql .= " ORDER BY rating DESC LIMIT 10";
        break;
    case 'latest':
    default:
        $title = 'последние 10';
        $sql .= " ORDER BY id DESC LIMIT 10";
        break;
}

$stmt = $pdo->query($sql);
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>

	<title>bash.org.ru - Цитатник Рунета</title>
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
			[<?php echo $type == 'latest' ? '<b>последние 10</b>' : '<a href="index.php?type=latest">последние 10</a>'; ?>] [<?php echo $type == 'random' ? '<b>случайные 10</b>' : '<a href="index.php?type=random">случайные 10</a>'; ?>] [<?php echo $type == 'best' ? '<b>лучшие 10</b>' : '<a href="index.php?type=best">лучшие 10</a>'; ?>] [<a href="browse.php">все цитаты</a>]  [<a href="add.php">добавить</a>] [<a href="search.php">поиск</a>]
		</td>
	</tr>
<?php foreach ($quotes as $quote): ?>
			<tr>
		<td align="left" colspan="2">
			<table width="100%" cellpadding="0" cellspacing="5">
				<tbody><tr> 
					<td><a href="quote.php?num=<?php echo $quote['id']; ?>"><?php echo $quote['id']; ?></a> <?php echo generateVoteLinks($pdo, $quote['id'], $quote['rating'], 'index.php', 'type=' . $type); ?>   Approved by <?php echo htmlspecialchars($quote['approved_by']); ?>, <?php echo date('Y-m-d H:i:s', strtotime($quote['approved_at'])); ?>					</td>
				</tr>
				<tr>
					<td class="dat">
						<?php echo nl2br(htmlspecialchars($quote['text'])); ?>
					</td>
				</tr>
			</tbody></table>
		</td>
	</tr>
<?php endforeach; ?>
			<tr>
		<td align="justify" colspan="2">
			[<?php echo $type == 'latest' ? '<b>последние 10</b>' : '<a href="index.php?type=latest">последние 10</a>'; ?>] [<?php echo $type == 'random' ? '<b>случайные 10</b>' : '<a href="index.php?type=random">случайные 10</a>'; ?>] [<?php echo $type == 'best' ? '<b>лучшие 10</b>' : '<a href="index.php?type=best">лучшие 10</a>'; ?>] [<a href="browse.php">все цитаты</a>]  [<a href="add.php">добавить</a>] [<a href="search.php">поиск</a>]<br><br>
			<hr><span class="small">General idea and stuff (c)bash.org crew, local version (c)DarkRider, 2004  </span>
		</td>
	</tr>

</tbody></table>

</td></tr>
</tbody></table>

</body></html>
