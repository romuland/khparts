var tablesElement=new Class({Implements:[Options,Events],options:{conn:null},initialize:function(b,a){this.el=b;this.setOptions(a);this.updateMeEvent=this.updateMe.bindWithEvent(this);if(typeOf(document.id(this.options.conn))==="null"){this.periodical=this.getCnn.periodical(500,this)}else{this.setUp()}},cloned:function(){},getCnn:function(){if(typeOf(document.id(this.options.conn))==="null"){return}this.setUp();clearInterval(this.periodical)},setUp:function(){this.el=document.id(this.el);this.cnn=document.id(this.options.conn);this.loader=document.id(this.el.id+"_loader");this.cnn.addEvent("change",this.updateMeEvent);var a=this.cnn.get("value");if(a!==""&&a!==-1){this.updateMe()}},updateMe:function(c){if(c){c.stop()}if(this.loader){this.loader.show()}var d=this.cnn.get("value");var a="index.php";var b=new Request({url:a,data:{option:"com_fabrik",format:"raw",task:"plugin.pluginAjax",g:"element",plugin:"field",method:"ajax_tables",cid:d.toInt()},onComplete:function(f){var e=JSON.decode(f);if(typeOf(e)!=="null"){if(e.err){alert(e.err)}else{this.el.empty();e.each(function(g){var h={value:g};if(g===this.options.value){h.selected="selected"}if(this.loader){this.loader.hide()}new Element("option",h).set("text",g).inject(this.el)}.bind(this))}}}.bind(this),onFailure:function(e){this.el.empty();if(this.loader){this.loader.hide()}alert(e.status+": "+e.statusText)}.bind(this)}).send()}});