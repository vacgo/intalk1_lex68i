<?php
$servername = "127.0.0.1"; //A HOSTNEVET CSERÉLNI KELL A DOCKER ÁLTAL KIAJÁNLOTT IP-RE
$username = "root";
$password = "";
$dbname = "data";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>