joJwPlayer = function(ev){
	var href = $(this).attr('href');
	var acceptFileTypes = /(\.|\/)(mp3|mp4|wav|mpeg|aiff|wmv)$/i;
    if(acceptFileTypes.test(href)) {
		ev.preventDefault();
		var title = href.split('/').pop();
	    //Main Player Setup
	    $('body').append('<section class="joPlayer"><div class="joHead"><h1 class="playerTitle">'+title+'</h1><div class="closeJoPlayer">X</div></div><div class="joBorder joPlayerEmbed"><div id="player"><!--Progressive Download Fallback--><div class="joProgressiveFallback"><div class="joDownload"><a class="progressive" href="'+href+'"><div class="joLinkBox"><span class="icon-play"></span>Progressive download<span class="icon-download"></span></div></a></div></div><!--Fallback-End--></div></div></section>');
	    jwplayer('player').setup({
	        file: href,
	        androidhls: true,
	        height: '92%',
	        width: 'auto',
	        aspectratio: '16:9'    
	    });
    	$('section.joPlayer .closeJoPlayer').unbind('click',function(){$('section.joPlayer').remove()});
    	$('section.joPlayer .closeJoPlayer').bind('click',function(){$('section.joPlayer').remove()});
    }
}    
$(document).ready(function(){
    $('a.joKursListeDl').unbind('click',joJwPlayer);
    $('a.joKursListeDl').bind('click',joJwPlayer);
});