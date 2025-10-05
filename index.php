<?php
include 'config.php';

$category = $_GET['category'] ?? '';
$life_expectancy = $_GET['life_expectancy'] ?? '';

$sql = "SELECT * FROM animalinfo WHERE 1";
if ($category) $sql .= " AND category='$category'";
if ($life_expectancy) $sql .= " AND life_expectancy='$life_expectancy'";
$sql .= " ORDER BY DATE(created_at) DESC, name ASC";

$result = $conn->query($sql);

$file = 'visitors.json';

// If file exists, read it and count the visitors
if (file_exists($file)) {
    $visitors = json_decode(file_get_contents($file), true);
    $total_unique = count($visitors);
} else {
    $total_unique = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Animal List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        h1 {
            margin-bottom: 20px;
        }
        .filter-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .filter-section form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        select, button, a {
            padding: 6px 10px;
            font-size: 14px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #555;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background: #222;
            color: white;
        }
        img {
            width: 100px;
        }
        .btn {
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .btn-edit {
            background-color: #007bff;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-add {
            background-color: #198754;
        }
        .btn-clear {
            background-color: #6c757d;
        }
    </style>
</head>
<body>

<h1>Animal List</h1>

<div class="filter-section">
    <a href="submission.php" class="btn btn-add">+ Add Animal</a>
    <p>Unique Visitors: <?= $total_unique ?></p>

    <form method="get">
        <label>Category:</label>
        <select name="category" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="herbivores" <?= $category=='herbivores'?'selected':'' ?>>Herbivores</option>
            <option value="omnivores" <?= $category=='omnivores'?'selected':'' ?>>Omnivores</option>
            <option value="carnivores" <?= $category=='carnivores'?'selected':'' ?>>Carnivores</option>
        </select>

        <label>Life Expectancy:</label>
        <select name="life_expectancy" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="0-1 year" <?= $life_expectancy=='0-1 year'?'selected':'' ?>>0-1 year</option>
            <option value="1-5 years" <?= $life_expectancy=='1-5 years'?'selected':'' ?>>1-5 years</option>
            <option value="5-10 years" <?= $life_expectancy=='5-10 years'?'selected':'' ?>>5-10 years</option>
            <option value="10+ years" <?= $life_expectancy=='10+ years'?'selected':'' ?>>10+ years</option>
        </select>

        <?php if ($category || $life_expectancy): ?>
            <a href="index.php" class="btn btn-clear">Clear</a>
        <?php endif; ?>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Description</th>
            <th>Life Expectancy</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): $i=1; while($row=$result->fetch_assoc()): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['life_expectancy']) ?></td>
                <td><img src="<?= htmlspecialchars($row['image']) ?>" alt="Animal Image"></td>
                <td>
                    <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('are you sure to delete this record?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="7" style="text-align:center;">No records found</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
