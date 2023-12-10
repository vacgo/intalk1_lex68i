<?php
session_start();
require "db_param.php";
include ('db_param.php');

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_presents"])){
        $present_name = $_POST["present_name"];
        $present_price = $_POST["present_price"];
        $purchase_date = $_POST["purchase_date"];



        $query_insert = "INSERT INTO data (present_name, present_price, purchase_date, added_datetime) VALUES (?, ?, ?, NOW())";
        $adding = $conn->prepare($query_insert);
        $adding->bind_param("sds", $present_name, $present_price, $purchase_date);

        if ($adding->execute()) {
        $success_message = "Az ajándékot sikeresen hozzáadtuk a listához.";
        } else {
        $error_message = "Hiba az ajándék hozzáadásánál: " . $adding->error;
        }
        $adding->close();

        $query_lista = "SELECT * FROM data ORDER BY added_datetime DESC";
        $result = $conn->query($query_lista);

        if ($result->num_rows > 0) {
            $presents = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $no_presents_message = "Nem található ajándék.";
        }


    }
    

    if (isset($_POST["view_presents"])) {
        $query_lista = "SELECT * FROM data ORDER BY added_datetime DESC";
        $result = $conn->query($query_lista);

        if ($result->num_rows > 0) {
            $presents = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $no_presents_message = "Nem található ajándék.";
        }
    }

    if (isset($_POST["modify_present"])) {
        $present_id_to_modify = $_POST["modify_present"];

        $query_modify = "SELECT * FROM data WHERE id = ?";
        $modifying = $conn->prepare($query_modify);
        $modifying->bind_param("i", $present_id_to_modify);
        $modifying->execute();
        $result = $modifying->get_result();

        if ($result->num_rows > 0) {
            $present_to_modify = $result->fetch_assoc();
        } else {
            $modify_error_message = "Az ajándék nem található a listában.";
        }

        $modifying->close();
    }

    if (isset($_POST["save_modified_present"])) {
        $modified_present_name = $_POST["modified_present_name"];
        $modified_present_price = $_POST["modified_present_price"];
        $modified_purchase_date = $_POST["modified_purchase_date"];
        
        $present_id_to_modify = $_POST["present_id_to_modify"];

        
        $query_update = "UPDATE data SET present_name = ?, present_price = ?, purchase_date = ? WHERE id = ?";
        $updating = $conn->prepare($query_update);
        $updating->bind_param("sdsi", $modified_present_name, $modified_present_price, $modified_purchase_date, $present_id_to_modify);

        if ($updating->execute()) {
            $success_message = "Az ajándék módosítása sikerült.";
            unset($present_to_modify);
        } else {
            $modify_error_message = "Az ajándék módosítása nem sikerült: " . $updating->error;
        }

        $updating->close();
    }

    if (isset($_POST["logout"])) {
        session_unset();
        session_destroy();
        header("Location: index.php"); 

    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="stilus.css">
    <title>Ajándéklista</title>
</head>
<body>
    <h2>Üdvözöllek, <?php echo $_SESSION["username"]; ?>!</h2>
    <p>Az ajándéklista menedzselése lentebb:</p>

    <form method="post" action="">
        <label for="present_name">Megnevezés:</label>
        <input type="text" name="present_name"><br>

        <label for="present_price">Ár (HUF):</label>
        <input type="number" name="present_price" step="0.01"><br>

        <label for="purchase_date">Vásárlás dátuma:</label>
        <input type="date" name="purchase_date"><br>

        <button type="submit" name="add_presents">Ajándék hozzáadása</button>
        <button type="submit" name="view_presents">Ajándéklista megtekintése</button>
        <br \>
        <button type="submit" name="logout">Kijelentkezés</button>
    </form>
    
   
    <?php if (isset($success_message)) { ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php } ?>

    <?php if (isset($error_message)) { ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php } ?>

    <?php if (isset($presents)) { ?>
        <h3>Ajándéklista:</h3>
        
        <table border="1">
            <thead>
                <tr>
                    <th>Azonosító</th>
                    <th>Megnevezés</th>
                    <th>Ár (HUF)</th>
                    <th>Vásárlás dátuma</th>
                    <th>Hozzáadva a listához</th>
                    <th>Ajándék módosítása</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($presents as $present) { ?>
                    <tr>
                        <td><?php echo $present['id']; ?></td>
                        <td><?php echo $present['present_name']; ?></td>
                        <td><?php echo $present['present_price']; ?></td>
                        <td><?php echo $present['purchase_date']; ?></td>
                        <td><?php echo $present['added_datetime']; ?></td>
                        <form method="post" action="">
                            <td><button type="submit" name="modify_present" value="<?php echo $present['id']; ?>">Módosítás</button></td>
                        </form>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
       
    <?php } ?>
    

    <?php if (isset($no_presents_message)) { ?>
        <p><?php echo $no_presents_message; ?></p>
    <?php } ?>

    <?php if (isset($modify_error_message)) { ?>
        <p style="color: red;"><?php echo $modify_error_message; ?></p>
    <?php } ?>

    <?php if (isset($present_to_modify)) { ?>
        <h3>Ajándék módosítása</h3>
        <form method="post" action="">
            <input type="hidden" name="present_id_to_modify" value="<?php echo $present_to_modify['id']; ?>">
            <label for="modified_present_name">Módosított megnevezés:</label>
            <input type="text" name="modified_present_name" value="<?php echo $present_to_modify['present_name']; ?>" required><br>

            <label for="modified_present_price">Módosított ár:</label>
            <input type="number" name="modified_present_price" step="0.01" value="<?php echo $present_to_modify['present_price']; ?>" required><br>

            <label for="modified_purchase_date">Módosított vásárlási dátum:</label>
            <input type="date" name="modified_purchase_date" value="<?php echo $present_to_modify['purchase_date']; ?>" required><br>

           



            <button type="submit" name="save_modified_present">Módosítás mentése</button>
        </form>
    <?php } ?>

    
    
</body>
</html>