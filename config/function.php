<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'supabase_connect.php';


if (!function_exists('validate')) {
function validate($inputData, $pdo = null) {
    // Optionally log the validation event to the database (if PDO is provided)
    if ($pdo) {
        $logData = htmlspecialchars(trim($inputData), ENT_QUOTES, 'UTF-8');
        $logTime = date('Y-m-d H:i:s');
        
        // Log the validation event if required (for example, storing in a log table)
        $query = "INSERT INTO validation_logs (data, log_time) VALUES (:data, :log_time)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':data', $logData, PDO::PARAM_STR);
        $stmt->bindParam(':log_time', $logTime, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Perform the validation (sanitize input)
    $validatedData = htmlspecialchars(trim($inputData), ENT_QUOTES, 'UTF-8');
    return $validatedData;
}
}


if (!function_exists('logoutSession')) {
function logoutSession($pdo = null) {
    // Optionally log the logout event to the database (if PDO is provided)
    if ($pdo) {
        $userId = isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser'] : null;
        $logoutTime = date('Y-m-d H:i:s');

        if ($userId) {
            $query = "INSERT INTO user_activity_logs (user_id, activity, activity_time) VALUES (:user_id, :activity, :activity_time)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':activity', $activity = 'Logout', PDO::PARAM_STR);
            $stmt->bindParam(':activity_time', $logoutTime, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    // Unset session variables to log out the user
    unset($_SESSION['auth']);
    unset($_SESSION['loggedInUserRole']);
    unset($_SESSION['loggedInUser']);
}
}


if (!function_exists('redirect')) {
function redirect($url, $status, $pdo = null) {
    // Store the status message in the session
    $_SESSION['status'] = htmlspecialchars($status, ENT_QUOTES, 'UTF-8');

    // Optionally log the redirect to a database (if PDO is provided)
    if ($pdo) {
        $query = "INSERT INTO redirect_logs (url, status_message) VALUES (:url, :status_message)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':url', $url, PDO::PARAM_STR);
        $stmt->bindParam(':status_message', $_SESSION['status'], PDO::PARAM_STR);
        $stmt->execute();
    }

    // Redirect the user to the specified URL
    header('Location: ' . $url);
    exit(0);
}
}


if (!function_exists('getCount')) {
function getCount($tableName) {
    global $pdo;
    $table = validate($tableName);
    try {
        $query = "SELECT COUNT(*) AS total FROM $table";
        $stmt = $pdo->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        return 0; // Return 0 if there's an error
    }
}
}

if (!function_exists('alertMessage')) {
function alertMessage() {
    if (isset($_SESSION['status'])) {
        echo '<div class="alert alert-success"><h6>' . htmlspecialchars($_SESSION['status'], ENT_QUOTES, 'UTF-8') . '</h6></div>';
        unset($_SESSION['status']);
    }
}
}


if (!function_exists('checkParamId')) {
function checkParamId($paramType, $pdo) {
    if (isset($_GET[$paramType])) {
        $paramValue = validate($_GET[$paramType]); // Validate the parameter value

        // Check if the parameter value is numeric
        if (!is_numeric($paramValue)) {
            return 'Invalid ID format';
        }

        // Prepare the query based on the table you want to check
        $tables = ['carousel', 'documents', 'services', 'users', 'news']; // List of tables to check
        $found = false;

        foreach ($tables as $table) {
            $query = "SELECT COUNT(*) FROM $table WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $paramValue, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $found = true;
                break; // Exit the loop if the ID is found in any table
            }
        }

        if ($found) {
            return $paramValue; // Return the valid ID
        } else {
            return 'No ID found';
        }
    } else {
        return 'No ID given';
    }
}
}




if (!function_exists('getAll')) {
function getAll($tableName) {
    global $pdo;
    $table = validate($tableName);
    try {
        $query = "SELECT * FROM $table";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
}

if (!function_exists('getById')) {
function getById($tableName, $id) {
    global $pdo;
    $table = validate($tableName);
    $id = validate($id);

    try {
        $query = "SELECT * FROM $table WHERE id = :id LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'status' => 200,
                'message' => 'Fetched data',
                'data' => $row,
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'No Data Record Found',
            ];
        }
    } catch (PDOException $e) {
        return [
            'status' => 500,
            'message' => 'Something went wrong',
        ];
    }
}
}

if (!function_exists('deleteQuery')) {
function deleteQuery($tableName, $id) {
    global $pdo;

    // Validate the table name and id to avoid potential issues
    $table = validate($tableName);
    $id = validate($id);

    $allowedTables = ['users', 'carousel', 'documents', 'news', 'services', 'social_medias']; // Add more valid table names as needed
    if (!in_array($table, $allowedTables)) {
        error_log("Invalid table name: $table");
        return false; // Invalid table name
    }

    try {
        // Prepare the DELETE query
        $query = "DELETE FROM $table WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();

        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            return true; // Successfully deleted
        } else {
            error_log("No rows deleted for id: $id in table: $table");
            return false; // No rows deleted (user might not exist)
        }
    } catch (PDOException $e) {
        // Log the error message for debugging
        error_log("Error deleting from $table: " . $e->getMessage());
        return false; // Return false if there's an error
    }
}
}

if (!function_exists('redirect1')) {
function redirect1($url, $message = '') {
    if (!empty($message)) {
        echo "<script>alert('$message');</script>";
    }
    echo "<script>window.location.href='$url';</script>";
    exit; // Always call exit after a redirect to stop further script execution
}
}



?>
