Fabrik.getWindow = function (opts) {
	if (Fabrik.Windows[opts.id]) {
		Fabrik.Windows[opts.id].open();
		Fabrik.Windows[opts.id].setOptions(opts);
		Fabrik.Windows[opts.id].loadContent();
	} else {
		var type = opts.type ? opts.type : '';
		switch (type) {
		case 'redirect':
			Fabrik.Windows[opts.id] = new Fabrik.RedirectWindow(opts);
			break;
		case 'modal':
			Fabrik.Windows[opts.id] = new Fabrik.Modal(opts);
			break;
		case '':
			/* falls through */
		default:
			Fabrik.Windows[opts.id] = new Fabrik.Window(opts);
			break;
		}
	}
	return Fabrik.Windows[opts.id];
};

Fabrik.Window = new Class({

	Implements: [Events, Options],
	
	options: {
		id: 'FabrikWindow',
		title: '&nbsp;',
		container: false,
		loadMethod: 'html',
		contentURL: '',
		createShowOverLay: false,
		width: 300,
		height: 300,
		loadHeight: 100,
		expandable: true,
		offset_x: null,
		offset_y: null,
		onContentLoaded: function () {
			this.fitToContent();
		}
	},
	
	modal: false,
	expanded: false,
	
	initialize: function (opts)
	{
		this.setOptions(opts);
		this.makeWindow();
	},
	
	makeWindow: function ()
	{
		var draggerC, dragger, expandButton;
		var handleParts = [];
		var d = {'width': this.options.width + 'px', 'height': this.options.height + 10 + 'px'};
		d.top = typeOf(this.options.offset_y) !== 'null' ? window.getScroll().y + this.options.offset_y : window.getSize().y / 2 + window.getScroll().y;
		d.left = typeOf(this.options.offset_x) !== 'null' ? window.getScroll().x + this.options.offset_x : window.getSize().x / 2  + window.getScroll().x - this.options.width / 2;
		this.window = new Element('div', {'id': this.options.id, 'class': 'fabrikWindow'}).setStyles(d);
		this.contentWrapperEl = this.window;
		var art = Fabrik.iconGen.create(icon.cross);
		
		var del = new Element('a', {'href': '#', 'class': 'close', 'events': {
			'click': this.close.bindWithEvent(this)
		}});
		art.inject(del);
		
		var hclass = 'handlelabel';
		if (!this.modal) {
			hclass += ' draggable';
			draggerC = new Element('div', {'class': 'bottomBar'});
			dragger = new Element('div', {'class': 'dragger'});
			var resizeIcon = Fabrik.iconGen.create(icon.resize, {
				scale: 0.8, 
				rotate: 0,
				shadow: {
					color: '#fff',
					translate: {x: 0, y: 1}
				},
				fill: {
					color: ['#999', '#666']
				}
			});
			resizeIcon.inject(dragger);
			draggerC.adopt(dragger);
		}
		var label = new Element('span', {'class': hclass}).set('text', this.options.title);
		handleParts.push(label);
		var expandIcon = Fabrik.iconGen.create(icon.expand, {scale: 0.4, fill: {
			color: ['#666666', '#999999']
		}});
		if (this.options.expandable && this.modal === false) {
			expandButton = new Element('a', {'href': '#', 'class': 'expand', 'events': {
				'click': this.expand.bindWithEvent(this)
			}}).adopt(expandIcon);
			handleParts.push(expandButton);
		}
		
		handleParts.push(del);
		this.handle = this.getHandle().adopt(handleParts);
		
		var bottomBarHeight = 15;
		var topBarHeight = 15;
		var contentHeight = this.options.height - bottomBarHeight - topBarHeight;
		if (contentHeight < this.options.loadHeight) {
			contentHeight = this.options.loadHeight;
		}
		this.contentWrapperEl = new Element('div.contentWrapper', {
			'styles': {'height': contentHeight + 'px'}
		});
		var itemContent = new Element('div', {'class': 'itemContent'});
		this.contentEl = new Element('div', {'class': 'itemContentPadder'});
		itemContent.adopt(this.contentEl);
		this.contentWrapperEl.adopt(itemContent);
		if (this.modal) {
			var ch = this.options.height - 30;
			cw = this.options.width;
			this.contentWrapperEl.setStyles({'height': ch + 'px', 'width': cw + 'px'});
			this.window.adopt([this.handle, this.contentWrapperEl]);
		} else {
			this.window.adopt([this.handle, this.contentWrapperEl, draggerC]);
			this.window.makeResizable({'handle': dragger,
				onDrag: function () {
					Fabrik.fireEvent('fabrik.window.resized', this.window);
					this.drawWindow();
				}.bind(this)
			});
			var dragOpts = {'handle': this.handle};
			dragOpts.onComplete = function () {
					Fabrik.fireEvent('fabrik.window.moved', this.window);
					this.drawWindow();
				}.bind(this);
			dragOpts.container = this.options.container ?	$(this.options.container) : null;
			this.window.makeDraggable(dragOpts);
		}

		$(document.body).adopt(this.window);
		this.loadContent();
		//bad idea - means opening windows are hidden if other code calls another window to hide
		/*Fabrik.addEvent('fabrik.overlay.hide', function () {
			this.window.hide();
		}.bind(this));*/
	},
	
	/**
	 * toggle the window full screen
	 */
	expand: function (e) {
		e.stop();
		if (!this.expanded) {
			this.expanded = true;
			var w = window.getSize();
			this.unexpanded = this.window.getCoordinates();
			this.window.setPosition({'x': 0, 'y': 0}).setStyles({'width': w.x, 'height': w.y});
		} else {
			this.window.setPosition({'x': this.unexpanded.left, 'y': this.unexpanded.top}).setStyles({'width': this.unexpanded.width, 'height': this.unexpanded.height});
			this.expanded = false;
		}
		this.drawWindow();
	},
	
	getHandle: function () {
		return new Element('div', {'class': 'handle draggable'});
	},
	
	loadContent: function () {
		var u;
		switch (this.options.loadMethod) {
		
		case 'html':
			if (typeOf(this.options.content) === 'null') {
				fconsole('no content option set for window.html');
				this.close();
				return;
			}
			if (typeOf(this.options.content) === 'element') {
				this.options.content.inject(this.contentEl.empty());
			} else {
				this.contentEl.set('html', this.options.content);
			}
			this.fireEvent('onContentLoaded', [this]);
			break;
		case 'xhr':
			u = this.window.getElement('.itemContent');
			Fabrik.loader.start(u);
			new Request.HTML({
				'url': this.options.contentURL,
				'data': {'fabrik_window_id': this.options.id},
				'update': u,
				onSuccess: function () {
					Fabrik.loader.stop(u);
					this.fireEvent('onContentLoaded', [this]);
				}.bind(this)
			}).post();
			break;
		case 'iframe':
			var h = this.options.height - 40;
			var w = this.contentEl.getScrollSize().x + 40 < window.getWidth() ? this.contentEl.getScrollSize().x + 40 : window.getWidth();
			u = this.window.getElement('.itemContent');
			Fabrik.loader.start(u);
			
			if (this.iframeEl) {
				this.iframeEl.dispose();
			}
			this.iframeEl = new Element('iframe', {
				'id': this.options.id + '_iframe',
				'name': this.options.id + '_iframe',
				'class': 'fabrikWindowIframe',
				'src': this.options.contentURL,
				'marginwidth': 0,
				'marginheight': 0,
				'frameBorder': 0,
				'scrolling': 'auto',
				'styles': {
					'height': h + 'px',
					'width': w
				}
			}).injectInside(this.window.getElement('.itemContent'));
			this.iframeEl.hide();
			this.iframeEl.addEvent('load', function (e) {
				Fabrik.loader.stop(this.window.getElement('.itemContent'));
				this.iframeEl.show();
				this.fireEvent('onContentLoaded', [this]);
			}.bind(this));
			break;
		}
	},
	
	drawWindow: function () {
		this.contentWrapperEl.setStyle('height', this.window.getDimensions().height - this.handle.getDimensions().height - 25);
		this.contentWrapperEl.setStyle('width', this.window.getDimensions().width - 2);
		// Resize iframe when window is resized
		if (this.options.loadMethod === 'iframe') {
			this.iframeEl.setStyle('height', this.contentWrapperEl.offsetHeight - 40);
			this.iframeEl.setStyle('width', this.contentWrapperEl.offsetWidth - 10);
		}
	},
	
	fitToContent: function () {
		if (!this.options.offset_y) {
			var myfx = new Fx.Scroll(window).toElement(this.window);
		}
		if (this.options.loadMethod !== 'iframe') {
			//as iframe content may not be on the same domain we CAN'T guarentee access to its body element to work out its dimensions
			var contentEl = this.window.getElement('.itemContent');
			var h = contentEl.getScrollSize().y < window.getHeight() ? contentEl.getScrollSize().y : window.getHeight();
			var w = contentEl.getScrollSize().x + 40 < window.getWidth() ? contentEl.getScrollSize().x + 40 : window.getWidth();
			this.window.setStyle('height', h);
			this.window.setStyle('width', w);
		}
		this.drawWindow();
	},
	
	center: function () {
		this.window.makeCenter();
	},
	
	close: function (e)
	{
		if (e) {
			e.stop();
		}
		//cant destroy as we want to be able to reuse them (see crop in fileupload element)
		this.window.fade('hide');
	},
	
	open: function (e) {
		if (e) {
			e.stop();
		}
		this.window.fade('show');
	}
	
});

Fabrik.Modal = new Class({
	Extends: Fabrik.Window,
	modal: true,
	
	getHandle: function () {
		return new Element('div', {'class': 'handle'});
	}
});

Fabrik.RedirectWindow = new Class({
	Extends: Fabrik.Window,
	initialize: function (opts) {
		var opts2 = {
			'id': 'redirect',
			'title': opts.title ? opts.title : '',
			loadMethod: loadMethod,
			'width': opts.width ? opts.width : 300,
			'height': opts.height ? opts.height : 320,
			'minimizable': false,
			'collapsible': true
			
		};
		opts2.id = 'redirect';
		opts = Object.merge(opts2, opts);
		var loadMethod;
		//if its a site page load via xhr otherwise load as iframe
		opts.loadMethod = 'xhr';
		if (!opts.contentURL.contains(Fabrik.liveSite) && (opts.contentURL.contains('http://') || opts.contentURL.contains('https://'))) {
			opts.loadMethod = 'iframe';
		} else {
			if (!opts.contentURL.contains('tmpl=component')) {
				opts.contentURL += opts.contentURL.contains('?') ? '&tmpl=component' : '?tmpl=component';
			}
		}
		this.setOptions(opts);
		this.makeWindow();
	}
});
