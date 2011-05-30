<?php

require_once( "config.php" );
require_once( "security.php" );
require_once( "html_helpers.php" );
require_once( "statis.php" );
require_once( "filestuff.php" );
html_header();

html_style();
if(isset($_GET['action']) && $_GET['action']=="create")
{
  if(isset($_POST['project_name']))
  {
    $pName = $_POST['project_name'].'.git';
    if(is_dir($repo_directory.'/'.$pName))
    {
      die("A project with this name already exists");
    }
    else if(strlen($pName) == 0)
    {
      die("Empty project name!");
    }
// create the project
    exec("git init --bare ".$repo_directory.'/'.$pName);
    if(isset($_POST['description']) && strlen($_POST['description']) > 0)
    {
      exec("echo '".$_POST['description']."' > ".$repo_directory.'/'.$pName."/description");
    }
    //log creators ip and name
    $fh = fopen("repo.log", 'a');
    fwrite($fh,date("H:i:s-d-m-Y "));
    $user = "Unknown";
    if(isset($_SERVER['REMOTE_USER']))
    {
      $user = $_SERVER['REMOTE_USER'];
    }
    fwrite($fh, "User:".$user." IP:".$_SERVER['REMOTE_ADDR']." Action: Create repo:".$pName."\n");
    fclose($fh);
    echo "Created project ".$pName;
  }
  else
  {
?>
<div class="githead">GIT project creation</div>
<form action="manage.php?action=create" method="POST">
<table><TR><TD align="center">Project name </TD>
<TD><INPUT type="text" name="project_name">.git</TD></TR>
<TR><TD>Description:</TD>
<TD><textarea name="description"></textarea></TD></TR>
<TR><TD align="center" colspan="2"><INPUT type="submit" value="Create project"></TD></TR></table>
</form>

<?php
  }
html_footer();
}


?>