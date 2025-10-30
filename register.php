<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Lalusin Information Technology Colleges</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Registration Form</h3>
                        <p><?php echo ucfirst($_GET['type']); ?> Registration</p>
                    </div>
                    <div class="card-body">
                        <form action="register_process.php" method="POST">
                            <input type="hidden" name="user_type" value="<?php echo $_GET['type']; ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <?php if($_GET['type'] === 'student'): ?>
                            <div class="mb-3">
                                <label for="course" class="form-label">Course</label>
                                <select class="form-control" id="course" name="course" required>
                                    <option value="">Select Course</option>
                                    <option value="BSIT">Bachelor of Science in Information Technology</option>
                                    <option value="BSCS">Bachelor of Science in Computer Science</option>
                                    <option value="BSIS">Bachelor of Science in Information Systems</option>
                                </select>
                            </div>
                            <?php endif; ?>

                            <?php if($_GET['type'] === 'faculty'): ?>
                            <div class="mb-3">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-control" id="department" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="IT">Information Technology</option>
                                    <option value="CS">Computer Science</option>
                                    <option value="IS">Information Systems</option>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="index.html">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>