<?php 
    session_start();

    if (!isset($_SESSION['username'])) {
      $_SESSION['msg'] = "You must log in first";
      header('location: login.php');
      exit();
  }
  
  if (isset($_GET['logout'])) {
      session_destroy();
      unset($_SESSION['username']);
      header('location: login.php');
      exit();
  }
  
$conn = new mysqli('localhost', 'root', '', 'register_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// คำสั่ง SQL
$sql = "CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task VARCHAR(255) NOT NULL,
    due_date DATE,
    user VARCHAR(255) NOT NULL,
    status BOOLEAN DEFAULT 0
)";

 
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task'])) {
      $task = $_POST['task'];
      $due_date = $_POST['due_date'];
      $user = $_SESSION['username'];
      $sql = "INSERT INTO tasks (task, due_date, user) VALUES ('$task', '$due_date', '$user')";
      $conn->query($sql);
  }
  
  if (isset($_GET['delete'])) {
      $id = $_GET['delete'];
      $sql = "DELETE FROM tasks WHERE id=$id";
      $conn->query($sql);
  }
  
  if (isset($_GET['done'])) {
      $id = $_GET['done'];
      $sql = "UPDATE tasks SET status=1 WHERE id=$id";
      $conn->query($sql);
  }
  
  $user = $_SESSION['username'];
  $result = $conn->query("SELECT * FROM tasks WHERE user='$user'");
  ?>
  
  <!DOCTYPE html>
  <html lang="th">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Home Page</title>
      <link rel="stylesheet" href="home.css">
  </head>
  <body>
      <div class="homeheader">
          <h2>TO DO LIST</h2>
      </div>
      <div class="homecontent">
          <?php if (isset($_SESSION['success'])) : ?>
              <div class="success">
                  <h3><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></h3>
              </div>
          <?php endif ?>
  
          <p>ยินดีต้อนรับ <strong><?php echo $_SESSION['username']; ?></strong></p>
          <p><a href="index.php?logout='1'" style="color: red;">Logout</a></p>
  
          <section class="todo">
              <h2>รายการสิ่งที่ต้องทำ</h2>
              <form method="POST">
                  <input type="text" name="task" class="input-field" placeholder="เพิ่มรายการที่ต้องทำ" required>
                  <input type="date" name="due_date" class="date-input" required>
                  <button type="submit" class="btn">เพิ่ม</button>
              </form>
              <ul class="scroll">
                  <?php while ($row = $result->fetch_assoc()): ?>
                      <li>
                          <?php echo $row['task'] . " (" . $row['due_date'] . ")"; ?>
                          <?php if (!$row['status']): ?>
                              <a href="?done=<?php echo $row['id']; ?>">✔</a>
                          <?php endif; ?>
                          <a href="?delete=<?php echo $row['id']; ?>">❌</a>
                      </li>
                  <?php endwhile; ?>
              </ul>
              <hr class="counter" />
          </section>
      </div>
  </body>
  </html>
  <?php $conn->close(); ?>
  