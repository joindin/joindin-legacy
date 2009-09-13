<div class="box">
	<h4><?= $title; ?></h4>
	<div class="ctn">
		<p>
    		Here's just a few of the other great presenters that spoke alongside 
    		<strong><?= $user->getName() ?></strong> at other conferences.
		</p>
    	<?php 
    	    if(count($user->getSessions()) === 0) {
    	        echo "This user has never given a session.";
    	    }
    	    else {
    	        echo "<ul>";
    	        foreach($speakers as $speaker) {
    	            echo "<li><a href=\"/speaker/view/{$speaker->getId()}\">{$speaker->getFullname()}</a>";
    	        }
    	        echo "</ul>";
    	    }
    	 ?>
    <h1>THIS NEEDS TO BE IMPLMENTED!</h1>
	</div>
</div>
