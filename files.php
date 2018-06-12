<?php
  require_once('pdo.php');
  session_start();
  $_SESSION["count"]=0;
  if(isset($_POST["submit"])){
    $_SESSION["search"]=1;
    $_SESSION["sfile"]=$_POST["search"];
    header('Location:files.php');
    return;
  }
  if(isset($_POST["cansearch"])){
    unset($_SESSION["search"]);
    header('Location:files.php');
    return;
  }
  if(!is_dir('uploads')) {
    mkdir('uploads');
  }
  if(isset($_POST["view"])){
    echo exec('uploads/'.$_POST["filename"]);
    header('Location:files.php');
    return;
  }
  if(isset($_POST["download"])){
    $filename = basename($_POST['filename']);
    $path = 'uploads/';
    $download_file =  $path.$filename;
    if(file_exists($download_file))
    {
      header('Content-Disposition: attachment; filename=' . $filename);
      readfile($download_file);
      exit;
    }
    else
    {
      echo 'File does not exists on given path';
    }
  }
  function dir_is_empty($dir) {
    $handle = opendir($dir);
    while (false !== ($entry = readdir($handle))) {
      if ($entry != "." && $entry != "..") {
        return FALSE;
      }
    }
    return TRUE;
  }

?>
<html>
  <head>
    <title>TT'18 | Files</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <style>
      li:hover{
        background-color: #eee;
      }
      .container{
        width:80%;
      }
      .down{
        margin-top:10%;
      }
      .tog{
        display: none;
      }
      @media(max-width:400px){
        .navbar-brand{
          display:none;
        }
      }
      @media(max-width:560px){
        .container,.container-fluid{
          width:100%;
        }
        .down{
          margin-top:23%;
        }
        th{
          display:none;
        }
        td{
          display: block;
        }
        .tog{
          display: block;
        }
      }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-expand-sm bg-light navbar-light fixed-top">
      <div class="container-fluid">
          <a class="navbar-brand" href="initial.php">Tech Tatva</a>
          <ul class="nav nav-tabs nav-justified" role="tablist">
            <li class="nav-item"><a class="nav-link" href="initial.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php">Register</a></li>
            <li class="nav-item"><a class="nav-link active" href="files.php">Files</a></li>
          </ul>
      </div>
    </nav>
    <div class="container">
    <form action="files.php" method="post" class="down form-inline">
    <?php
      if(!isset($_SESSION["search"])){
        echo '<div class="form-group"><input type="text" name="search" placeholder="Search using your Team Title" class="form-control"></div>&nbsp<input type="submit" value="Search" name="submit" class="btn btn-primary">';
      }
      else{
        echo "<input type='submit' value='Cancel Search' name='cansearch' class='btn btn-primary'>";
      }
    ?>
    </form>
    <?php

      $dir="uploads";
      $scan=scandir($dir,SCANDIR_SORT_NONE);
      if(!dir_is_empty($dir)&&!isset($_SESSION["search"])){
        echo "<table class='table table-hover'>";
        echo "<tr><th class='tog'>Files</th><th>File Name</th><th>Actions</th></tr>";
        foreach($scan as $key => $value){
          if($value!='.'&&$value!='..'){
            echo "<tr>";
            echo "<td>";
            echo $value;
            echo "</td>";
            echo "<td>";
            echo "<form action='files.php' method='post'>";
            echo "<input type='hidden' name='filename' value='".$value."'>";
            echo "<input type='submit' value='View' name='view' class='btn btn-primary'>";
            echo " ";
            echo "<input type='submit' value='Download' name='download' class='btn btn-primary'>";
            echo "</form>";
            echo "</td></tr>";
          }
        }
        echo "</table>";
      }
      else{
        if(dir_is_empty($dir))
          echo "Oops. There seem to be no uploaded files as of yet.";
        else{
          if(isset($_SESSION["search"])){
            unset($_SESSION["search"]);
            $sql="SELECT file_path,team_name FROM team_data WHERE team_name LIKE :p";
            $stmt=$pdo->prepare($sql);
            $stmt->execute(array(
              ':p'=>'%'.$_SESSION["sfile"].'%'
            ));
            while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
              $sfile=substr($row['file_path'],8);
              if($_SESSION["count"]==0){
                echo "<table class='table table-hover'>";
                echo "<tr><th class='tog'>Files</th><th>File Name</th><th>Team</th><th>Actions</th></tr>";
                $_SESSION["count"]=1;
              }
              echo "<tr>";
              echo "<td>";
              echo $sfile;
              echo "</td>";
              echo "<td>".$row['team_name']."</td>";
              echo "<td>";
              echo "<form action='files.php' method='post'>";
              echo "<input type='hidden' name='filename' value='".$sfile."'>";
              echo "<input type='submit' value='View' name='view' class='btn btn-primary'>";
              echo " ";
              echo "<input type='submit' value='Download' name='download' class='btn btn-primary'>";
              echo "</form>";
              echo "</td></tr>";
            }
          }
        }
      }

    ?>
    </div>
  </body>
</html>
