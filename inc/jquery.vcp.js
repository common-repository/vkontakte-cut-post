jQuery(document).ready(function($) {

  
  VK.Observer.subscribe('widgets.like.liked', function(num){

    vkcut = $.cookie('vkcut');
    if (!vkcut)
      $.cookie('vkcut', postId, 365*24*60*60); 
  }); 
  
  
  VK.Observer.subscribe('widgets.like.unliked', function(num){
  
    vkcut = $.cookie('vkcut');
    if (vkcut)
      $.removeCookie('vkcut');
      
  }); 
  
  $(".vcp_button[rel]").overlay({
    top: 160,
    closeOnClick: false,
    fixed: false,
    onBeforeClose: function() {
    },
    onLoad: function () {
      if (!$(".fb_inner iframe").length) {
        $(".fb_inner").append(VK.Widgets.Like("vcp_vk_like", {type: "full"}));
      }
    }
  });
  
  $('.vcp_reload').live("click", function () {
    window.location.reload();
  });
        
});

