<?php
include 'config.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // ======= Server-side validation =======
    if(empty($name) || empty($email) || empty($phone)){
        die("<p style='color:red;text-align:center;'>All fields are required!</p>");
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        die("<p style='color:red;text-align:center;'>Invalid email format!</p>");
    }

    if(!is_numeric($phone) || strlen($phone) != 10){
        die("<p style='color:red;text-align:center;'>Phone must be 10 digits!</p>");
    }

    // ======= Optional: Check for duplicate email =======
    $sql_check = "SELECT * FROM appointments WHERE email='$email'";
    $result = $conn->query($sql_check);
    if($result->num_rows > 0){
        die("<p style='color:red;text-align:center;'>Email already exists!</p>");
    }

    // ======= Insert data into database =======
    $sql = "INSERT INTO appointments (name, email, phone) VALUES ('$name','$email','$phone')";
    if ($conn->query($sql) === TRUE) {
        $message = "<p style='color:green;text-align:center;'>Entry added successfully!</p>";
    } else {
        $message = "<p style='color:red;text-align:center;'>Error: " . $conn->error . "</p>";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Form - Doctor Appointment App</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
body { background: linear-gradient(to right, #e0f7fa, #e1f5fe); min-height:100vh; display:flex; flex-direction:column;}
.form-container { background-color:white; padding:40px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.2); margin-top:50px;}
footer { margin-top:auto; background-color:#0288d1; color:white; padding:15px 0;}
.btn-success:hover { background-color:#00695c;}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
<div class="container-fluid">
<a class="navbar-brand" href="index.html">Doctor Appointment App</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav ms-auto">
<li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
<li class="nav-item"><a class="nav-link active" href="form.php">Form</a></li>
<li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
<li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
</ul>
</div>
</div>
</nav>

<!-- Form Section -->
<div class="container form-container col-md-6">
<h2 class="mb-4 text-center">Book an Appointment</h2>

<!-- Display message -->
<?php echo $message; ?>

<form method="POST" action="form.php" onsubmit="return validateForm()">
<div class="mb-3">
<label class="form-label">Name</label>
<input type="text" name="name" id="name" class="form-control" placeholder="Enter Name"/>
</div>
<div class="mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" id="email" class="form-control" placeholder="Enter Email"/>
</div>
<div class="mb-3">
<label class="form-label">Phone</label>
<input type="text" name="phone" id="phone" class="form-control" placeholder="Enter Phone"/>
</div>
<div class="d-flex justify-content-between">
<button type="submit" class="btn btn-success">Submit</button>
<button type="reset" class="btn btn-secondary">Clear Form</button>
</div>
</form>
</div>

<!-- Footer -->
<footer class="text-center">Developed by INFAS | 2025</footer>

<script>
function validateForm() {
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let phone = document.getElementById("phone").value.trim();

    if (name === "" || email === "" || phone === "") {
        alert("All fields are required!");
        return false;
    }
    if (!email.includes("@")) {
        alert("Invalid email!");
        return false;
    }
    if (phone.length !== 10 || isNaN(phone)) {
        alert("Phone must be 10 digits!");
        return false;
    }
    return true;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
