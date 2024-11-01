
function vcp_insert() {
  vcp_select = tinyMCE.activeEditor.selection.getContent();
  tinyMCE.activeEditor.selection.setContent('[vkcut]' + vcp_select + '[/vkcut]');
}

(function() {
    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('vcp_name');
  
    tinymce.create('tinymce.plugins.vcp_insert', {

        init : function(ed, url){
            ed.addButton('vcp_name', {
                title : 'vcp_name.vkcut',
                onclick : function() {
                    ed.execCommand(
                        'mceInsertContent',
                        false,
                        vcp_insert()
                    );
                },
                image: url + "/vkontakte-icon-bw.png"
            });
        },
    getInfo : function() {
      return {
        longname : 'vKontakte Cut Post Part Plugin',
        author : 'Mikhail Zorge',
        authorurl : 'http://zorge.biz/',
        infourl : 'http://zorge.biz/vk-cut/',
        version : "1.0"
      };
    }
  });

    tinymce.PluginManager.add('vcp_name', tinymce.plugins.vcp_insert);
  
})();