<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'barket_db');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $student_id = $_POST['student_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/@students\.nu-dasma\.edu\.ph$/', $email)) {
        $error = "Please use a valid school email (e.g., @students.nu-dasma.edu.ph).";
    } else {
        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT email FROM sellers WHERE email = ?");
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO sellers (first_name, last_name, student_id, email, password, registration_date, status) VALUES (?, ?, ?, ?, ?, NOW(), 'active')");
            $stmt->bind_param('sssss', $first_name, $last_name, $student_id, $email, $hashed_password);

            if ($stmt->execute()) {
                echo "Registration successful!";
                $_SESSION['seller_email'] = $email; // Log in the seller
                header("Location: seller-login.php");
                exit();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url(userimg/NU-Dasma.png);
            background-position: center;
            background-size: cover;
            overflow: hidden;
        }

        .glass-container {
            position: relative;
            width: 470px;
            padding: 25px;
            border-radius: 20px;
            backdrop-filter: blur(6px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.4);
            z-index: 10;
            overflow: hidden;
        }

        .glass-container h2 {
            color: #143f77;
            font-size: 28px;
            font-weight: 600;
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            max-width: 120px;
            height: 90px;
            position: center;
        }

        .logo-text {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .logo-text span {
            color: #cfa122;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-size: 37px;
            margin-bottom: 15px;
        }

        .input-group {
            position: relative;
            margin-bottom: 30px;
        }

        .password-group {
            position: relative;
            margin-bottom: 30px;
        }

        .input-group input {
            width: 100%;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            outline: none;
            border-radius: 35px;
            font-size: 16px;
            color: #000000;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .password-group input {
            width: 100%;
            padding: 15px 50px 15px 20px; /* Space for the eye icon */
            background: rgba(255, 255, 255, 0.2);
            border: none;
            outline: none;
            border-radius: 35px;
            font-size: 16px;
            color: #000000;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .input-group input::placeholder,
        .password-group input::placeholder {
            color: rgba(0, 0, 0, 0.7);
        }

        .input-group input:focus,
        .password-group input:focus {
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .input-group label,
        .password-group label {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            color: rgba(4, 4, 4, 0.8);
            font-size: 16px;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus + label,
        .input-group input:valid + label,
        .password-group input:focus + label,
        .password-group input:valid + label {
            top: 0;
            left: 15px;
            font-size: 12px;
            color: #143f77;
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 10px;
            color: #000000;
        }

        .eye-icon {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            font-size: 20px;
            cursor: pointer;
            color: #555;
        }

        .register-btn {
            width: 100%;
            padding: 15px;
            background: #143f77;
            border: none;
            outline: none;
            border-radius: 35px;
            color: #f4e088;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .register-btn:hover {
            background: #0f2a5b;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: rgba(0, 0, 0, 0.8);
            font-size: 14px;
        }

        .login-link a {
            color: #000000;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="glass-container">
        <div class="logo">
            <img src="userimg/Project Logo.png" alt="NU Barket Logo">
            <div class="logo-text">
                <span>NU Barket</span>
            </div>
        </div>
        <h2>Register</h2>
        <?php if ($error): ?>
            <div style="color: red; text-align: center; margin-bottom: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="input-group">
                <input type="text" name="first_name" placeholder=" " required>
                <label>First Name</label>
            </div>
            <div class="input-group">
                <input type="text" name="last_name" placeholder=" " required>
                <label>Last Name</label>
            </div>
            <div class="input-group">
                <input type="text" name="student_id" required>
                <label>Student Number</label>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder=" " required>
                <label>School Email</label>
            </div>
            <div class="password-group">
                <input type="password" name="password" placeholder=" " required>
                <label>Password</label>
                <i class="bx bx-show eye-icon" onclick="togglePassword('password')"></i>
            </div>
            <div class="password-group">
                <input type="password" name="confirm_password" placeholder=" " required>
                <label>Confirm Password</label>
                <i class="bx bx-show eye-icon" onclick="togglePassword('confirm_password')"></i>
            </div>
            <button type="submit" class="register-btn">Register</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="login.html">Login</a>
        </div>
    </div>

    <script>
        function togglePassword(fieldName) {
            const passwordField = document.querySelector(`input[name="${fieldName}"]`);
            const eyeIcon = passwordField.nextElementSibling; // Directly next sibling
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove('bx-show');
                eyeIcon.classList.add('bx-eye-closed');
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove('bx-eye-closed');
                eyeIcon.classList.add('bx-show');
            }
        }
    </script>
</body>
</html>
