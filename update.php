<?php
include 'config.php';

// Get record to edit
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM entries WHERE id=$id";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
    } else {
        die("Record not found!");
    }
}

// Process form submission
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $errors = [];

    // Server-side validation
    if(empty($name)) $errors['name'] = "Name is required!";
    if(empty($email)) $errors['email'] = "Email is required!";
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format!";
    if(empty($phone)) $errors['phone'] = "Phone is required!";
    elseif(!is_numeric($phone) || strlen($phone) != 10) $errors['phone'] = "Phone must be 10 digits!";

    if(empty($errors)){
        $sql = "UPDATE entries SET name='$name', email='$email', phone='$phone' WHERE id=$id";
        if($conn->query($sql) === TRUE){
            header("Location: view.php");
            exit();
        } else {
            $errors['db'] = "Error updating record: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Appointment - Doctor Appointment App</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(to right, #e0f7fa, #e1f5fe);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.card {
    width: 100%;
    max-width: 500px;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    background-color: white;
}
.input-group-text {
    width: 40px;
    text-align: center;
}
</style>
</head>
<body>

<div class="card">
    <h3 class="text-center mb-4">Edit Appointment</h3>

    <?php if(isset($errors['db'])): ?>
        <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <div class="input-group">
                <input type="text" name="name" id="name" value="<?php echo $row['name']; ?>" class="form-control">
                <span class="input-group-text" id="nameIcon"></span>
            </div>
            <div class="text-danger" id="nameError"><?php echo $errors['name'] ?? ''; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="input-group">
                <input type="email" name="email" id="email" value="<?php echo $row['email']; ?>" class="form-control">
                <span class="input-group-text" id="emailIcon"></span>
            </div>
            <div class="text-danger" id="emailError"><?php echo $errors['email'] ?? ''; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <div class="input-group">
                <input type="text" name="phone" id="phone" value="<?php echo $row['phone']; ?>" class="form-control">
                <span class="input-group-text" id="phoneIcon"></span>
            </div>
            <div class="text-danger" id="phoneError"><?php echo $errors['phone'] ?? ''; ?></div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" name="update" class="btn btn-success">Update</button>
            <a href="view.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
// Live validation function
function validateField(field, iconId, errorId, type){
    const value = field.value.trim();
    let valid = false;
    let message = "";

    if(value === ""){
        message = type + " is required!";
    } else if(type === "Email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)){
        message = "Invalid email format!";
    } else if(type === "Phone" && (isNaN(value) || value.length !== 10)){
        message = "Phone must be 10 digits!";
    } else {
        valid = true;
    }

    document.getElementById(errorId).innerText = message;
    document.getElementById(iconId).innerHTML = valid ? "✅" : (message ? "❌" : "");
}

// Add event listeners
document.getElementById("name").addEventListener("input", function(){
    validateField(this, "nameIcon", "nameError", "Name");
});
document.getElementById("email").addEventListener("input", function(){
    validateField(this, "emailIcon", "emailError", "Email");
});
document.getElementById("phone").addEventListener("input", function(){
    validateField(this, "phoneIcon", "phoneError", "Phone");
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
