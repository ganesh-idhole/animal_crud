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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container my-5">

<h1 class="mb-4">Animal List</h1>

<div class="d-flex justify-content-between mb-3">
    <a href="submission.php" class="btn btn-success">+ Add Animal</a>
    <p>Unique Visitors: <?= $total_unique ?></p>
    <form method="get" class="d-flex align-items-center">
        <label class="me-2 fw-bold">Category:</label>
        <select name="category" class="form-select w-auto me-3" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="herbivores" <?= $category=='herbivores'?'selected':'' ?>>Herbivores</option>
            <option value="omnivores" <?= $category=='omnivores'?'selected':'' ?>>Omnivores</option>
            <option value="carnivores" <?= $category=='carnivores'?'selected':'' ?>>Carnivores</option>
        </select>

        <label class="me-2 fw-bold">Life Expectancy:</label>
        <select name="life_expectancy" class="form-select w-auto me-3" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="0-1 year" <?= $life_expectancy=='0-1 year'?'selected':'' ?>>0-1 year</option>
            <option value="1-5 years" <?= $life_expectancy=='1-5 years'?'selected':'' ?>>1-5 years</option>
            <option value="5-10 years" <?= $life_expectancy=='5-10 years'?'selected':'' ?>>5-10 years</option>
            <option value="10+ years" <?= $life_expectancy=='10+ years'?'selected':'' ?>>10+ years</option>
        </select>

        <?php if ($category || $life_expectancy): ?>
            <a href="index.php" class="btn btn-outline-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
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
                <td><img src="<?= htmlspecialchars($row['image']) ?>" width="100" class="img-thumbnail"></td>
                <td>
                    <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> Edit</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?');"><i class="bi bi-trash"></i> Delete</a>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="7" class="text-center">No records found</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
