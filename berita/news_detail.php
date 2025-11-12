<?php
include "../CiviCore/db.php";

// Check for slug in URL
if (isset($_GET['slug']) && $_GET['slug'] !== '') {
    $slug = $conn->real_escape_string($_GET['slug']);
    $sql  = "SELECT * FROM news WHERE slug = '$slug' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $news = $result->fetch_assoc();
    } else {
        // No news found with this slug
        http_response_code(404);
        echo "<h1>404 - Berita Tidak Ditemukan</h1>";
        exit;
    }
} else {
    // No slug provided
    http_response_code(404);
    echo "<h1>404 - Berita Tidak Ditemukan</h1>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-65T4XSDM2Q"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-65T4XSDM2Q');
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $news['title']; ?></title>
  <link href="../CiviCore/berita/news_detail.css" rel="stylesheet" type="text/css">
  <link href="../headerFooter.css" rel="stylesheet" type="text/css">
</head>
<body>

<div class="article">
  <h1><?php echo $news['title']; ?></h1>
  <div class="meta">
    Dipublish oleh: <?php echo htmlspecialchars($news['created_by']); ?> | <?php echo date("j F Y, H:i", strtotime($news['created_at'])); ?>
    <br><br>Category: <?php echo $news['category']; ?></div>

  <figure>
    <img src="../CiviCore/berita/<?php echo $news['image']; ?>" alt="Trulli" style="width:100%">
    <figcaption style="text-align: center;"><?php echo $news['image_desc']; ?></figcaption>
  </figure>

  <p><?php echo nl2br($news['content']); ?></p>
</div>
<!------------------- FOOTER ----------------------------------->	
<div class="gotoTop" onclick="topFunction()" id="myBtn" title="Go to top"> <img src="../icon/go_to_top.png"></div>

<div id="footer"></div>
<script>
fetch("../footer.php")
  .then(response => response.text())
  .then(data => {
    document.getElementById("footer").innerHTML = data;
  });
</script>
<!------------------- BATAS AKHIR CONTENT ---------------------------------->
</body>
</html>
