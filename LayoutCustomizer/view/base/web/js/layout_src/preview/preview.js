define([
    '../render/canvas2d',
    './config',
    './context',
    '../data/helper',
    '../ui/helper/html',
    './widgets/toolbar'
], function(Drawer, Config, Context, DataHelper, HtmlHelper, Toolbar) {

    class Preview {
        constructor(rootElement, config = {}) {
            this._rootElement = rootElement;
            this._rootWidgets = [];
            this.initContext(rootElement, config);
            this.initElements();
            this.initDrawer();
            this.initWidgets();
        }

        initContext(rootElement, config) {
            let ctx = new Context(rootElement, DataHelper.merged(Config, config));
            ctx.eventManager.subscribe(this, 'context');
            this._context = ctx;
        }

        initElements() {
            let ef = this._context.elementFactory;
            // Top
            let top = ef.make('div');
            HtmlHelper.addClassName(top, 'top');
            this._rootElement.appendChild(top);
            this._topElement = top;

            // Content
            let main = ef.make('div');
            HtmlHelper.addClassName(main, 'main');
            this._rootElement.appendChild(main);
            this._mainElement = main;

            // Canvas
            let canvas = ef.make('canvas', this._context.config.canvas);
            this._mainElement.appendChild(canvas);
            this._canvas = canvas;
        }

        initDrawer() {
            let drawer = new Drawer.Drawer(this._canvas),
                scale = this._context.config.scale,
                font = this._context.config.font;
            drawer.scale = scale;
            drawer.scaleMultiplier = this._context.config.scaleMultiplier;
            drawer.fontFamily = font ? (font.family || null) : null;
            drawer.fontSize = font ? (font.size || null) : null;
            drawer.editMode = true;
            this._context.scale = scale;
            drawer.backgroundColor = this._context.config.background;
            // drawer.setSmoothing(this._context.config.smoothing);
            drawer.reset();
            this._drawer = drawer;
        }

        initWidgets() {
            let ctx = this._context;

            // Toolbar
            let toolbar = new Toolbar.Widget(ctx);
            toolbar.addTools(this._context.config.toolbar);
            this._topElement.appendChild(toolbar.element);
            this._rootWidgets.push(toolbar);
        }

        onEvent(event) {
            if (event.type == 'context' && event.name == 'changed') {
                if (event.data.scale != undefined) {
                    this._drawer.scale = event.data.scale;
                    this.redraw();
                }
            }
        }

        importData(data) {
            // Create objects
            let objects = this._context.dataImport.createAll(data);

            // Add root objects
            let oh = this._context.objectHelper;
            objects.forEach(function(object) {
                this._context.rootObjectIds.add(object.objectId);
                // Init links
                oh.forEach(object, this._initLink.bind(this), 'link');
            }, this);

            // Update all linked objects
            this._context.linkManager.updateAllObjects();

            // Redraw
            this.redraw();
        }

        redraw() {
            let measurementManager = this._context.measurementManager;
            this._drawer.reset();
            this._context.rootObjectIds.toArray().forEach(function(objectId) {
                let object = this._context.objectManager.get(objectId);
                object.reset();
                object.prepare();
                object.place();
                measurementManager.collect(object);
                measurementManager.prepare(object);
                this._drawer.draw(object);
            }, this);
        }

        clear() {
            let rootObjectIds = this._context.rootObjectIds,
                om = this._context.objectManager,
                oh = this._context.objectHelper;
            rootObjectIds.toArray().forEach(function(objectId) {
                // destroy object tree
                let object = om.get(objectId);
                oh.forEach(object, function(child) {
                    om.destroy(child.objectId);
                    rootObjectIds.remove(child.objectId);
                });
            });
            this.redraw();
        }

        _initLink(link) {
            this._context.linkManager.updateLink(link);
        }

        get context() { return this._context; }
        get drawer() { return this._drawer; }
    }

    return Preview;
});
