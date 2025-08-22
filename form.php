<?php
include 'config.php';

$message = '';
$errors = ['name'=>'', 'email'=>'', 'phone'=>''];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // ======= Server-side validation =======
    if(empty($name)) $errors['name'] = "Name is required!";
    elseif(strlen($name) < 3) $errors['name'] = "Name must be at least 3 characters!";

    if(empty($email)) $errors['email'] = "Email is required!";
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format!";

    if(empty($phone)) $errors['phone'] = "Phone is required!";
    elseif(!is_numeric($phone) || strlen($phone) != 10) $errors['phone'] = "Phone must be 10 digits!";

    // Insert if no errors
    if(!array_filter($errors)) {
        $sql = "INSERT INTO entries (name, email, phone) VALUES ('$name','$email','$phone')";
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success text-center'>Entry added successfully!</div>";
            $name = $email = $phone = ''; // clear form
        } else {
            $message = "<div class='alert alert-danger text-center'>Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Book Appointment - Doctor Appointment App</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
body { background: linear-gradient(to right, #e0f7fa, #e1f5fe); min-height:100vh; display:flex; flex-direction:column;}
.form-container { background-color:white; padding:40px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.2); margin-top:50px;}
footer { margin-top:auto; background-color:#0288d1; color:white; padding:15px 0;}
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

<form method="POST" action="" onsubmit="return validateForm()">
<div class="mb-3">
<label class="form-label">Name</label>
<input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name ?? ''); ?>" placeholder="Enter Name">
<div class="text-danger" id="nameError"><?php echo $errors['name']; ?></div>
</div>

<div class="mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="Enter Email">
<div class="text-danger" id="emailError"><?php echo $errors['email']; ?></div>
</div>

<div class="mb-3">
<label class="form-label">Phone</label>
<input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($phone ?? ''); ?>" placeholder="Enter Phone">
<div class="text-danger" id="phoneError"><?php echo $errors['phone']; ?></div>
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
// Real-time validation and input formatting
function validateField(field, errorId, type){
    let value = field.value.trim();
    let message = "";

    if(value === "") message = type + " is required!";
    else if(type === "Name" && value.length < 3) message = "Name must be at least 3 characters!";
    else if(type === "Email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) message = "Invalid email format!";
    else if(type === "Phone" && (isNaN(value) || value.length != 10)) message = "Phone must be 10 digits!";

    document.getElementById(errorId).innerText = message;
    return message === "";
}

document.getElementById("name").addEventListener("input", function(){ 
    this.value = this.value.toUpperCase(); 
    validateField(this,"nameError","Name");
});
document.getElementById("email").addEventListener("input", function(){ validateField(this,"emailError","Email"); });
document.getElementById("phone").addEventListener("input", function(){ 
    this.value = this.value.replace(/[^0-9]/g,''); 
    validateField(this,"phoneError","Phone"); 
});

function validateForm(){
    let valid = true;
    valid &= validateField(document.getElementById("name"),"nameError","Name");
    valid &= validateField(document.getElementById("email"),"emailError","Email");
    valid &= validateField(document.getElementById("phone"),"phoneError","Phone");
    return !!valid;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
