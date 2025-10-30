<?php
session_start();
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_type = $_POST['user_type'];
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $raw_password = isset($_POST['password']) ? $_POST['password'] : '';

    // If using Supabase, sign up via Supabase Auth and create profile row via PostgREST
    if (isset($USE_SUPABASE) && $USE_SUPABASE) {
        require_once 'includes/supabase.php';

        // Signup user in Supabase Auth
        $signup = supabase_auth_signup($email, $raw_password, ['data' => ['name' => $name]]);
        if (isset($signup['status']) && ($signup['status'] === 200 || $signup['status'] === 201)) {
            // Supabase will create a user entry; the response body may include created user details
            $body = $signup['body'];
            $user = $body['user'] ?? null;

            // If Supabase returned a user id, create a profile row in PostgREST (students or faculty)
            if ($user && isset($user['id'])) {
                $profile = [
                    'id' => $user['id'],
                    'name' => $name,
                    'email' => $email,
                ];

                if ($user_type === 'student') {
                    $profile['course'] = $_POST['course'] ?? null;
                    $res = supabase_insert('students', $profile);
                } else {
                    $profile['department'] = $_POST['department'] ?? null;
                    $res = supabase_insert('faculty', $profile);
                }

                if (isset($res['status']) && ($res['status'] === 201 || $res['status'] === 200)) {
                    $_SESSION['success'] = 'Registration successful! Please login.';
                    header('Location: index.html');
                    exit();
                }
            }
        }

        $_SESSION['error'] = 'Registration failed on Supabase. Check configuration and logs.';
        header('Location: register.php?type=' . $user_type);
        exit();
    }

    // Fallback local MySQL behavior
    $name = $conn->real_escape_string($name);
    $email = $conn->real_escape_string($email);
    $password = password_hash($raw_password, PASSWORD_DEFAULT);

    if ($user_type === 'student') {
        $course = $conn->real_escape_string($_POST['course']);
        $sql = "INSERT INTO students (name, email, password, course) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $course);
    } else {
        $department = $conn->real_escape_string($_POST['department']);
        $sql = "INSERT INTO faculty (name, email, password, department) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $department);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Registration successful! Please login.';
        header('Location: index.html');
        exit();
    } else {
        $_SESSION['error'] = 'Registration failed. Please try again.';
        header('Location: register.php?type=' . $user_type);
        exit();
    }
}
?>