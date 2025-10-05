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
        echo "<p class='error-msg'>Incorrect Captcha. Please try again!</p>";
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
            echo "<p class='error-msg'>Error: " . $stmt->error . "</p>";
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            padding: 30px;
        }
        h2 {
            text-align: center;
            background-color: #88b5daff;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 15px;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }
        input[type="radio"] {
            margin-right: 5px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .captcha-box {
            width: 100px;
            text-align: center;
            background-color: #e0e0e0;
            padding: 5px;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        img {
            margin-top: 10px;
            border-radius: 5px;
        }
        .error-msg {
            color: red;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Animal</h2>
        <form method="post" enctype="multipart/form-data">
            <p>
                <label for="name">Animal Name:</label><br>
                <input type="text" name="name" id="name" value="<?php echo $row['name']; ?>" required>
            </p>
            <p>
                <label>Category:</label><br>
                <input type="radio" name="category" value="herbivores" <?php if ($row['category'] == 'herbivores') echo 'checked'; ?>> Herbivores<br>
                <input type="radio" name="category" value="omnivores" <?php if ($row['category'] == 'omnivores') echo 'checked'; ?>> Omnivores<br>
                <input type="radio" name="category" value="carnivores" <?php if ($row['category'] == 'carnivores') echo 'checked'; ?>> Carnivores
            </p>
            <p>
                <label for="image">Upload Image:</label><br>
                <input type="file" name="image" id="image" accept="image/*"><br>
                <?php if (!empty($row['image'])): ?>
                    <img src="<?php echo $row['image']; ?>" width="100">
                <?php else: ?>
                    <p>No image uploaded</p>
                <?php endif; ?>
            </p>
            <p>
                <label for="description">Description:</label><br>
                <textarea name="description" id="description" rows="4"><?php echo $row['description']; ?></textarea>
            </p>
            <p>
                <label for="life_expectancy">Life Expectancy:</label><br>
                <select name="life_expectancy" id="life_expectancy" required>
                    <option value="0-1 year" <?php if ($row['life_expectancy'] == '0-1 year') echo 'selected'; ?>>0-1 year</option>
                    <option value="1-5 years" <?php if ($row['life_expectancy'] == '1-5 years') echo 'selected'; ?>>1-5 years</option>
                    <option value="5-10 years" <?php if ($row['life_expectancy'] == '5-10 years') echo 'selected'; ?>>5-10 years</option>
                    <option value="10+ years" <?php if ($row['life_expectancy'] == '10+ years') echo 'selected'; ?>>10+ years</option>
                </select>
            </p>
            <p>
                <label for="captcha">Captcha:</label><br>
                <div class="captcha-box"><?php echo $_SESSION['captcha']; ?></div>
                <input type="number" name="captcha" placeholder="Enter the above number" required>
            </p>
            <p>
                <input type="submit" name="submit" value="Update">
            </p>
        </form>
    </div>
</body>
</html>
