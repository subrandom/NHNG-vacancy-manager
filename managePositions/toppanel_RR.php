<?php session_start();?>
<h4> Welcome back <? echo $_SESSION['userFullName'];?>!</h4>
<blockquote >
<ul>
<li>Fields highlighted in yellow can be modified by clicking on them.</li>
<li>Please mark reservations as ENLISTED when you know they are.</li>
<li>To modify the WAIVER field, you must contact OPS; this is to prevent abuse of the system.</li>
<li>You can cancel your own reservations by clicking on the yellow lock.</li>
</blockquote>