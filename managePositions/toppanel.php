<?php session_start();?>
<h4> Welcome back <? echo $_SESSION['userFullName'];?>!</h4>
<blockquote>
<ul>
<li>Fields highlighted in yellow can be modified by clicking on them.</li>
<li>Red UIC = unit is off limits.</li>
<li>Red PARA/LINE = position was marked invalid during last data import.</li>
</ul>
</blockquote>
