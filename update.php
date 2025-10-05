<?php
include "config.php";
session_start();

if (!isset($_POST['submit'])) {
    $_SESSION['captcha'] = rand(1000, 99999);
}

$id = $_GET['id'];
$sql = "SELECT * from animalinfo where id=?";
$query = $conn->prepare($sql);
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$query->close();

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $life_expectancy = $_POST['life_expectancy'];
    $entered_captcha = $_POST['captcha'];
    $actual_captcha = $_SESSION['captcha'];

    // Validate captcha
    if ($entered_captcha != $actual_captcha) {
        echo "<p style='color:red; text-align:center;'>Incorrect Captcha. Please try again!</p>";
    } else {
        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];
            $tmp_name = $_FILES['image']['tmp_name'];
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $image_path = $upload_dir . basename($image);
            move_uploaded_file($tmp_name, $image_path);
        } else {
            // Keep old image if no new image uploaded
            $image_path = $row['image'];
        }

        // Update data in database
        $update_sql = "UPDATE animalinfo 
                   SET name = ?, category = ?, description = ?, life_expectancy = ?, image = ?, updated_at = NOW() 
                   WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssi", $name, $category, $description, $life_expectancy, $image_path, $id);

        if ($stmt->execute()) {
            header("Location: index.php"); // Redirect after successful update
            exit;
        } else {
            echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
        $conn->close();

    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Edit Animal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow-sm">
                    <h2 class="text-center mb-4 bg-secondary bg-gradient text-white p-2 rounded">Edit Animal</h2>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Animal Name :</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo $row['name']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category :</label><br>
                            <input type="radio" name="category" value="herbivores" <?php if ($row['category'] == 'herbivores') echo 'checked'; ?>> Herbivores<br>
                            <input type="radio" name="category" value="omnivores" <?php if ($row['category'] == 'omnivores') echo 'checked'; ?>> Omnivores<br>
                            <input type="radio" name="category" value="carnivores" <?php if ($row['category'] == 'carnivores') echo 'checked'; ?>> Carnivores
                        </div>

                        <div class="mb-3">
                            <label for="image">Upload Image :</label>
                            <input type="file" name="image" id="image" accept="image/*"><br>
                            <?php if (!empty($row['image'])): ?>
                                <img src="<?php echo $row['image']; ?>" width="100" class="img-thumbnail mt-2">
                            <?php else: ?>
                                <p>No image uploaded</p>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description">Description :</label>
                            <textarea name="description" id="description" rows="4" class="form-control"><?php echo $row['description']; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="life_expectancy">Life Expectancy :</label>
                            <select name="life_expectancy" id="life_expectancy" class="form-select" required>
                                <option value="0-1 year" <?php if ($row['life_expectancy'] == '0-1 year') echo 'selected'; ?>>0-1 year</option>
                                <option value="1-5 years" <?php if ($row['life_expectancy'] == '1-5 years') echo 'selected'; ?>>1-5 years</option>
                                <option value="5-10 years" <?php if ($row['life_expectancy'] == '5-10 years') echo 'selected'; ?>>5-10 years</option>
                                <option value="10+ years" <?php if ($row['life_expectancy'] == '10+ years') echo 'selected'; ?>>10+ years</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="captcha" class="form-label">Captcha :</label><br>
                            <input type="text" class="form-control mb-2" value="<?php echo $_SESSION['captcha']; ?>" readonly style="max-width:120px; text-align:center; background-color:#f0f0f0;">
                            <input type="number" name="captcha" class="form-control" placeholder="Enter the above number" required>
                        </div>
                        <input type="submit" name="submit" class="btn btn-success" value="Update">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>