<?php

include '../components/connect.php';// dòng này  được sử dụng để nhập (hoặc kết nối) nội dung của tệp connect.php vào trong tệp register.php.

session_start(); // khởi động phiên làm việc ứng dung 

$admin_id = $_SESSION['admin_id'];//Gán giá trị của biến $_SESSION['admin_id'] cho biến $admin_id.

if(!isset($admin_id)){ //ktr ng dùng đăng nhập hay chưa 
   header('location:admin_login.php');//Nếu người dùng chưa đăng nhập, sử dụng hàm header() để chuyển hướng người dùng đến trang admin_login.php.
};

if(isset($_POST['submit'])){// kiểm tra xem người dùng đã nhấn vào nút submit hay chưa trong phiên đăng nhập

   $name = $_POST['name'];// lấy giá trị từ trường của tên name
   $name = filter_var($name, FILTER_SANITIZE_STRING);// lọc dũ diệu và xóa bỏ cái ký tự đặc biệt trong biến name
   $pass = sha1($_POST['pass']);//dòng này mã hóa mật khẩu được đăng nhập bởi người dùng trong biểu mẫu
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);// mã hóa mật khẩu đã xác nhận bởi người dùng trong biểu mẫu
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);
// truy vấn và lưu thông tin của bảng admin từ cơ sở dữ liệu với điều kiện tên name cung cấp
   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
   $select_admin->execute([$name]);// thực thi câu lệnh đã chuẩn bị
   
   if($select_admin->rowCount() > 0){// //đoạn code này kiểm tra xem tên và mật khẩu đã nhập từ biểu mẫu có hợp lệ hay không.
       //Nếu không hợp lệ, thì sẽ hiển thị thông báo lỗi tương ứng
      $message[] = 'Tên đã được sử dụng';
   }else{
      if($pass != $cpass){
         $message[] = 'Mật khẩu không khớp!';
         //đoạn code này thực hiện việc đăng ký tài khoản quản trị mới vào bảng admin. 
         //Nếu thành công, nó sẽ hiển thị thông báo "Đăng kí thành công!"
      }else{
         $insert_admin = $conn->prepare("INSERT INTO `admin`(name, password) VALUES(?,?)");
         $insert_admin->execute([$name, $cpass]);
         $message[] = 'Đăng kí thành công!';
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Tạo tài khoản</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- register admin section starts 
bắt đầu đăng kí quản trị thành viên -->

<section class="form-container">

   <form action="" method="POST">
      <h3>Tạo tài khoản mới</h3>
      <input type="text" name="name" maxlength="20" required placeholder="Tên đăng nhập" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" maxlength="20" required placeholder="Mật khẩu" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" maxlength="20" required placeholder="Xác nhận mật khẩu" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Tạo" name="submit" class="btn">
   </form>

</section>

<!-- register admin section ends -->
















<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>