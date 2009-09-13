<div class="img">
	<div class="frame">
	<?php 
		$path = '/inc/img/event_icons/';
		if(($event->getIcon() != null && $event->getIcon() != '') && is_file($_SERVER['DOCUMENT_ROOT'] . $path . $event->getIcon())) {
		    $img = escape($event->getIcon());
        } else {
            $img = 'none.gif';
        }
        $title = escape($event->getTitle());
        $html = "<img src=\"{$path}{$img}\" alt=\"{$title}\" />";
        
        if(isset($showLink) && $showLink) {
            $html = "<a href=\"/event/view/{$event->getId()}\">" . $html . "</a>";
        }
        
        echo $html;
		?>
	</div>
</div>
