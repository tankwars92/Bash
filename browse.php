<?php
require_once 'config.php';

$page = isset($_GET['num']) ? (int)$_GET['num'] : 1;
$page = max(1, $page); 

$quotes_per_page = 10;

$stmt = $pdo->query("SELECT COUNT(*) FROM quotes WHERE approved = 1");
$total_quotes = $stmt->fetchColumn();

$total_pages = ceil($total_quotes / $quotes_per_page);

$page = min($page, $total_pages);

$offset = ($page - 1) * $quotes_per_page;

$stmt = $pdo->prepare("SELECT * FROM quotes WHERE approved = 1 ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->execute([$quotes_per_page, $offset]);
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagination_links = [];
$pagination_links[] = $page == 1 ? '<b>1</b>' : '<a href="browse.php?num=1">1</a>';

$max_show = min(6, $total_pages);
for ($i = 2; $i <= $max_show; $i++) {
    if ($i == $page) {
        $pagination_links[] = '<b>' . $i . '</b>';
    } else {
        $pagination_links[] = '<a href="browse.php?num=' . $i . '">' . $i . '</a>';
    }
}

if ($total_pages > 6) {
    $pagination_links[] = '...';
    if ($page == $total_pages) {
        $pagination_links[] = '<b>' . $total_pages . '</b>';
    } else {
        $pagination_links[] = '<a href="browse.php?num=' . $total_pages . '">' . $total_pages . '</a>';
    }
}
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
			[<a href="index.php">последние 10</a>] [<a href="quote.php">случайные 10</a>] [<a href="index.php?type=best">лучшие 10</a>] [<b>все цитаты</b>]  [<a href="add.php">добавить</a>] [<a href="search.php">поиск</a>]
		</td>
	</tr>
	<tr>
		<td align="justify" colspan="2"><form action="browse.php" method="get" name="chg">
			[<?php echo implode('] [', $pagination_links); ?>] 
			<?php if ($page < $total_pages): ?>
				- [<a href="browse.php?num=<?php echo $page + 1; ?>">следующая страница</a>]
			<?php endif; ?> <br>
			На страницу: <select name="num">
				<?php for ($i = 1; $i <= $total_pages; $i++): ?>
					<option value="<?php echo $i; ?>" <?php echo $i == $page ? 'selected="selected"' : ''; ?>><?php echo $i; ?></option>
				<?php endfor; ?>
			</select><input type="submit" value="Перейти"></form>
		</td>
	</tr>
<?php foreach ($quotes as $quote): ?>
				<tr>
		<td align="left" colspan="2">
			<table width="100%" cellpadding="0" cellspacing="5">
				<tbody><tr> 
					<td><a href="quote.php?num=<?php echo $quote['id']; ?>"><?php echo $quote['id']; ?></a> <?php echo generateVoteLinks($pdo, $quote['id'], $quote['rating'], 'browse.php', 'num=' . $page); ?>   Approved by <?php echo htmlspecialchars($quote['approved_by']); ?>, <?php echo date('Y-m-d H:i:s', strtotime($quote['approved_at'])); ?>					</td>
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
		<td align="justify" colspan="2"><form action="browse.php" method="get" name="chg">
			[<?php echo implode('] [', $pagination_links); ?>] 
			<?php if ($page < $total_pages): ?>
				- [<a href="browse.php?num=<?php echo $page + 1; ?>">следующая страница</a>]
			<?php endif; ?> <br>
			На страницу: <select name="num">
				<?php for ($i = 1; $i <= $total_pages; $i++): ?>
					<option value="<?php echo $i; ?>" <?php echo $i == $page ? 'selected="selected"' : ''; ?>><?php echo $i; ?></option>
				<?php endfor; ?>
			</select><input type="submit" value="Перейти"></form>
		</td>
	</tr>
	<tr>
		<td align="justify" colspan="2">
			[<a href="index.php">последние 10</a>] [<a href="quote.php">случайные 10</a>] [<a href="index.php?type=best">лучшие 10</a>] [<b>все цитаты</b>]  [<a href="add.php">добавить</a>] [<a href="search.php">поиск</a>]<br><br>
			<hr><span class="small">General idea and stuff (c)bash.org crew, local version (c)DarkRider, 2004  </span>
		</td>
	</tr>
	
</tbody></table>

</td></tr>
</tbody></table>

</body></html>
