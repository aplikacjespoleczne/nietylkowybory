/*
 * script.js
 * 
 */

function localizeSite( l)
{
  document.title = l(document.title);

  $('#imgLogo').attr('src',l($('#imgLogo').attr('src')));

  $('#navParticipation').html(l($('#navParticipation').html()));
  $('#navAbout').html(l($('#navAbout').html()));
  $('#navContact').html(l($('#navContact').html()));

  $('.da-slide > h2').html(l($('.da-slide > h2').html()));
  $('.da-slide > p.impact').html(l($('.da-slide > p.impact').html()));
  $('.da-slide > p span.teaser').html(l($('.da-slide > p span.teaser').html()));

  $('.container > h2').html(l($('.container > h2').html()));
  $('.container > p').html(l($('.container > p').html()));

//  $('.filter.all').html(l($('.filter.all').html()));
  $('.filter.participation').html(l($('.filter.participation').html()));
  $('.filter.easy').html(l($('.filter.easy').html()));
  $('.filter.doit').html(l($('.filter.doit').html()));
  $('.filter.chucknorris').html(l($('.filter.chucknorris').html()));
//  $('.filter.starters').html(l($('.filter.starters').html()));
//  $('.filter.urge').html(l($('.filter.urge').html()));
  $('.filter.initiative').html(l($('.filter.initiative').html()));
  $('.filter.support').html(l($('.filter.support').html()));
//  $('.filter.citypart').html(l($('.filter.citypart').html()));
//  $('.filter.city').html(l($('.filter.city').html()));
//  $('.filter.action').html(l($('.filter.action').html()));
//  $('.filter.collection').html(l($('.filter.collection').html()));
  $('.filter.contribute').html(l($('.filter.contribute').html()));
  $('.filter.passive').html(l($('.filter.passive').html()));
//  $('.filter.oneyear').html(l($('.filter.oneyear').html()));
//  $('.filter.someyears').html(l($('.filter.someyears').html()));
  $('.filter.adults').html(l($('.filter.adults').html()));
  $('.filter.youth').html(l($('.filter.youth').html()));
  $('.filter.formal').html(l($('.filter.formal').html()));
  $('.filter.informal').html(l($('.filter.informal').html()));
  $('.filter.alone').html(l($('.filter.alone').html()));
  $('.filter.withpeople').html(l($('.filter.withpeople').html()));
  $('.filter.withmasses').html(l($('.filter.withmasses').html()));
  $('.filter.other').html(l($('.filter.other').html()));
}

$(document).ready(function()
{
  $(".scroll").click(function(event){    
    event.preventDefault();
    $('html,body').animate({
      scrollTop:$(this.hash).offset().top
    },1200);
  });

  var defaults = {
    containerID: 'toTop', // fading element id
    containerHoverID: 'toTopHover', // fading element hover id
    scrollSpeed: 1200,
    easingType: 'linear' 
  };
  /*
  $().UItoTop({
    easingType: 'easeOutQuart'
  });
  */
/*
  $('.popup-with-zoom-anim').magnificPopup({
    type: 'inline',
    fixedContentPos: false,
    fixedBgPos: true,
    overflowY: 'auto',
    closeBtnInside: true,
    preloader: false,
    midClick: true,
    removalDelay: 300,
    mainClass: 'my-mfp-zoom-in'
  });
  */
  var l = function(string) {
    return string.toLocaleString();
  };
  localizeSite( l);
});

$(function ()
{
  var filterList = {
    init:function() {
      // MixItUp plugin
      // http://mixitup.io
      $('#portfoliolist').mixitup({
        targetSelector: '.portfolio',
        filterSelector: '.filter',
        effects: ['fade'],
        easing: 'snap',
        // call the hover effect
        onMixEnd: filterList.hoverEffect()
      });        
    },
    hoverEffect:function() {
      // Simple parallax effect
      $('#portfoliolist .portfolio').hover(
        function () {
          $(this).find('.label').stop().animate({bottom: 0}, 200, 'easeOutQuad');
          $(this).find('img').stop().animate({top: 0}, 500, 'easeOutQuad');        
        },
        function () {
          $(this).find('.label').stop().animate({bottom: 0}, 200, 'easeInQuad');
          $(this).find('img').stop().animate({top: 0}, 300, 'easeOutQuad');                
        }    
      );        
    }
  };
  // Run the show!
  filterList.init();

});  
