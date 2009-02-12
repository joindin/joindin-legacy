/*
 * Jonathan Howard
 *
 * jQuery Pause
 * version 0.2
 *
 * Requires: jQuery 1.0 (tested with svn as of 7/20/2006)
 *
 * Feel free to do whatever you'd like with this, just please give credit where
 * credit is do.
 *
 *
 *
 * pause() will hold everything in the queue for a given number of milliseconds,
 * or 1000 milliseconds if none is given.
 *
 *
 *
 * unpause() will clear the queue of everything of a given type, or 'fx' if no
 * type is given.
 */

$.fn.pause = function(milli,type) {
	milli = milli || 1000;
	type = type || "fx";
	return this.queue(type,function(){
		var self = this;
		setTimeout(function(){
			$(self).dequeue();
		},milli);
	});
};

$.fn.clearQueue = $.fn.unpause = function(type) {
	return this.each(function(){
		type = type || "fx";
		if(this.queue && this.queue[type]) {
			this.queue[type].length = 0;
		}
	});
};