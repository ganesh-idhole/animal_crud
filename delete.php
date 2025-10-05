<?php
include "config.php";
if(isset($_GET['id'])){
    $id = $_GET['id'];

    $query = $conn->prepare("DELETE FROM animalinfo WHERE id = ?");
    $query->bind_param("i", $id);

    if ($query->execute()) {
        header("Location: index.php");    // Redirect back to the list page after successful deletion
        exit;
    } else {
        echo "<p style='color:red;'>Error: " . $query->error . "</p>";
    }

    $query->close();
    $conn->close();
}else{
    echo "<p style='color:red;'>Invalid request.</p>";
}
?>