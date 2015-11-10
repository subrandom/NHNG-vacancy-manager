<?php session_start();?>
<h4> Welcome back <? echo $_SESSION['userFullName'];?>!</h4>
<blockquote >
<ul>
<li>Fields highlighted in yellow can be modified by clicking on them.</li>
<li>Please mark reservations as ENLISTED when you know they are.</li>
<li>To modify the WAIVER field, you must contact OPS; this is to prevent abuse of the system.</li>
<li>You cannot see positions which are held outside of your team, contact OPS if you are looking for
	a position held by another team.</li>
</blockquote>