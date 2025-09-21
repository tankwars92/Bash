<?php
require_once 'config.php';

$quote_id = isset($_GET['num']) ? (int)$_GET['num'] : 0;
$show_single_quote = $quote_id > 0;

if ($show_single_quote) {
    $stmt = $pdo->prepare("SELECT * FROM quotes WHERE id = ? AND approved = 1");
    $stmt->execute([$quote_id]);
    $quote = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$quote) {
        $quote = [
            'id' => 0,
            'text' => 'Цитата не найдена',
            'rating' => 0,
            'approved_by' => 'System',
            'approved_at' => date('Y-m-d H:i:s')
        ];
    }
} else {
    $stmt = $pdo->query("SELECT * FROM quotes WHERE approved = 1 ORDER BY RANDOM() LIMIT 10");
    $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>

	<title>bash.org.ru - Цитатник Рунета</title>
	<link rel="STYLESHEET" type="text/css" href="files/quote.css">
	<link rel="alternate" title="Bash.org.ru RSS" href="rss/" type="application/rss+xml">
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
			[<a href="index.php">последние 10</a>] [<?php echo $show_single_quote ? '<a href="quote.php">случайные 10</a>' : '<b>случайные 10</b>'; ?>] [<a href="index.php?type=best">лучшие 10</a>] [<a href="browse.php">все цитаты</a>]  [<a href="add.php">добавить</a>] [<a href="search.php">поиск</a>]
		</td>
	</tr>
<?php if ($show_single_quote): ?>
			<tr>
		<td align="left" colspan="2">
			<table width="100%" cellpadding="0" cellspacing="5">
				<tbody><tr> 
					<td><a href="quote.php?num=<?php echo $quote['id']; ?>"><?php echo $quote['id']; ?></a> <?php echo generateVoteLinks($pdo, $quote['id'], $quote['rating'], 'quote.php', $quote_id > 0 ? 'num=' . $quote_id : ''); ?>   Approved by <?php echo htmlspecialchars($quote['approved_by']); ?>, <?php echo date('Y-m-d H:i:s', strtotime($quote['approved_at'])); ?>					</td>
				</tr>
				<tr>
					<td class="dat">
						<?php echo nl2br(htmlspecialchars($quote['text'])); ?>
					</td>
				</tr>
			</tbody></table>
		</td>
	</tr>
<?php else: ?>
<?php foreach ($quotes as $quote): ?>
			<tr>
		<td align="left" colspan="2">
			<table width="100%" cellpadding="0" cellspacing="5">
				<tbody><tr> 
					<td><a href="quote.php?num=<?php echo $quote['id']; ?>"><?php echo $quote['id']; ?></a> <?php echo generateVoteLinks($pdo, $quote['id'], $quote['rating'], 'quote.php'); ?>   Approved by <?php echo htmlspecialchars($quote['approved_by']); ?>, <?php echo date('Y-m-d H:i:s', strtotime($quote['approved_at'])); ?>					</td>
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
<?php endif; ?>

	<tr>
		<td align="justify" colspan="2">
			[<a href="index.php">последние 10</a>] [<?php echo $show_single_quote ? '<a href="quote.php">случайные 10</a>' : '<b>случайные 10</b>'; ?>] [<a href="index.php?type=best">лучшие 10</a>] [<a href="browse.php">все цитаты</a>]  [<a href="add.php">добавить</a>] [<a href="search.php">поиск</a>]<br><br>
			<hr><span class="small">General idea and stuff (c)bash.org crew, local version (c)DarkRider, 2004  </span>
		</td>
	</tr>

</tbody></table>

</td></tr>
</tbody></table>

</body></html>
