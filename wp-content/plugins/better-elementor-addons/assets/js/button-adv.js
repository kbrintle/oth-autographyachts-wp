!(function ($) {
  function btnCircle(){
       /* ==  Button Animation  == */
      $( ".bea-adv-button" ).mouseenter(function(e) {
        var parentOffset = $(this).offset(); 
        var relX = e.pageX - parentOffset.left;
        var relY = e.pageY - parentOffset.top;
        $(this).find(".bea-adv-button-circle").css({"left": relX, "top": relY });
        $(this).find(".bea-adv-button-circle").removeClass("desplode-circle");
        $(this).find(".bea-adv-button-circle").addClass("explode-circle");
      });
      $( ".bea-adv-button" ).mouseleave(function(e) {
        var parentOffset = $(this).offset(); 
        var relX = e.pageX - parentOffset.left;
        var relY = e.pageY - parentOffset.top;
        $(this).find(".bea-adv-button-circle").css({"left": relX, "top": relY });
        $(this).find(".bea-adv-button-circle").removeClass("explode-circle");
        $(this).find(".bea-adv-button-circle").addClass("desplode-circle");
      });
  }
  
  function btnwithcursor(){
      // --- Bea-button 
    const $$ = (s, o = document) => o.querySelectorAll(s);

    $$('.bea-animated-btn-cursor').forEach(el => el.addEventListener('mousemove', function(e) {
      const pos = this.getBoundingClientRect();
      const mx = e.clientX - pos.left - pos.width/2; 
      const my = e.clientY - pos.top - pos.height/2;
      
      this.style.transform = 'translate('+ mx * 0.6 +'px, '+ my * 1.2 +'px)';
      this.style.transform += 'rotate3d('+ mx * -0.15 +', '+ my * -0.4 +', 0, 15deg)';
      // this.children[0].style.transform = 'translate('+ mx * 0.05 +'px, '+ my * 0.14 +'px)';
    }));

    $$('.bea-animated-btn-cursor').forEach(el => el.addEventListener('mouseleave', function() {
      this.style.transform = 'translate3d(0px, 0px, 0px)';
      this.style.transform += 'rotate3d(0, 0, 0, 0deg)';
      this.children[0].style.transform = 'translate3d(0px, 0px, 0px)';
    }));
  }
  
  jQuery(window).on('elementor/frontend/init', function () {

    elementorFrontend.hooks.addAction('frontend/element_ready/better-button-adv.default', btnCircle);
    elementorFrontend.hooks.addAction('frontend/element_ready/better-button-adv.default', btnwithcursor);

  });
})(jQuery)
