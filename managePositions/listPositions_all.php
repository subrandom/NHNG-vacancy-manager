<?php
session_start();
if (!$_SESSION['permission']['ManagePositions']) 
{
	require_once('../common/forbidden.html');
} 
else 
{
	require_once('../common/dbQuery.php');

    $query = "SELECT * FROM vwPositions";
    $result = readDB($query);
?>
    <table id ="positionlist" class="tablesorter">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th class="hidden"></th>
                <th>UIC</th>
                <th>PARA/<br>
                    LINE</th>
                <th>MOS</th>
                <th>GRD</th>
                <th>DESCRIPTION</th>
                <th>HELD BY<br>
                    ADMIN</th>
                <th>HELD BY<br>
                    RRNCO</th>
                <th>HELD<br>
                    FOR</th>
                <th>HELD<br>
                    SINCE</th>
                <th>WVR?</th>
                <th>NOTES</th>
                <th>ENL?</th>
            </tr>
        </thead>
        <tbody>
        <? while ($position = mysql_fetch_array($result, MYSQL_ASSOC)) { 
	        
	        //we're going to need to know 125 & 999L to add into notes, so...
	        $specialNote = "";	        
	        if($position['positionIsOverstrength'] == "y")
	        {
		        $specialNote = "[125%]";
	        }
	        if($position['positionIsPendingLoss'] == "y")
	        {
		        $specialNote = "[B9]";
	        }
        ?>
            <tr>
                <td><img id="reserve" alt="reserve" src="
                <?php //determine lock color
                if($position['positionIsLocked'] == "y"){
             		echo "../images/lock-red.png";
             	}   
                elseif ($position['positionIsHeld'] == "n"){
	                echo "../images/lock-green.png";
                }
                else{
	                echo "../images/lock.png";
                }
                										?>"/></td>
                <td><img id="delete" alt="delete" src = "../images/trash.png"></td>
                <td class="hidden"><? echo $position['positionID']; ?></td>
				<td <? echo ($position['unitOffLimits'] == "y" ? "class = 'callout-red'" : null) ?>><a href="#" class="tip"><? echo $position['positionUIC'];?>
				<span>
					<? 
					echo $position['unitDesignation'];
					echo "<br>";
					echo $position['unitLocation'];
					?>
				</span></a></td>
                <td <?echo ($position['positionIsInvalid'] == "y" ? "class = 'callout-red'" : null); ?>>
                <? echo $position['positionPara'] . "/" . $position['positionLine']; ?></td>
                <td><? echo $position['positionMOS']; ?></td>
                <td><? echo $position['positionGrade']; ?></td>
                <td><? echo $position['positionDescription']; ?></td>
                <td><? echo $position['userLastName']; ?></td>
                <td><? echo $position['recruiterLastName']; ?></td>
                <td><? echo $position['positionIsHeldForApplicant']; ?></td>
                <td><? echo ($position['positionHeldDate'] == "0000-00-00" ? "" : $position['positionHeldDate']); ?></td>
                <td id="waiver" class="yellow"><? echo ($position['positionIsHeld'] == "n" ? "" : strtoupper($position['positionIsHeldWaiver'])) ?></td>
                <td id="note" class="yellow"><? echo "$specialNote  ".$position['positionIsHeldNotes']; ?></td>
                <td id="enlisted" class="yellow"><? echo ($position['positionIsHeld'] == "n" ? "" : strtoupper($position['positionIsHeldEnlisted'])) ?></td>
            </tr>
<? } ?>
    </tbody>
</table>
<br>
<? } ?>
    <script type="text/javascript">
        $(document).ready(function(){
           $('#positionlist').tablesorter({
        	sortList: [[5,0], [3,0], [4,0]],
	        headers: {
        	    0: {sorter: false},
            	1: {sorter: false},
            	13: {sorter: false}
        	}
    		});
            $('td img[id=reserve]').click(function(event){
                event.preventDefault();
                if (event.shiftKey)
                {
	            	var str = "frmAdminLock.php?id=" + $(this).closest("tr").find("td.hidden").text();
                }
                else
                {
	                var str = "frmManagePositions.php?id=" + $(this).closest("tr").find("td.hidden").text();
                }     
                $('#toppanel').load(str);
            });
            $('td img[id=delete]').click(function(event){
                event.preventDefault();
                var str = "frmDeletePosition.php?id=" + $(this).closest("tr").find("td.hidden").text();
                $('#toppanel').load(str);
            });
            $('td img[id=reserve]').hover(function(){
                $(this).css('cursor','pointer');
            },function() {
                $(this).css('cursor','auto');
            });
            $('td img[id=delete]').hover(function(){
                $(this).css('cursor','pointer');
            },function() {
                $(this).css('cursor','auto');
            });
            $('td[id=note]').click(function(event){
            	event.preventDefault();
            	var str = "frmUpdateNote.php?id=" + $(this).closest("tr").find("td.hidden").text();
            	$('#toppanel').load(str);
            	});
            $('td[id=note]').hover(function(){
            	$(this).css('cursor','pointer');
            },function(){
            	$(this).css('cursor','auto');
            });
            $('td[id=waiver]').hover(function(){
	            $(this).css('cursor','pointer');
	            },
	            function() 
	            {
	            	$(this).css('cursor','auto');
				});
			$('td[id=waiver]').click(function(event){
				event.preventDefault();
				var str = "doChangeWaiverFlag.php?id=" + $(this).closest("tr").find("td.hidden").text();
				$('#toppanel').load(str);
				});
            $('td[id=enlisted]').hover(function(){
	            $(this).css('cursor','pointer');
	            },
	            function() 
	            {
	            	$(this).css('cursor','auto');
				});
			$('td[id=enlisted]').click(function(event){
				event.preventDefault();
				var str = "doChangeEnlistedFlag.php?id=" + $(this).closest("tr").find("td.hidden").text();
				$('#toppanel').load(str);
				});				
        });
</script>