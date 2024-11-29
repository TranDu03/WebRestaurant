<?php

include 'components/connect.php';
//Dòng mã "include 'components/connect.php';" được sử dụng để kết nối nội dung của tệp tin "connect.php" 
session_start();
// khởi động phiên làm việc của ứng dụng

// kiểm tra mã biến phiên đã thiết lập hay chưa
if(isset($_SESSION['user_id'])){  //Nếu biến phiên $_SESSION['user_id'] đã được thiết lập thì người dùng đã được đăng nhập  
   //thì giá trị của $_SESSION['user_id'] được gán cho biến $user_id.)
   $user_id = $_SESSION['user_id'];
}else{  // ngược lại chưa thiết lập thì người dùng đăng nhập gán giá trị rỗng
   $user_id = '';
};

if(isset($_POST['submit'])){ //Câu lệnh này kiểm tra xem người dùng đã nhấn nút submit hay chưa hay là xem biến $_POST['submit' có tồn tại không
  // lấy và lọc dữ liệu (file_var)
   $name = $_POST['name'];// lấy giá trị của trường có tên "name"  và gán nó cho biến $name
   $name = filter_var($name, FILTER_SANITIZE_STRING); // trong biến name lọc dữ liệu hàm filter_var với tham số filter_sanitize_string để loại bỏ các ký tự đặc biệt
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']); //dòng code sẽ mã hóa mật khẩu được nhập bởi người dùng bằng hàm sha1()
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']); //Dòng này sẽ mã hóa mật khẩu đã xác nhận (confirm password) được nhập bởi người dùng bằng hàm sha1().
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);
 //Đoạn mã này giúp bạn truy vấn và lấy thông tin về người dùng từ cơ sở dữ liệu dựa trên email hoặc số điện thoại mà người dùng cung cấp, 
 //và lưu trữ thông tin người dùng vào biến $row để sử dụng trong các xử lý tiếp theo.
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
   $select_user->execute([$email, $number]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){ // nếu truy vấn dữ liệu người dùng với số hàng dữ liệu trả về có giá trị > 0 cs nghĩa là truy vấn tìm thấy ít nhất một hàng dữ liệu
      $message[] = 'email or number already exists!';//thì thông báo lỗi trong mảng $message sẽ hiển thị nội dung là email hoặc số điện thoại đã tồn tại.
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';// ngược lại  Nếu mật khẩu không khớp,
         // thì thông báo lỗi trong mảng sẽ được hiển thị để người dùng biết và nhập lại mật khẩu ( xác nhận mật khẩu không khớp)
      }else{// Đoạn mã này thực thi câu lệnh sql để thêm người dùng vào bảng user và 
         //để cung cấp thông tin name, email number password từ bảng dữ liệu người dùng
         //Phương thức execute() thực thi câu lệnh và thêm dữ liệu người dùng mới vào bảng.
         $insert_user = $conn->prepare("INSERT INTO `users`(name, email, number, password) VALUES(?,?,?,?)");
         $insert_user->execute([$name, $email, $number, $cpass]);
         $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
         $select_user->execute([$email, $pass]);
         $row = $select_user->fetch(PDO::FETCH_ASSOC);
         if($select_user->rowCount() > 0){
            $_SESSION['user_id'] = $row['id'];
            header('location:home.php');
         }
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
   <title>Đăng kí</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">

   <form action="" method="post">
      <h3>Đăng kí</h3>
      <input type="text" name="name" required placeholder="Tên của bạn" class="box" maxlength="50">
      <input type="email" name="email" required placeholder="Email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" required placeholder="Số điện thoại" class="box" min="0" max="9999999999" maxlength="10">
      <input type="password" name="pass" required placeholder="Mật khẩu" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Xác nhập mật khẩu" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Đăng kí ngay" name="submit" class="btn">
      <p>Bạn đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
   </form>

</section>











<?php include 'components/footer.php'; ?>







<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>