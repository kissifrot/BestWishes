<script type="text/javascript">
// <![CDATA[
	$(document).ready(function() {
		posTopMenu = $("#left_menu").offset();
		$("#left_menu").css("position", "absolute");
	});
	$(window).scroll(function() {
		var scrollTop = $(this).scrollTop();
		if(posTopMenu != null) {
			if(scrollTop <= posTopMenu.top)
				$('#left_menu').css('top', scrollTop + posTopMenu.top + "px");
			else
				$('#left_menu').css('top', scrollTop + "px");
		}
		else
			$('#left_menu').css('top', $(this).scrollTop() + "px");
	});
// ]]>
</script>