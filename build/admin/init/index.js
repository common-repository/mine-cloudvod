!function(){"use strict";window.wp.util,jQuery((function(n){n(".mcv-notice .notice-dismiss").on("click",(function(){let i=n(this).parent().data("id");wp.ajax.send("mcv_dismiss_notice",{data:{key:i,mcv_nonce:mcv_admin_init.nonce},success:function(n){return!0},error:function(n){return console.log(n),!1}})})),n(".mcv-notice .mcv-dismiss").on("click",(function(){n(this).parents(".mcv-notice").find(".notice-dismiss").trigger("click")})),n(".mcv-notice .mcv-initpages").on("click",(function(){let i=this;wp.ajax.send("mcv_order_init",{data:{nonce:mcv_admin_init.nonce},success:function(c){return n(i).parents(".mcv-notice").find(".notice-dismiss").trigger("click"),window.layer.msg(c.msg),!0},error:function(n){return console.log(n),!1}})}))}))}();