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
      error("A project with this name already exists");
    }
    else if(strlen($pName) == 0)
    {
      error("Empty project name!");
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
    info_text();
  }
  else
  {
?>
<div align="center">
<div class="githead">GIT project creation</div>
<form action="manage.php?action=create" method="POST">
<table><TR><TD align="center">Project name </TD>
<TD><INPUT type="text" name="project_name">.git</TD></TR>
<TR><TD>Description:</TD>
<TD><textarea cols="30" name="description"></textarea></TD></TR>
<TR><TD align="center" colspan="2"><INPUT type="submit" value="Create project"></TD></TR></table>
</form>
</div>

<?php
  }
html_footer();
}

function info_text()
{
  global $pName;
  global $server_name;
  global $repo_http_relpath;
  ?>
  <div align="center">
    <div class="githead">Created GIT project: <a href="git.php?p=<?php echo $pName?>"><?php echo $pName?></a></div>
    <div class="content">
      <p>So, what's next?<br />
      You can connect your local repository to the remote repository just created as follows:</p>

      <h2>Add the remote repository</h2>
      <p class="commandline">$&gt; git remote add origin http://<?php if(isset($_SERVER['REMOTE_USER'])){ echo $_SERVER['REMOTE_USER']; } else {echo "&lt;username&gt;";} ?>@<?php echo $server_name.$repo_http_relpath.$pName ?></p>

      <h2>Push the local master branch to the created remote repository</h2>
      <p class="commandline">$&gt; git push origin master</p>
    </div>
  </div>
<?php
  html_footer("<center><a href='git.php?p=".$pName."'>Go to the created repository</a></center>");
}

function error($text)
{
  ?>
  <div align="center">
  <div class="githead">Error</div>
  <table><tr><td><?php echo $text;?>
  </td></tr></table>
  </div>
  <?php
  html_footer();
  die();
}
?>