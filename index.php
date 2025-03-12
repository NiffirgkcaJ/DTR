<?php
    session_start();
    require 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $query = "SELECT id, password_hash FROM admin WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: index.php"); // Redirect to the main page
            exit();
        } else {
            header("Location: index.php?error=Invalid username or password");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.8">
        <!-- css links -->
        <link href="css/bootstrap-5.3.3.css" rel="stylesheet">
        <link href="css/global.css" rel="stylesheet">
        <!-- js links -->
        <script src="js/bootstrap-5.3.3.js"></script>
        <script src="js/script.js"></script>
        <title>SEL Daily Time Record</title>
        <!-- styles -->
        <style>
            @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 600;
            font-display: swap;
            src: url('fonts/Poppins-SemiBold.ttf') format('truetype');
            }

            body {
                font-family: 'Poppins', sans-serif !important;
            }

            #datetime-placeholder {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 5px;
            }

            .bar {
                width: 300px;
                background-color: #ccc;
                border-radius: 5px;
                animation: pulse 1.5s infinite ease-in-out;
            }

            .middle {
                width: 400px;
            }

            @keyframes pulse {
                0% {
                    opacity: 0.6;
                }
                50% {
                    opacity: 1;
                }
                100% {
                    opacity: 0.6;
                }
            }
        </style>
    </head>
    <body>
        <div class="container text-center my-5">
            <h1 class="display-3 fw-bold">SEL Daily Time Record</h1>
            <div id="datetime-placeholder">
                <div class="bar"></div>
                <div class="bar middle"></div>
                <div class="bar"></div>
            </div>
            <div id="datetime" class="display-4"></div>
            <div class="row py-5">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Time Record</h5>
                            <p class="card-text">Enter your ID number to record time-in or time-out.</p>
                            
                            <!-- Form to submit the ID -->
                            <form action="process.php" method="POST" onsubmit="return validateInput()">
                                <label for="userInput">Enter your ID Number:</label>
                                <input type="text" id="userInput" name="user_id" class="form-control" placeholder="Your ID Number" required>
                                <button type="submit" name="record" class="btn btn-primary m-2">Record</button>
                            </form>

                            <!-- Display message -->
                            <?php
                                if (isset($_GET['message'])) {
                                    echo "<p class='text-success'>" . htmlspecialchars($_GET['message']) . "</p>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
            <?php if (isset($_GET['error'])) echo "<p style='color: red;'>" . htmlspecialchars($_GET['error']) . "</p>"; ?>

            <!-- Login Button (Icon) -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                    🔑 <!-- You can replace this with an actual icon -->
            </button>

            <!-- Login Modal -->
            <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="loginModalLabel">Admin Login</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="admin_login.php" method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" id="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-success">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function validateInput() {
                let userInput = document.getElementById("userInput").value.trim();
                if (userInput === "") {
                    alert("Please enter a valid ID number.");
                    return false;
                }
                return true;
            }
        </script>
    </body>
</html>