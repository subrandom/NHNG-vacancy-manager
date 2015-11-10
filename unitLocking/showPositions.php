<?php
session_start();

if (!$_SESSION['permission']['UnitLocking']) 
{
	require_once('../common/forbidden.html');
} 
else 
{
	require_once('../common/dbQuery.php');
	
	$myUIC = $_SESSION['userUIC'];
	
	//execute the query to retrieve unit positions

	$query = "SELECT * FROM positions WHERE positionUIC = '$myUIC' OR positionUIC IN (SELECT unitUIC FROM units WHERE unitParentUnit = '$myUIC' OR unitParentUnit IN (SELECT unitUIC FROM units WHERE unitParentUnit = '$myUIC'))";
	$result = readDB($query);
?>
    <br>
    <table id ="positionlist" class="tablesorter">
        <thead>
            <tr>
                <th></th>
                <th class="hidden"></th>
                <th>UIC</th>
                <th>PARA/<br>
                    LINE</th>
                <th>MOS</th>
                <th>GRD</th>
                <th>DESCRIPTION</th>
                <th>125%</th>
                <th>999L</th>
            </tr>
        </thead>
        <tbody>
        <? 
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
		?>
            <tr>
                <td>
             	<?php 
             	//determine what color lock to show
             	//red = locked by unit, yellow = held by recruiter, green = open
             	if($row['positionIsLocked'] == "y"){
             		?><img id="lock" alt="locked" src="../images/lock-red.png"> <?
             	}   
                elseif ($row['positionIsHeld']=="n"){
	            ?>    <img id="lock" alt="open" src="../images/lock-green.png"> <?
                }
                else{
				?>	<img id="lock" alt="reserved" src="../images/lock.png"> <?
                }                
                ?>
                </td>
                <td class="hidden"><? echo $row['positionID']; ?></td>
				<td class="uic"><? echo $row['positionUIC'];?></td>
                <td>
                <? echo $row['positionPara'] . "/" . $row['positionLine']; ?></td>
                <td><? echo $row['positionMOS']; ?></td>
                <td><? echo $row['positionGrade']; ?></td>
                <td><? echo $row['positionDescription']; ?></td>
                <td><? echo strtoupper($row['positionIsOverstrength']); ?></td>
                <td><? echo strtoupper($row['positionIsPendingLoss']); ?></td>
            </tr>
<?
		} ?>
    </tbody>
</table>
<br>
<? 
} ?>
    <script type="text/javascript">
        $(document).ready(function(){
           $('#positionlist').tablesorter({
        	sortList: [[2,0], [3,0]],
	        headers: {
        	    0: {sorter: false},
            	1: {sorter: false},
            	13: {sorter: false}
        	}
    		});
           $('td img[id=lock]').click(function(event){
            	event.preventDefault();
            	var str = "doLockUnlock.php?id=" + $(this).closest("tr").find("td.hidden").text();
            	$('#toppanel').load(str);
            	});
            $('td img[id=lock]').hover(function(){
                $(this).css('cursor','pointer');
                },function() {
                	$(this).css('cursor','auto');
            });
        });
</script>