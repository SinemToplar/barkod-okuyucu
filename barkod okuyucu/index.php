<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barcode_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Ürün adına göre arama
if (isset($_POST['searchProductName'])) {
    $searchProductName = $_POST['searchProductName'];
    $sql = "SELECT * FROM products WHERE product_name LIKE '%$searchProductName%'";
} else {
    $sql = "SELECT * FROM products";
}

// Barkod'a göre arama
if (isset($_POST['searchBarcode'])) {
    $searchBarcode = $_POST['searchBarcode'];
    $sql = "SELECT * FROM products WHERE barcode LIKE '%$searchBarcode%'";
} else {
    $sql = "SELECT * FROM products";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ürün Listesi</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        function showProductDetails(productName, price) {
            document.getElementById("urun_detaylari").innerHTML = "Ürün Adı: " + productName + " - Fiyat: " + price;
        }
    </script>
</head>
<body>

<h2>Ürün Listesi</h2>

<!-- Ürün adı arama formu -->
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    Ürün Adı: <input type="text" name="searchProductName">
    <input type="submit" value="Ara">
    <button onclick="window.location.href='<?php echo $_SERVER['PHP_SELF'];?>'">Geri Dön</button>
</form>

<!-- Barkod arama formu -->
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    Barkod: <input type="text" name="searchBarcode">
    <input type="submit" value="Ara">
    <button onclick="window.location.href='<?php echo $_SERVER['PHP_SELF'];?>'">Geri Dön</button>
</form>

<!-- Ürünlerin listelendiği tablo -->
<table>
    <tr>
        <th>Barkod</th>
        <th>İşlemler</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><a href='#' onclick='showProductDetails(\"" . $row["product_name"] . "\", \"" . $row["price"] . "\")'>" . $row["barcode"] . "</a></td>";
            echo "<td>";
            echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
            echo "<input type='hidden' name='barkod' value='".$row["barcode"]."'>";
            echo "<input type='submit' name='sil' value='Sil' onclick='return confirm(\"Bu ürünü silmek istediğinize emin misiniz?\")'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='2'>Ürün bulunamadı.</td></tr>";
    }
    ?>
</table>

<!-- Yeni ürün ekleme formu -->
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    Barkod: <input type="text" name="barkod">
    Ürün Adı: <input type="text" name="urun_ad">
    Fiyat: <input type="text" name="fiyat">
    <input type="submit" name="ekle" value="Yeni Ürün Ekle">
</form>

<!-- Ürün detayları -->
<div id="urun_detaylari"></div>

</body>
</html>

<?php
// Yeni ürün ekleme işlemi
if(isset($_POST['ekle'])) {
    $barkod = $_POST['barkod'];
    $urun_ad = $_POST['urun_ad'];
    $fiyat = $_POST['fiyat'];

    $sql_ekle = "INSERT INTO products (barcode, product_name, price) VALUES ('$barkod', '$urun_ad', '$fiyat')";

    if ($conn->query($sql_ekle) === TRUE) {
        echo '<script>alert("Yeni ürün başarıyla eklendi.");</script>';
        // Sayfayı yenilemek için yönlendirme yapılıyor
        echo '<script>window.location.href="' . $_SERVER['PHP_SELF'] . '";</script>';
    } else {
        echo "Ekleme işlemi sırasında bir hata oluştu: " . $conn->error;
    }
}

// Silme işlemi
if(isset($_POST['sil'])) {
    $barkod = $_POST['barkod'];
    $sql_sil = "DELETE FROM products WHERE barcode='$barkod'";
    if ($conn->query($sql_sil) === TRUE) {
        echo '<script>alert("Ürün başarıyla silindi.");</script>';
        // Sayfayı yenilemek için yönlendirme yapılıyor
        echo '<script>window.location.href="' . $_SERVER['PHP_SELF'] . '";</script>';
    } else {
        echo "Silme işlemi sırasında bir hata oluştu: " . $conn->error;
    }
}
// Veritabanı bağlantısını kapat
$conn->close();
?>
