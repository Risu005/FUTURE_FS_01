<?php

session_start();


$db_host = 'localhost';
$db_name = 'portfolio_db';
$db_user = 'root';
$db_pass = '';
$db_charset = 'utf8mb4';


$admin_password = '@Logankent001';


if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['login'])) {
        if ($_POST['password'] === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $login_error = 'Invalid password.';
        }
    }
}


if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin-messages.php');
    exit;
}


if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    try {
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
        $dbh = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $stmt = $dbh->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = :id");
        $stmt->execute([':id' => $_GET['mark_read']]);
    } catch (PDOException $e) {
        error_log("Mark read error: " . $e->getMessage());
    }
    header('Location: admin-messages.php');
    exit;
}


if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
        $dbh = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $stmt = $dbh->prepare("DELETE FROM contact_messages WHERE id = :id");
        $stmt->execute([':id' => $_GET['delete']]);
    } catch (PDOException $e) {
        error_log("Delete error: " . $e->getMessage());
    }
    header('Location: admin-messages.php');
    exit;
}


$messages = [];
$total = 0;
$unread = 0;
if (isset($_SESSION['admin_logged_in'])) {
    try {
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
        $dbh = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $stmt = $dbh->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        $messages = $stmt->fetchAll();
        $total = count($messages);
        $unread = count(array_filter($messages, fn($m) => !$m['is_read']));
    } catch (PDOException $e) {
        $db_error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Contact Messages | Derick Mgalawe</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --bg: #0f0f1a;
            --bg-card: #16162a;
            --text: #e2e8f0;
            --text-muted: #94a3b8;
            --border: rgba(99, 102, 241, 0.15);
            --success: #10b981;
            --danger: #ef4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            padding: 2rem;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        .subtitle { color: var(--text-muted); margin-bottom: 2rem; }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }
        .stat-card h3 { font-size: 2rem; color: var(--primary); }
        .stat-card p { color: var(--text-muted); font-size: 0.9rem; }
        .logout-btn {
            float: right;
            background: var(--danger);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        th {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        td { font-size: 0.9rem; color: var(--text-muted); }
        tr:hover td { color: var(--text); }
        .unread { border-left: 3px solid var(--primary); }
        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-new { background: rgba(99, 102, 241, 0.2); color: #818cf8; }
        .badge-read { background: rgba(16, 185, 129, 0.2); color: #4ade80; }
        .actions a {
            color: var(--text-muted);
            margin-right: 0.5rem;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .actions a:hover { color: var(--primary); }
        .actions a.delete:hover { color: var(--danger); }
        .login-box {
            max-width: 400px;
            margin: 10vh auto;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2rem;
        }
        .login-box h2 { text-align: center; margin-bottom: 1.5rem; }
        .login-box input {
            width: 100%;
            padding: 0.875rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            margin-bottom: 1rem;
        }
        .login-box button {
            width: 100%;
            padding: 0.875rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        .error { color: var(--danger); text-align: center; margin-bottom: 1rem; }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
        .db-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .stats { grid-template-columns: 1fr; }
            table { font-size: 0.8rem; }
            th, td { padding: 0.75rem; }
            body { padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!isset($_SESSION['admin_logged_in'])): ?>
        <div class="login-box">
            <h2><i class="fas fa-lock"></i> Admin Login</h2>
            <?php if (isset($login_error)): ?>
                <p class="error"><?php echo htmlspecialchars($login_error); ?></p>
            <?php endif; ?>
            <form method="post">
                <input type="password" name="password" placeholder="Enter admin password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
        <?php else: ?>
        <a href="?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <h1><i class="fas fa-envelope"></i> Contact Messages</h1>
        <p class="subtitle">Messages received through your portfolio contact form</p>

        <div class="stats">
            <div class="stat-card">
                <h3><?php echo $total; ?></h3>
                <p>Total Messages</p>
            </div>
            <div class="stat-card">
                <h3 style="color: var(--primary);"><?php echo $unread; ?></h3>
                <p>Unread</p>
            </div>
            <div class="stat-card">
                <h3 style="color: var(--success);"><?php echo $total - $unread; ?></h3>
                <p>Read</p>
            </div>
        </div>

        <?php if (isset($db_error)): ?>
            <div class="db-error"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($db_error); ?></div>
        <?php endif; ?>

        <?php if (empty($messages)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No messages yet. They will appear here when someone uses your contact form.</p>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                <tr class="<?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                    <td>
                        <?php if (!$msg['is_read']): ?>
                            <span class="badge badge-new">New</span>
                        <?php else: ?>
                            <span class="badge badge-read">Read</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($msg['name']); ?></strong></td>
                    <td><a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" style="color:var(--primary);"><?php echo htmlspecialchars($msg['email']); ?></a></td>
                    <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars(substr($msg['message'], 0, 100))) . (strlen($msg['message']) > 100 ? '...' : ''); ?></td>
                    <td><?php echo date('M j, Y H:i', strtotime($msg['created_at'])); ?></td>
                    <td class="actions">
                        <?php if (!$msg['is_read']): ?>
                            <a href="?mark_read=<?php echo $msg['id']; ?>" title="Mark as read"><i class="fas fa-check"></i></a>
                        <?php endif; ?>
                        <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>" title="Reply"><i class="fas fa-reply"></i></a>
                        <a href="?delete=<?php echo $msg['id']; ?>" class="delete" title="Delete" onclick="return confirm('Delete this message?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
