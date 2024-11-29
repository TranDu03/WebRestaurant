<?php

include '../components/connect.php';// dòng này được kết nối với nội dung tệp connect.php vào trong admin_login.php

session_start();// khởi động phiên làm việc ứng dụng
// kiểm tra xem người dùng đã nhấn vào nút submit hay chưa
if(isset($_POST['submit'])){// thực thi người dùng nhấn vào nút submit 

   $name = $_POST['name']; // lấy giá trị từ trường của tên 
   $name = filter_var($name, FILTER_SANITIZE_STRING);// trong biến $name lọc dữ liệu của hàm filter_var với tham số filter_sanitize_string để loại bỏ các ký tự đặc biệt
   $pass = sha1($_POST['pass']);// trong biến $pass mã hóa mật khẩu người đã được nhập bởi người dùng bằng hàm sha1
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
// Đoạn mã này truy vấn và lấy thông tin từ bảng "admin"  từ cơ sở dữ liệu dựa trên điều kiện tên và mật khẩu cung cấp
   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);// và thực thi truy vấn câu lệnh đã chuẩn bị 
   //Đoạn code này thực hiện việc xác thực đăng nhập. Nếu tên đăng nhập và mật khẩu chính xác,  chuyển hướng người dùng đến trang dashboard.php.
   // ngược lại Nếu tên đăng nhập hoặc mật khẩu không chính xác, đoạn code sẽ hiển thị nội dung thông báo(sai tên đăng nhập hoặc mật khẩu).
   if($select_admin->rowCount() > 0){ //Nếu rowCount() lớn hơn 0, điều đó có nghĩa là có ít nhất một người dùng được tìm thấy
      $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
      $_SESSION['admin_id'] = $fetch_admin_id['id'];
      header('location:dashboard.php');
   }else{
      $message[] = 'Sai tên đăng nhập hoặc mật khẩu!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đăng Nhập</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- admin login form section starts
tạo biểu mẫu đăng nhập   -->

<section class="form-container">

   <form action="" method="POST">
      <h3>Đăng Nhập</h3>
      <p>Tài khoản mặc định = <span>admin</span> & mật khẩu = <span>111</span></p>
      <input type="text" name="name" maxlength="20" required placeholder="Tên đăng nhập" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" maxlength="20" required placeholder="Mật khẩu" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Đăng Nhập" name="submit" class="btn">
   </form>

</section>

<!-- admin login form section ends -->











</body>
</html>