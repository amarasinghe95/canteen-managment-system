<?php

function emptyInputSignup($firstname, $lastname, $nic, $email, $mobile, $username, $pwd,  $repeatpwd){
    $result;

    if(empty($firstname) || empty($lastname) || empty($nic) || empty($email) || empty($mobile) || empty($username) || empty($pwd) || empty($repeatpwd)){
        $result = true;
    }
    else{
        $result = false;
    }
    return $result;
}


function invalidUserName($username){
    $result;
    if(!preg_match("/^[a-zA-Z0-9]*$/", $username)){
        $result = true;
    }
    else{
        $result = false;
    }
    return $result;
}   

function invalidEmail($email){
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $result = true;
    }
    else{
        $result = false;
    }
    return $result;
}

function passwordMatch($pwd, $repeatpwd){
    if($pwd !== $repeatpwd){
        $result = true;
    }
    else{
        $result = false;
    }
    return $result;
}

function usernameTaken($conn, $username, $mobile){
    $sql = "SELECT * FROM users WHERE username = ? OR phone_number = ?;";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: ../creat-account.php?error=stmtfailed");
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "si", $username, $mobile);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($resultData)){
        return $row;
    }
    else{
        $result = false;
        return $result;
    }
    mysqli_stmt_close($stmt);
}

function creatUser($conn, $firstname, $lastname, $nic, $email, $mobile, $username, $pwd){
    
    //Inserting into "users" table
    $sql = "INSERT INTO users (first_name, last_name, nic, email, phone_number, username, password) VALUES (?,?,?,?,?,?,?);"; 
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: ../creat-account.php?error=stmtfailed");
        exit();
    }

    $hashedpd = password_hash($pwd, PASSWORD_DEFAULT);
    
    mysqli_stmt_bind_param($stmt, "ssssiss", $firstname, $lastname, $nic, $email, $mobile, $username, $hashedpd);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    //Finish quary

   
    //Inserting into "owner" table
    $sql1 = "INSERT INTO owners (user_id) VALUES (?);";
    $stmt1 = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt1, $sql1)){
        header("location: ../creat-account.php?error=stmtfailed");
        exit();
    }

    $last_entry1 = mysqli_insert_id($conn);
    mysqli_stmt_bind_param($stmt1, "i", $last_entry1);
    mysqli_stmt_execute($stmt1);

    mysqli_stmt_close($stmt1);
    //Finish quary
   
    //Inserting into "wallet" table
    $sql2 = "INSERT INTO owner_wallet (user_id, owner_id) VALUES (?,?);";
    $stmt2 = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt2, $sql2)){
        header("location: ../creat-account.php?error=stmtfailed");
        exit();
    }

    $last_entry2 = mysqli_insert_id($conn);
    mysqli_stmt_bind_param($stmt2, "is", $last_entry1, $last_entry2);
    mysqli_stmt_execute($stmt2);

    mysqli_stmt_close($stmt2);
    //header("location: ../signup.php?error=noerror");

    //Finish quary

    //Updating "owner table
     
    $sql3 = "UPDATE owners SET owner_wallet_id = ? WHERE owner_id = ?;";
    $stmt3 = mysqli_stmt_init($conn);
 
    if(!mysqli_stmt_prepare($stmt3, $sql3)){
         header("location: ../creat-account.php?error=stmtfailed");
         exit();
    }
 
    $last_entry3 = mysqli_insert_id($conn);
    mysqli_stmt_bind_param($stmt3, "ii", $last_entry3, $last_entry2);
    mysqli_stmt_execute($stmt3);
 
    mysqli_stmt_close($stmt3);
    header("location: ../creat-account.php?error=noerror");
     //Finish quary
}

function emptyInputLogin($username, $pwd){
    $result;

    if(empty($username) || empty($pwd)){
        $result = true;
    }
    else{
        $result = false;
    }
    return $result;
}

function loginUser($conn, $username, $pwd){

    $usernameTaken = usernameTaken($conn, $username, $username);

    if($usernameTaken == false){
        header("location: ../login.php?error=wrongcredentials");
        exit();
    }

    $pwdHashed = $usernameTaken["password"];
    $pwdcheck = password_verify($pwd, $pwdHashed);

    if($pwdcheck == false){

        header("location: ../login.php?error=wrongpassword");
        exit();

    }
    else if($pwdcheck == true){

        session_start();
        $_SESSION["userid"] = $usernameTaken["user_id"];
        $_SESSION["username"] = $usernameTaken["username"];
        header("location: ../index.php");
        exit();

    }

}
function profileDetails($conn, $username){
    $sql = "SELECT * FROM users WHERE username = ?;";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: index.php?error=profileerror");
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($resultData)){
        return $row;
    }
    else{
        header("location: index.php?error=profileerror");
        exit();
    }
    mysqli_stmt_close($stmt);
}

function walletDetails($conn, $userid){
    $sql = "SELECT * FROM wallet WHERE user_id = ?;";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: index.php?error=walleterror");
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "i", $userid);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($resultData)){
        return $row;
    }
    else{
        header("location: index.php?error=profileerror");
        exit();
    }
    mysqli_stmt_close($stmt);
}

function emptyaddProducts($productName, $brandname, $price, $qty, $productdescription)
{
    $result = '';

    if (empty($productName) || empty($brandname) || empty($price) || empty($qty) || empty($productdescription)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}
//write function for add product
function creatproduct($conn, $productName, $brandname, $price, $qty, $productdescription)
{

    //Inserting into "product" table
    $sql = "INSERT INTO product (product_name, brand_name, price, qty, product_description) VALUES (?,?,?,?,?);";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../admin/add-product.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "sssis", $productName, $brandname, $price, $qty, $productdescription);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    //Finish quary


    //Inserting into "categories" table
    //$sql1 = "INSERT INTO categories  (product_id) VALUES (?);";
    //$stmt1 = mysqli_stmt_init($conn);

    //if (!mysqli_stmt_prepare($stmt1, $sql1)) {
    // header("location: ../add-product.php?error=stmtfailed");
    //exit();
    //}

    $last_entry1 = mysqli_insert_id($conn);
    //mysqli_stmt_bind_param($stmt1, "i", $last_entry1);
    //mysqli_stmt_execute($stmt1);

    //mysqli_stmt_close($stmt1);
    //Finish quary

    //Inserting into "brand" table
    $sql2 = "INSERT INTO brand (product_id, brand_name) VALUES (?,?);";
    $stmt2 = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt2, $sql2)) {
        header("location: ../add-product.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt2, "is", $last_entry1, $brandname);
    mysqli_stmt_execute($stmt2);

    mysqli_stmt_close($stmt2);
    //header("location: ../signup.php?error=noerror");

    //Finish quary


    // var_dump($_POST )

    //check whether the image is selected or not and set the value and image name accordingly
}
