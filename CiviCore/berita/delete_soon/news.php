<?php
include "../db.php"; // your DB connection file

// Fetch latest news
$result = $conn->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News & Updates</title>
  <link href="news.css" rel="stylesheet" type="text/css">
</head>
<body>

<header>
  <h1>News & Updates</h1>
</header>

<div class="container">
  <!-- News List -->
  <div class="news-list">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="news-item">
        <img src="<?php echo $row['image']; ?>" alt="News">
        <div class="news-content">
          <h2><?php echo $row['title']; ?></h2>
          <p><?php echo substr($row['content'], 0, 120) . "..."; ?></p>
          <a href="news_detail.php?id=<?php echo $row['id']; ?>" target="_blank">Read More</a>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <h3>Latest News</h3>
    <ul>
      <?php
      $latest = $conn->query("SELECT id, title FROM news ORDER BY created_at DESC LIMIT 5");
      while ($n = $latest->fetch_assoc()):
      ?>
        <li><a href="news_detail.php?id=<?php echo $n['id']; ?>"><?php echo $n['title']; ?></a></li>
      <?php endwhile; ?>
    </ul>

    <h3>Categories</h3>
    <ul>
      <?php
      $cats = $conn->query("SELECT DISTINCT category FROM news");
      while ($c = $cats->fetch_assoc()):
      ?>
        <li><a href="news.php?category=<?php echo urlencode($c['category']); ?>">
          <?php echo $c['category']; ?></a></li>
      <?php endwhile; ?>
    </ul>
  </div>
</div>

</body>
</html>
