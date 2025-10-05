<?php
include 'config.php';
session_start();

// Generate a random captcha number only when form loads (not on submit)
if (!isset($_POST['submit'])) {
    $_SESSION['captcha'] = rand(1000, 99999);
}


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
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $upload_dir = "uploads/";

        if (!empty($image)) {
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $image_path = $upload_dir . basename($image);
            move_uploaded_file($tmp_name, $image_path);
        } else {
            $image_path = NULL;
        }

        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO animalinfo (name, category, description, life_expectancy, image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("sssss", $name, $category, $description, $life_expectancy, $image_path);

        if ($stmt->execute()) {
            header("Location: index.php");
            echo "<p style='color:green;'>Animal information saved successfully!</p>";
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
    <title>Animal List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow-sm">
                    <h2 class="text-center mb-4 bg-secondary bg-gradient text-white p-2 rounded">Animal Submission Form</h2>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Animal Name :</label>
                            <input type="text" name="name" id="name" class="form-control" required />
                        </div>
                        <div class="mb-3" class="form-check">
                            <label class="form-check-label">Category :</label><br>
                            <input type="radio" name="category" value="herbivores" class="form-check-input" required> Herbivores<br>
                            <input type="radio" name="category" value="omnivores" class="form-check-input"> Omnivores<br>
                            <input type="radio" name="category" value="carnivores" class="form-check-input"> Carnivores
                        </div>
                        <div class="mb-3">
                            <label for="image">Upload Image :</label><br>
                            <input type="file" name="image" id="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="description">Description :</label><br>
                            <textarea name="description" id="description" rows="4" cols="50"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="life_expectancy">Life Expectancy :</label><br>
                            <select name="life_expectancy" id="life_expectancy" required>
                                <option value="0-1 year">0-1 year</option>
                                <option value="1-5 years">1-5 years</option>
                                <option value="5-10 years">5-10 years</option>
                                <option value="10+ years">10+ years</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="captcha" class="form-label">Captcha :</label><br>
                            <input type="text" class="form-control mb-2" value="<?php echo $_SESSION['captcha']; ?>" readonly style="max-width:120px; text-align:center; background-color:#f0f0f0;">
                            <input type="number" name="captcha" class="form-control" placeholder="Enter the above number" required>
                        </div>
                        <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>