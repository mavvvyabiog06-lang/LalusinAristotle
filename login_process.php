<?php
session_start();
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // support both Supabase mode and local MySQL mode for sanitizing input
    if (isset($USE_SUPABASE) && $USE_SUPABASE) {
        $username = isset($_POST['username']) ? filter_var($_POST['username'], FILTER_SANITIZE_STRING) : '';
    } else {
        $username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : '';
    }
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $user_type = $_POST['user_type'];
    // If using Supabase, authenticate via Supabase Auth and fetch profile from PostgREST
    if (isset($USE_SUPABASE) && $USE_SUPABASE) {
        require_once 'includes/supabase.php';

        // Attempt sign in
        $auth = supabase_auth_signin($username, $password);
        if (isset($auth['status']) && ($auth['status'] === 200 || $auth['status'] === 201)) {
            $body = $auth['body'];
            // Response may contain 'access_token' and 'user'
            $access_token = $body['access_token'] ?? null;
            $user = $body['user'] ?? null;

            if ($user && isset($user['id'])) {
                // Try to fetch profile from students or faculty table using PostgREST
                $table = ($user_type === 'student') ? 'students' : 'faculty';
                // Query by id (Supabase user id should map to profile id if you stored it that way)
                $res = supabase_get($table, '?id=eq.' . $user['id'] . '&select=*');
                if (isset($res['status']) && $res['status'] === 200) {
                    $rows = $res['body'];
                    if (is_array($rows) && count($rows) > 0) {
                        $profile = $rows[0];
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_type'] = $user_type;
                        $_SESSION['name'] = $profile['name'] ?? $user['email'];
                        // Store optional access token for API calls
                        $_SESSION['access_token'] = $access_token;
                        if ($user_type === 'student') header('Location: student/dashboard.php'); else header('Location: faculty/dashboard.php');
                        exit();
                    }
                }
            }
        }
    } else {
        // Fallback: local MySQL authentication (original behavior)
        $table = ($user_type === 'student') ? 'students' : 'faculty';
        
        $sql = "SELECT * FROM {$table} WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $user_type;
                $_SESSION['name'] = $user['name'];
                
                if ($user_type === 'student') {
                    header('Location: student/dashboard.php');
                } else {
                    header('Location: faculty/dashboard.php');
                }
                exit();
            }
        }
    }
    
    $_SESSION['error'] = 'Invalid username or password';
    header('Location: index.html');
    exit();
}
?>